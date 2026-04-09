<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\MeetingRooms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MeetingRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Ruangan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Ruangan')
                            ->placeholder('Misal: Ruang Jayakarta')
                            ->required(),

                        Select::make('location_id')
                            ->label('Lokasi / Lantai')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('capacity')
                            ->label('Kapasitas (Orang)')
                            ->numeric()
                            ->required(),

                        TagsInput::make('equipment')
                            ->label('Fasilitas / Equipment')
                            ->placeholder('Ketik fasilitas lalu tekan enter')
                            ->suggestions(['Proyektor', 'TV 65 Inch', 'Papan Tulis', 'Video Conference', 'Sound System'])
                            ->columnSpanFull(),

                        Toggle::make('requires_approval')
                            ->label('Butuh Persetujuan (Approval) Admin?')
                            ->helperText('Jika aktif, karyawan yang mem-booking butuh persetujuan dari HR/GA.'),

                        Toggle::make('is_active')
                            ->label('Ruangan Aktif / Bisa Digunakan')
                            ->default(true),
                    ]),
            ]);
    }
}
