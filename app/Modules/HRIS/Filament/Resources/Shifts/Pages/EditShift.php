<?php
namespace App\Modules\HRIS\Filament\Resources\Shifts\Pages;
use App\Modules\HRIS\Filament\Resources\Shifts\ShiftResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShift extends EditRecord {
    protected static string $resource = ShiftResource::class;
    protected function getHeaderActions(): array {
        return [DeleteAction::make()];
    }
}
