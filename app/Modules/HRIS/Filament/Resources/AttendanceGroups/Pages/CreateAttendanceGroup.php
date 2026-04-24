<?php

namespace App\Modules\HRIS\Filament\Resources\AttendanceGroups\Pages;

use App\Modules\HRIS\Filament\Resources\AttendanceGroups\AttendanceGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceGroup extends CreateRecord
{
    protected static string $resource = AttendanceGroupResource::class;
}
