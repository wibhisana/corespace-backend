<?php
namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\Pages;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Locations\LocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLocation extends EditRecord {
    protected static string $resource = LocationResource::class;
    protected function getHeaderActions(): array {
        return [DeleteAction::make()];
    }
}
