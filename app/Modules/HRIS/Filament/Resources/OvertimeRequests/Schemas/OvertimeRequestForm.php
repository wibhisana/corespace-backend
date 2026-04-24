<?php

namespace App\Modules\HRIS\Filament\Resources\OvertimeRequests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class OvertimeRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pengajuan Lembur')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Karyawan')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('date')
                            ->label('Tanggal Lembur')
                            ->required(),

                        TimePicker::make('start_time')
                            ->label('Jam Mulai')
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('Jam Selesai')
                            ->required(),

                        TextInput::make('duration_minutes')
                            ->label('Durasi (Menit)')
                            ->numeric()
                            ->required()
                            ->helperText('Total waktu lembur dalam hitungan menit (Cth: 2 jam = 120).'),

                        Textarea::make('reason')
                            ->label('Alasan / Pekerjaan')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                // SECTION APPROVAL (KHUSUS HR / MANAGER)
                Section::make('Persetujuan (Approval)')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status Persetujuan')
                            ->options([
                                'Pending' => 'Menunggu (Pending)',
                                'Approved' => 'Disetujui (Approved)',
                                'Rejected' => 'Ditolak (Rejected)',
                            ])
                            ->default('Pending')
                            ->required()
                            ->live(),

                        Select::make('approved_by')
                            ->label('Disetujui / Ditolak Oleh')
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih nama Anda/Manager yang memproses.'),

                        Textarea::make('rejection_note')
                            ->label('Catatan Penolakan')
                            ->required(fn (Get $get) => $get('status') === 'Rejected')
                            ->hidden(fn (Get $get) => $get('status') !== 'Rejected')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
