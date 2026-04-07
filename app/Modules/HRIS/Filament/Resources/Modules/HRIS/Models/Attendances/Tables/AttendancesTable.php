<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name') // <-- Pemanggilan diperbaiki
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('clock_in')
                    ->label('Masuk')
                    ->time(),
                TextColumn::make('clock_out')
                    ->label('Pulang')
                    ->time(),
                TextColumn::make('notes')
                    ->limit(20),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
