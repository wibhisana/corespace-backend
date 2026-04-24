<?php

namespace App\Modules\HRIS\Filament\Resources\OvertimeRequests\Pages;

use App\Modules\HRIS\Filament\Resources\OvertimeRequests\OvertimeRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOvertimeRequest extends EditRecord
{
    protected static string $resource = OvertimeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
