<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveTypes\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveTypes\LeaveTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveType extends CreateRecord
{
    protected static string $resource = LeaveTypeResource::class;
}
