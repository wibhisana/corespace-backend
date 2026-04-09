<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRules;

use App\Modules\HRIS\Models\OvertimeRule;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRules\Pages\CreateOvertimeRule;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRules\Pages\EditOvertimeRule;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRules\Pages\ListOvertimeRules;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRules\Schemas\OvertimeRuleForm;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRules\Tables\OvertimeRulesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class OvertimeRuleResource extends Resource
{
    protected static ?string $model = OvertimeRule::class;

    // Ikon Timbangan/Aturan
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Aturan Lembur';
    protected static ?string $modelLabel = 'Aturan Lembur';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Kehadiran';
    protected static ?int $navigationSort = 2; // Tampil di bawah Grup Kehadiran

    public static function form(Schema $schema): Schema
    {
        return OvertimeRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OvertimeRulesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOvertimeRules::route('/'),
            'create' => CreateOvertimeRule::route('/create'),
            'edit' => EditOvertimeRule::route('/{record}/edit'),
        ];
    }
}
