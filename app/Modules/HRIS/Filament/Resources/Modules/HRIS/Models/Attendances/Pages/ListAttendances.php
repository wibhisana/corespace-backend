<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\AttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
