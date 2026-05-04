<?php

namespace App\Modules\HRIS\Filament\Resources\LeaveBalances\Tables;

use App\Modules\HRIS\Models\LeaveBalance;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveBalancesTable
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
                    ->label('Jenis Cuti')
                    ->badge(),

                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('source')
                    ->label('Tipe Bucket')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        LeaveBalance::SOURCE_ANNUAL        => 'Annual',
                        LeaveBalance::SOURCE_CARRY_FORWARD => 'Carry-Forward',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        LeaveBalance::SOURCE_ANNUAL        => 'info',
                        LeaveBalance::SOURCE_CARRY_FORWARD => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('total_quota')
                    ->label('Jatah')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('used_quota')
                    ->label('Terpakai')
                    ->numeric()
                    ->color('danger')
                    ->alignCenter(),

                TextColumn::make('remaining_quota')
                    ->label('Sisa Saldo')
                    ->numeric()
                    ->color('success')
                    ->weight('bold')
                    ->alignCenter(),

                TextColumn::make('expires_at')
                    ->label('Kedaluwarsa')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : null)
                    ->sortable(),
            ])
            ->groups([
                Group::make('year')->label('Tahun'),
                Group::make('user.name')->label('Karyawan'),
                Group::make('source')->label('Tipe Bucket'),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label('Tipe Bucket')
                    ->options([
                        LeaveBalance::SOURCE_ANNUAL        => 'Annual',
                        LeaveBalance::SOURCE_CARRY_FORWARD => 'Carry-Forward',
                    ]),

                TernaryFilter::make('active_only')
                    ->label('Hanya Bucket Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif (belum expired)')
                    ->falseLabel('Sudah Expired')
                    ->queries(
                        true:  fn (Builder $q) => $q->where(function ($w) {
                            $w->whereNull('expires_at')->orWhere('expires_at', '>=', today());
                        }),
                        false: fn (Builder $q) => $q->whereNotNull('expires_at')->where('expires_at', '<', today()),
                        blank: fn (Builder $q) => $q,
                    ),
            ])
            ->defaultSort('expires_at', 'asc')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
