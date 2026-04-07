<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AttendanceForm
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
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required(),
                TimePicker::make('clock_in')
                    ->label('Jam Masuk'),
                TimePicker::make('clock_out')
                    ->label('Jam Pulang'),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }
}
