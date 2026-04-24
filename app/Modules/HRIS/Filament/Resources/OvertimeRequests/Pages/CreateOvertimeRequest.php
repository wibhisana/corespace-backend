<?php

namespace App\Modules\HRIS\Filament\Resources\OvertimeRequests\Pages;

use App\Modules\HRIS\Filament\Resources\OvertimeRequests\OvertimeRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOvertimeRequest extends CreateRecord
{
    protected static string $resource = OvertimeRequestResource::class;
}
