<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hierarki Lokasi')
                    ->description('Atur nama gedung atau lantai ruang rapat.')
                    ->columns(1) // 1 kolom saja agar rapi
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lokasi')
                            ->placeholder('Misal: Gedung A / Lantai 1')
                            ->required()
                            ->maxLength(255),

                        // Kunci Hierarki: Bisa diisi kosong jika ini adalah Gedung Utama
                        Select::make('parent_id')
                            ->label('Bagian Dari (Induk Lokasi)')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Kosongkan jika ini adalah Gedung/Lokasi Utama. Pilih gedung jika ini adalah Lantai/Ruang Spesifik.'),
                    ]),
            ]);
    }
}
