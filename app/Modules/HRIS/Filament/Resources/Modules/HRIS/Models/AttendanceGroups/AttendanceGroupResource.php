<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups;

use App\Modules\HRIS\Models\AttendanceGroup;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\Pages\CreateAttendanceGroup;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\Pages\EditAttendanceGroup;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\Pages\ListAttendanceGroups;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\Schemas\AttendanceGroupForm;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\Tables\AttendanceGroupsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AttendanceGroupResource extends Resource
{
    protected static ?string $model = AttendanceGroup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Grup Kehadiran';
    protected static ?string $modelLabel = 'Grup Kehadiran';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Kehadiran';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AttendanceGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceGroupsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceGroups::route('/'),
            'create' => CreateAttendanceGroup::route('/create'),
            'edit' => EditAttendanceGroup::route('/{record}/edit'),
        ];
    }
}
