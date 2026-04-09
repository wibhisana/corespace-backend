<?php
namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\Pages;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\LocationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLocation extends CreateRecord {
    protected static string $resource = LocationResource::class;
}
