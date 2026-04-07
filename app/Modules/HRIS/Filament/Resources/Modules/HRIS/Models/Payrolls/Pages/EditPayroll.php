<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\PayrollResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayroll extends EditRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
