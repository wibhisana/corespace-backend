<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances;

use App\Modules\HRIS\Models\Attendance;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\Pages\CreateAttendance;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\Pages\EditAttendance;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\Pages\ListAttendances;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\Schemas\AttendanceForm;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\Tables\AttendancesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'date';

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['Super Admin', 'HR Manager']) ?? false;
    }
}
