<?php

namespace App\Modules\HRIS\Filament\Resources\OvertimeRules\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OvertimeRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kebijakan & Aturan Lembur')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Aturan')
                            ->placeholder('Cth: Lembur Staf IT / Lembur Pabrik')
                            ->required()
                            ->maxLength(255),

                        Select::make('calculation_method')
                            ->label('Metode Kalkulasi Jam')
                            ->options([
                                'Manual' => 'Sesuai Pengajuan Karyawan',
                                'Attendance_Based' => 'Sesuai Waktu Absen (Clock-out)',
                            ])
                            ->default('Manual')
                            ->required(),

                        Select::make('compensation_type')
                            ->label('Tipe Kompensasi')
                            ->options([
                                'Paid' => 'Dibayar (Uang Lembur)',
                                'Time_Off' => 'Diganti Waktu Libur (Time-off in Lieu)',
                            ])
                            ->default('Paid')
                            ->required(),

                        Toggle::make('requires_approval')
                            ->label('Wajib Approval Manajer/HR')
                            ->default(true)
                            ->helperText('Jika aktif, lembur tidak akan sah sebelum disetujui.'),

                        Toggle::make('is_active')
                            ->label('Aturan Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
