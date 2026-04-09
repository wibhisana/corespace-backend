<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveTypes\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveTypes\LeaveTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeaveType extends EditRecord
{
    protected static string $resource = LeaveTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
