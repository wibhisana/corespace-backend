<?php

namespace App\Modules\HRIS\Filament\Resources\LeaveBalances\Schemas;

use App\Modules\HRIS\Models\LeaveBalance;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaveBalanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Saldo Cuti Karyawan')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Karyawan')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('leave_type_id')
                            ->label('Jenis Cuti')
                            ->relationship('leaveType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('year')
                            ->label('Tahun (year)')
                            ->helperText('Tahun asal kuota. Untuk carry_forward, isi dengan tahun sumber (mis. sisa 2025 → year=2025).')
                            ->numeric()
                            ->default(now()->year)
                            ->required(),

                        Select::make('source')
                            ->label('Tipe Bucket')
                            ->options([
                                LeaveBalance::SOURCE_ANNUAL        => 'Annual (jatah reguler)',
                                LeaveBalance::SOURCE_CARRY_FORWARD => 'Carry-Forward (sisa tahun lalu)',
                            ])
                            ->default(LeaveBalance::SOURCE_ANNUAL)
                            ->required()
                            ->helperText('Carry-Forward expired biasanya 31 Maret. Annual expired 31 Desember.'),

                        TextInput::make('total_quota')
                            ->label('Total Jatah (Hari)')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('used_quota')
                            ->label('Terpakai (Hari)')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        DatePicker::make('expires_at')
                            ->label('Berlaku Sampai (Expired)')
                            ->helperText('Bucket otomatis disembunyikan dari pemakaian setelah tanggal ini. Kosongkan jika tidak pernah hangus.')
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
