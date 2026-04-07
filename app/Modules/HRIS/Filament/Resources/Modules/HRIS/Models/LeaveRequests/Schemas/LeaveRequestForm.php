<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Karyawan')
                    ->required()
                    ->searchable(),

                Select::make('leave_type')
                    ->label('Tipe Cuti')
                    ->options([
                        'Cuti Tahunan' => 'Cuti Tahunan',
                        'Cuti Sakit' => 'Cuti Sakit',
                        'Cuti Melahirkan' => 'Cuti Melahirkan',
                        'Izin' => 'Izin',
                    ])
                    ->required(),

                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required(),

                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required(),

                Textarea::make('reason')
                    ->label('Alasan')
                    ->required()
                    ->columnSpanFull(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
