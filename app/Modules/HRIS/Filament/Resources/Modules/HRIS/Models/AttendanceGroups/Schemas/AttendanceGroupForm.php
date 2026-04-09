<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Grup')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Grup')
                            ->placeholder('Misal: Karyawan Pusat / Tim Gudang')
                            ->required()
                            ->maxLength(255),

                        Select::make('shift_id')
                            ->label('Aturan Sif Default')
                            ->relationship('shift', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Sif ini akan berlaku untuk semua karyawan di dalam grup ini.'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Grup Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
