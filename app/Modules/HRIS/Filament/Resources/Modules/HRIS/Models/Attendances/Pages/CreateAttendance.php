<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Attendances\AttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;
}
