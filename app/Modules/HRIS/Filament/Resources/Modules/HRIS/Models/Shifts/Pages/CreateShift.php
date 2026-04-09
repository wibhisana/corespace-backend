<?php
namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Shifts\Pages;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Shifts\ShiftResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShift extends CreateRecord {
    protected static string $resource = ShiftResource::class;
}
