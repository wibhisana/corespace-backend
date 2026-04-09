<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveBalances\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveBalances\LeaveBalanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeaveBalances extends ListRecords
{
    protected static string $resource = LeaveBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
