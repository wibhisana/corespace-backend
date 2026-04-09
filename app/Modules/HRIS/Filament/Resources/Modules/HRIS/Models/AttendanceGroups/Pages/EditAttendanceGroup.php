<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\AttendanceGroups\AttendanceGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceGroup extends EditRecord
{
    protected static string $resource = AttendanceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
