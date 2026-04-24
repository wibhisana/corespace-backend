<?php
namespace App\Modules\HRIS\Filament\Resources\Shifts\Pages;
use App\Modules\HRIS\Filament\Resources\Shifts\ShiftResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShift extends CreateRecord {
    protected static string $resource = ShiftResource::class;
}
