<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Unit / Anak Perusahaan')
                    ->description('Kelola detail anak perusahaan dan hubungkan departemen yang berada di bawahnya.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Unit')
                            ->placeholder('Misal: KPN Plantation')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Tipe Unit')
                            ->options([
                                'Holding' => 'Holding / Pusat',
                                'Subsidiary' => 'Anak Perusahaan',
                                'Branch' => 'Kantor Cabang',
                            ])
                            ->required()
                            ->disabled(fn (string $context): bool => $context === 'edit')
                            ->dehydrated()
                            ->helperText(fn (string $context): ?string =>
                                $context === 'edit' ? 'Tipe Unit tidak dapat diubah setelah dibuat.' : null
                            ),

                    ]),

                Section::make('Geofencing (Lokasi Absensi)')
                    ->description('Koordinat GPS kantor untuk validasi Clock-in karyawan via mobile.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->placeholder('-6.2088')
                            ->helperText('Contoh: -6.2088 (Jakarta)'),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->placeholder('106.8456')
                            ->helperText('Contoh: 106.8456 (Jakarta)'),

                        TextInput::make('radius_meters')
                            ->label('Radius (Meter)')
                            ->numeric()
                            ->default(50)
                            ->helperText('Jarak maksimal karyawan boleh clock-in dari titik koordinat.'),
                    ]),

                Section::make('Departemen Terkait')
                    ->schema([
                        Select::make('departments')
                            ->label('Departemen Terkait')
                            ->relationship('departments', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih departemen yang menjadi bagian dari unit ini.'),
                    ]),
            ]);
    }
}
