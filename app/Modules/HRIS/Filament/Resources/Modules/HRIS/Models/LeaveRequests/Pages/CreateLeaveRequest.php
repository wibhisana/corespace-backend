<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\LeaveRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;
}
