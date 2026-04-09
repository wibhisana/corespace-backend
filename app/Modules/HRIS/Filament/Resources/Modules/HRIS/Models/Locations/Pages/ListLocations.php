<?php
namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\Pages;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\LocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLocations extends ListRecords {
    protected static string $resource = LocationResource::class;
    protected function getHeaderActions(): array {
        return [CreateAction::make()];
    }
}
