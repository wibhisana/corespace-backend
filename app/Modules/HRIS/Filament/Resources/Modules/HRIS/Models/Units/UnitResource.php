<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units;

use App\Modules\HRIS\Models\Unit;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\Pages\CreateUnit;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\Pages\EditUnit;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\Pages\ListUnits;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\Schemas\UnitForm;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\Tables\UnitsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string | UnitEnum | null $navigationGroup = 'Organisasi';

    protected static ?string $navigationLabel = 'Unit Bisnis';

    protected static ?string $modelLabel = 'Unit Bisnis';

    protected static ?string $pluralModelLabel = 'Unit Bisnis';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return UnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitsTable::configure($table);
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
            'index' => ListUnits::route('/'),
            'create' => CreateUnit::route('/create'),
            'edit' => EditUnit::route('/{record}/edit'),
        ];
    }
}
