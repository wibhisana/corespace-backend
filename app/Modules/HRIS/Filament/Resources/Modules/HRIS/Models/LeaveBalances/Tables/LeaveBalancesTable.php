<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveBalances\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Tables\Grouping\Group;

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
            ])
            ->groups([
                Group::make('year')->label('Tahun'),
                Group::make('user.name')->label('Karyawan'),
            ])
            ->filters([
                // Tambahkan filter tahun di sini nanti
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
