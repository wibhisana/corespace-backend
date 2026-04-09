<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class AttendanceGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Grup')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('shift.name')
                    ->label('Aturan Sif')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('users_count')
                    ->label('Jml Karyawan')
                    ->counts('users') // Menghitung otomatis karyawan di grup ini
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
