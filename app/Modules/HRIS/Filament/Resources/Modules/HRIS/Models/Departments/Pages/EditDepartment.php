<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\DepartmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
