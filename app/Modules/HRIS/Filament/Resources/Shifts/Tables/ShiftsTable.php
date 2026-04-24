<?php

namespace App\Modules\HRIS\Filament\Resources\Shifts\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class ShiftsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Sif')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Fixed' => 'success',
                        'Scheduled' => 'warning',
                        'Free' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('start_time')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->placeholder('-'),

                TextColumn::make('end_time')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->placeholder('-'),

                TextColumn::make('grace_period')
                    ->label('Toleransi')
                    ->suffix(' Menit'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
