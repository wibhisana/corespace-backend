<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveTypes\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Cuti')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('default_quota')
                    ->label('Kuota Default')
                    ->suffix(' Hari')
                    ->sortable(),

                IconColumn::make('is_unlimited')
                    ->label('Tak Terbatas')
                    ->boolean(),

                IconColumn::make('requires_attachment')
                    ->label('Wajib Lampiran')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
