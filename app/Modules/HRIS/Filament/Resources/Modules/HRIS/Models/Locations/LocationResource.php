<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations;

use App\Models\Location;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\Pages\CreateLocation;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\Pages\EditLocation;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\Pages\ListLocations;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\Schemas\LocationForm;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\Tables\LocationsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    // Ikon pin lokasi (Map Pin)
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Lokasi & Gedung';
    protected static ?string $modelLabel = 'Lokasi / Gedung';

    public static function form(Schema $schema): Schema
    {
        return LocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'edit' => EditLocation::route('/{record}/edit'),
        ];
    }
}
