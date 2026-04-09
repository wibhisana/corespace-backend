<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Shifts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pengaturan Sif Kerja')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Sif')
                            ->placeholder('Misal: Sif Pagi / Sif Satpam Malam')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Tipe Sif')
                            ->options([
                                'Fixed' => 'Sif Tetap (Fixed Shift)',
                                'Scheduled' => 'Sif Terjadwal (Scheduled Shift)',
                                'Free' => 'Sif Bebas (Free Shift)',
                            ])
                            ->required()
                            ->default('Fixed')
                            ->live(), // Penting agar form reaktif saat tipe diubah

                        // Jam masuk & pulang disembunyikan jika Tipe Sif adalah 'Free'
                        TimePicker::make('start_time')
                            ->label('Jam Masuk')
                            ->required(fn (Get $get) => $get('type') !== 'Free')
                            ->hidden(fn (Get $get) => $get('type') === 'Free'),

                        TimePicker::make('end_time')
                            ->label('Jam Pulang')
                            ->required(fn (Get $get) => $get('type') !== 'Free')
                            ->hidden(fn (Get $get) => $get('type') === 'Free'),

                        TextInput::make('grace_period')
                            ->label('Toleransi Terlambat (Menit)')
                            ->numeric()
                            ->default(15)
                            ->required()
                            ->helperText('Berapa menit karyawan diizinkan terlambat tanpa dikenakan sanksi/potongan.'),
                    ]),
            ]);
    }
}
