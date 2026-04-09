<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveTypes\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveTypes\LeaveTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeaveTypes extends ListRecords
{
    protected static string $resource = LeaveTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
