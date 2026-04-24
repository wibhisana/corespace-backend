<?php

namespace App\Modules\HRIS\Filament\Resources\OvertimeRules\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;

class OvertimeRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Aturan')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('calculation_method')
                    ->label('Kalkulasi')
                    ->badge()
                    ->color('info'),

                TextColumn::make('compensation_type')
                    ->label('Kompensasi')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Paid' ? 'success' : 'warning'),

                IconColumn::make('requires_approval')
                    ->label('Butuh Approval')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
