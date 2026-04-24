<?php

namespace App\Modules\HRIS\Filament\Resources\LeaveTypes;

use App\Modules\HRIS\Models\LeaveType;
use App\Modules\HRIS\Filament\Resources\LeaveTypes\Pages\CreateLeaveType;
use App\Modules\HRIS\Filament\Resources\LeaveTypes\Pages\EditLeaveType;
use App\Modules\HRIS\Filament\Resources\LeaveTypes\Pages\ListLeaveTypes;
use App\Modules\HRIS\Filament\Resources\LeaveTypes\Schemas\LeaveTypeForm;
use App\Modules\HRIS\Filament\Resources\LeaveTypes\Tables\LeaveTypesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Jenis Cuti';
    protected static ?string $modelLabel = 'Jenis Cuti';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Cuti';
    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return LeaveTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaveTypes::route('/'),
            'create' => CreateLeaveType::route('/create'),
            'edit' => EditLeaveType::route('/{record}/edit'),
        ];
    }
}
