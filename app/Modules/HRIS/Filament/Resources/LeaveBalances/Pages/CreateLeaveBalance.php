<?php

namespace App\Modules\HRIS\Filament\Resources\LeaveBalances\Pages;

use App\Modules\HRIS\Filament\Resources\LeaveBalances\LeaveBalanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveBalance extends CreateRecord
{
    protected static string $resource = LeaveBalanceResource::class;
}
