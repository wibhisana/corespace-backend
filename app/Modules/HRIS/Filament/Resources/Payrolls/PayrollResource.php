<?php

namespace App\Modules\HRIS\Filament\Resources\Payrolls;

use App\Modules\HRIS\Models\Payroll;
use App\Modules\HRIS\Filament\Resources\Payrolls\Pages\CreatePayroll;
use App\Modules\HRIS\Filament\Resources\Payrolls\Pages\EditPayroll;
use App\Modules\HRIS\Filament\Resources\Payrolls\Pages\ListPayrolls;
use App\Modules\HRIS\Filament\Resources\Payrolls\Schemas\PayrollForm;
use App\Modules\HRIS\Filament\Resources\Payrolls\Tables\PayrollsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Penggajian / Payroll';
    protected static ?string $modelLabel = 'Data Gaji';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Finansial';

    public static function form(Schema $schema): Schema
    {
        return PayrollForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayrollsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['HR Manager', 'Super Admin']) ?? true;
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
