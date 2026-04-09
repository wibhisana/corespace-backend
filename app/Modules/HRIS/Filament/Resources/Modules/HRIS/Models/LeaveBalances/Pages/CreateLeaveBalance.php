<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveBalances\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveBalances\LeaveBalanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveBalance extends CreateRecord
{
    protected static string $resource = LeaveBalanceResource::class;
}
