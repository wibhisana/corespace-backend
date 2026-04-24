<?php

namespace App\Modules\HRIS\Filament\Resources\Payrolls\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PayrollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->columns(3)
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Karyawan')
                            ->required()
                            ->searchable(),

                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ])
                            ->required(),

                        TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->required(),
                    ]),

                Section::make('Rincian Komponen Gaji')
                    ->columns(2)
                    ->schema([
                        TextInput::make('basic_salary')
                            ->label('Gaji Pokok (Rp)')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateNet($set, $get)),

                        TextInput::make('net_salary')
                            ->label('Gaji Bersih / Take Home Pay (Rp)')
                            ->numeric()
                            ->disabled() // Dihitung otomatis
                            ->dehydrated()
                            ->required(),

                        // Komponen JSON untuk Tunjangan
                        Repeater::make('allowance_details')
                            ->label('Daftar Tunjangan & Lembur')
                            ->schema([
                                TextInput::make('name')->label('Nama Komponen')->required(),
                                TextInput::make('amount')->label('Nominal (Rp)')->numeric()->required(),
                            ])
                            ->columns(2)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateNet($set, $get)),

                        // Komponen JSON untuk Potongan
                        Repeater::make('deduction_details')
                            ->label('Daftar Potongan')
                            ->schema([
                                TextInput::make('name')->label('Nama Komponen')->required(),
                                TextInput::make('amount')->label('Nominal (Rp)')->numeric()->required(),
                            ])
                            ->columns(2)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateNet($set, $get)),
                    ]),

                Section::make('Status Pembayaran')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'Approved' => 'Approved',
                                'Paid' => 'Paid',
                            ])
                            ->default('Draft')
                            ->required(),

                        DatePicker::make('payment_date')
                            ->label('Tanggal Dibayarkan'),
                    ]),
            ]);
    }

    // Fungsi statis untuk menghitung otomatis Gaji Bersih saat form diubah
    public static function calculateNet(Set $set, Get $get)
    {
        $basic = (int) $get('basic_salary');

        $allowances = collect($get('allowance_details'))->sum('amount');
        $deductions = collect($get('deduction_details'))->sum('amount');

        $set('total_allowances', $allowances); // Optional: simpan nilai totalnya jika di-form ada hidden field
        $set('total_deductions', $deductions);

        $net = $basic + $allowances - $deductions;
        $set('net_salary', $net);
    }
}
