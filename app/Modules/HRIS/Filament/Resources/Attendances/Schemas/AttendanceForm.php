<?php

namespace App\Modules\HRIS\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Karyawan')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('shift_id')
                            ->relationship('shift', 'name')
                            ->label('Jadwal Sif (Hari ini)')
                            ->searchable()
                            ->preload()
                            ->helperText('Sif apa yang berlaku untuk karyawan ini hari ini?'),

                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),

                        Select::make('status')
                            ->label('Status Kehadiran')
                            ->options([
                                'Present' => 'Hadir Tepat Waktu',
                                'Late' => 'Hadir (Terlambat)',
                                'Absent' => 'Tidak Hadir / Bolos',
                                'On_Leave' => 'Sedang Cuti',
                                'Day_Off' => 'Hari Libur',
                            ])
                            ->default('Absent')
                            ->required(),
                    ]),

                Section::make('Catatan Waktu (Clock-in / out)')
                    ->columns(2)
                    ->schema([
                        // Menggunakan DateTimePicker karena Sif bisa melintasi tengah malam
                        DateTimePicker::make('clock_in')
                            ->label('Waktu Masuk'),

                        DateTimePicker::make('clock_out')
                            ->label('Waktu Pulang'),

                        TextInput::make('lateness_minutes')
                            ->label('Terlambat (Menit)')
                            ->numeric()
                            ->default(0),

                        TextInput::make('early_out_minutes')
                            ->label('Pulang Cepat (Menit)')
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Log Lokasi & Catatan')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('clock_in_location')
                            ->label('Koordinat / Lokasi Masuk'),

                        TextInput::make('clock_out_location')
                            ->label('Koordinat / Lokasi Pulang'),

                        Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
