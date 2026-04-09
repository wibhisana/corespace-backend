<?php
namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Shifts\Pages;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Shifts\ShiftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShifts extends ListRecords {
    protected static string $resource = ShiftResource::class;
    protected function getHeaderActions(): array {
        return [CreateAction::make()];
    }
}
