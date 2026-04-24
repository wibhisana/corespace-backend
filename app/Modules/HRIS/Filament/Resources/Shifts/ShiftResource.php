<?php

namespace App\Modules\HRIS\Filament\Resources\Shifts;

use App\Modules\HRIS\Models\Shift;
use App\Modules\HRIS\Filament\Resources\Shifts\Pages\CreateShift;
use App\Modules\HRIS\Filament\Resources\Shifts\Pages\EditShift;
use App\Modules\HRIS\Filament\Resources\Shifts\Pages\ListShifts;
use App\Modules\HRIS\Filament\Resources\Shifts\Schemas\ShiftForm;
use App\Modules\HRIS\Filament\Resources\Shifts\Tables\ShiftsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Sif Kerja';
    protected static ?string $modelLabel = 'Sif Kerja';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Kehadiran';
    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return ShiftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShiftsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShifts::route('/'),
            'create' => CreateShift::route('/create'),
            'edit' => EditShift::route('/{record}/edit'),
        ];
    }
}
