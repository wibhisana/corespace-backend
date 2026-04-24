<?php

namespace App\Modules\HRIS\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Struktur Departemen')
                    ->description('Atur hierarki dan detail departemen di sini.')
                    ->columns(2) // Membagi form menjadi 2 kolom
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Departemen')
                            ->placeholder('Misal: HR & Admin')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('Kode Departemen')
                            ->placeholder('Misal: HRD-001')
                            ->required()
                            ->maxLength(255),

                        Select::make('unit_id')
                            ->label('Unit Bisnis')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Unit bisnis tempat departemen ini berada.'),

                        // INI KUNCI UNTUK HIERARKI (TREE)
                        Select::make('parent_id')
                            ->label('Induk Departemen (Parent)')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Kosongkan jika ini adalah departemen utama/tertinggi.')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
