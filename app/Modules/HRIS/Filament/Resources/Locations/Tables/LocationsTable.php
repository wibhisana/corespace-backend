<?php

namespace App\Modules\HRIS\Filament\Resources\Locations\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lokasi / Lantai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('parent.name')
                    ->label('Berada di')
                    ->searchable()
                    ->placeholder('- Lokasi Utama -'),

                TextColumn::make('meeting_rooms_count')
                    ->label('Jumlah Ruangan')
                    ->counts('meetingRooms') // Otomatis menghitung jumlah ruang rapat di lokasi ini
                    ->badge()
                    ->color('info'),
            ])
            // 💡 FITUR HIERARKI (GROUPING)
            ->groups([
                Group::make('parent.name')
                    ->label('Gedung Induk')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn ($record) => $record->parent ? '📍 ' . $record->parent->name : '🏢 Lokasi Utama (Gedung/Kawasan)')
            ])
            ->defaultGroup('parent.name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
