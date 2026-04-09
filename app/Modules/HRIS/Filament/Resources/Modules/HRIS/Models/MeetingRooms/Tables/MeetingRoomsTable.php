<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\MeetingRooms\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class MeetingRoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Ruangan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->suffix(' Orang')
                    ->sortable(),

                IconColumn::make('requires_approval')
                    ->label('Butuh Approval')
                    ->boolean(),

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
