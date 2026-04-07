<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls;

use App\Modules\HRIS\Models\Payroll;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Pages\CreatePayroll;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Pages\EditPayroll;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Pages\ListPayrolls;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Schemas\PayrollForm;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Tables\PayrollsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PayrollForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayrollsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canViewAny(): bool
    {
        // Hanya HR dan Admin yang bisa melihat menu Payroll di sidebar
        return auth()->user()->hasAnyRole(['HR Manager', 'Super Admin']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrolls::route('/'),
            'create' => CreatePayroll::route('/create'),
            'edit' => EditPayroll::route('/{record}/edit'),
        ];
    }
}
