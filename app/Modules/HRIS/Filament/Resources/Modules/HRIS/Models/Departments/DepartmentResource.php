<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments;

use App\Modules\HRIS\Models\Department;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\Pages\CreateDepartment;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\Pages\EditDepartment;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\Pages\ListDepartments;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\Schemas\DepartmentForm;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\Tables\DepartmentsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

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
