<?php

namespace App\Modules\HRIS\Filament\Resources\LeaveBalances;

use App\Modules\HRIS\Models\LeaveBalance;
use App\Modules\HRIS\Filament\Resources\LeaveBalances\Pages\CreateLeaveBalance;
use App\Modules\HRIS\Filament\Resources\LeaveBalances\Pages\EditLeaveBalance;
use App\Modules\HRIS\Filament\Resources\LeaveBalances\Pages\ListLeaveBalances;
use App\Modules\HRIS\Filament\Resources\LeaveBalances\Schemas\LeaveBalanceForm;
use App\Modules\HRIS\Filament\Resources\LeaveBalances\Tables\LeaveBalancesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LeaveBalanceResource extends Resource
{
    protected static ?string $model = LeaveBalance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Saldo Cuti Karyawan';
    protected static ?string $modelLabel = 'Saldo Cuti';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Cuti';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return LeaveBalanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveBalancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaveBalances::route('/'),
            'create' => CreateLeaveBalance::route('/create'),
            'edit' => EditLeaveBalance::route('/{record}/edit'),
        ];
    }
}
