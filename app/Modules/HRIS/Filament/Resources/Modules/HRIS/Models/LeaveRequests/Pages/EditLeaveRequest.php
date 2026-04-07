<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\LeaveRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeaveRequest extends EditRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
