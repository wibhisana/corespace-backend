<?php

namespace App\Modules\HRIS\Filament\Resources\LeaveRequests\Tables;

use App\Modules\HRIS\Exceptions\InsufficientLeaveBalanceException;
use App\Modules\HRIS\Models\LeaveRequest;
use App\Modules\HRIS\Models\LeaveType;
use App\Modules\HRIS\Services\LeaveBalanceService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('leaveType.name')
                    ->label('Tipe Cuti')
                    ->badge(),
                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_days')
                    ->label('Hari')
                    ->alignCenter(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
                TextColumn::make('approver.name')
                    ->label('Diproses Oleh')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record) {
                        $service = app(LeaveBalanceService::class);
                        $leaveType = LeaveType::find($record->leave_type_id);
                        $shouldDeduct = $leaveType && ! $leaveType->is_unlimited;

                        try {
                            DB::transaction(function () use ($record, $service, $shouldDeduct) {
                                if ($shouldDeduct) {
                                    // FIFO: bucket dengan expires_at terdekat
                                    // (carry_forward) dilumat duluan, baru annual.
                                    $service->deduct(
                                        userId:      (int) $record->user_id,
                                        leaveTypeId: (int) $record->leave_type_id,
                                        days:        (int) $record->total_days,
                                    );
                                }

                                $record->update([
                                    'status'         => 'approved',
                                    'approved_by'    => auth()->id(),
                                    'approved_at'    => Carbon::now(),
                                    'rejection_note' => null,
                                ]);
                            });

                            Notification::make()
                                ->title('Pengajuan cuti disetujui.')
                                ->success()
                                ->send();
                        } catch (InsufficientLeaveBalanceException $e) {
                            Notification::make()
                                ->title('Gagal approve')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (LeaveRequest $record): bool =>
                        $record->status === 'pending'
                        && auth()->user()?->can('approve', $record)
                    ),

                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->modalHeading('Tolak Pengajuan Cuti')
                    ->modalDescription('Berikan alasan penolakan agar tercatat di audit trail.')
                    ->schema([
                        Textarea::make('reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (LeaveRequest $record, array $data) {
                        $record->update([
                            'status'         => 'rejected',
                            'approved_by'    => auth()->id(),
                            'approved_at'    => Carbon::now(),
                            'rejection_note' => $data['reason'],
                        ]);

                        Notification::make()
                            ->title('Pengajuan cuti ditolak.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (LeaveRequest $record): bool =>
                        $record->status === 'pending'
                        && auth()->user()?->can('reject', $record)
                    ),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
