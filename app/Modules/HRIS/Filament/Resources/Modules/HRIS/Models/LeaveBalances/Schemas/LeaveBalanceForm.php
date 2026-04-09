<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveBalances\Schemas;

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
                            ->label('Tahun')
                            ->numeric()
                            ->default(now()->year)
                            ->required(),

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

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
