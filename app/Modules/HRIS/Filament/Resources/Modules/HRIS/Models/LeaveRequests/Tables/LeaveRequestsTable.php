<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\Tables;

use App\Modules\HRIS\Models\LeaveRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                TextColumn::make('leave_type')
                    ->label('Tipe Cuti')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // Tombol Approve
                // Tombol Approve di LeaveRequestsTable.php
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record) {
                        // 1. Hitung durasi cuti
                        $start = \Carbon\Carbon::parse($record->start_date);
                        $end = \Carbon\Carbon::parse($record->end_date);
                        $daysNeeded = $start->diffInDays($end) + 1;

                        $user = $record->user;

                        // 2. Ambil semua ember kuota yang MASIH AKTIF (belum expired) dan MASIH ADA SISA,
                        // urutkan dari tanggal expired paling dekat (habiskan sisa tahun lalu dulu)
                        $activeQuotas = $user->leaveQuotas()
                            ->where('expires_at', '>=', now()) // Belum lewat bulan Maret
                            ->whereRaw('quota > used')         // Masih ada sisa
                            ->orderBy('expires_at', 'asc')
                            ->get();

                        // 3. Logika Pemotongan FIFO (First In First Out)
                        foreach ($activeQuotas as $bucket) {
                            if ($daysNeeded <= 0) break; // Jika sudah terbayar semua, berhenti

                            $availableInBucket = $bucket->quota - $bucket->used;

                            if ($availableInBucket >= $daysNeeded) {
                                // Jika ember ini cukup untuk sisa cuti
                                $bucket->increment('used', $daysNeeded);
                                $daysNeeded = 0;
                            } else {
                                // Jika ember ini tidak cukup, kurangi semua sisanya, dan lanjut ke ember tahun berikutnya
                                $bucket->increment('used', $availableInBucket);
                                $daysNeeded -= $availableInBucket;
                            }
                        }

                        // 4. Update status cuti jadi approved
                        $record->update(['status' => 'approved']);
                    })
                    ->visible(fn (LeaveRequest $record): bool =>
                        $record->status === 'pending'
                        && auth()->user()?->can('approve', $record)
                    ),

                // Tombol Reject
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pengajuan Cuti')
                    ->modalDescription('Anda yakin menolak pengajuan cuti ini? Tindakan ini tidak bisa dibatalkan.')
                    ->action(fn (LeaveRequest $record) => $record->update(['status' => 'rejected']))
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
