<?php

namespace App\Modules\HRIS\Filament\Resources\Departments;

use App\Modules\HRIS\Models\Department;
use App\Modules\HRIS\Filament\Resources\Departments\Pages\CreateDepartment;
use App\Modules\HRIS\Filament\Resources\Departments\Pages\EditDepartment;
use App\Modules\HRIS\Filament\Resources\Departments\Pages\ListDepartments;
use App\Modules\HRIS\Filament\Resources\Departments\Schemas\DepartmentForm;
use App\Modules\HRIS\Filament\Resources\Departments\Tables\DepartmentsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string | UnitEnum | null $navigationGroup = 'Organisasi';

    protected static ?string $navigationLabel = 'Departemen';

    protected static ?string $modelLabel = 'Departemen';

    protected static ?string $pluralModelLabel = 'Departemen';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
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
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }
}
