<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\AttendanceGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceGroups extends ListRecords
{
    protected static string $resource = AttendanceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
