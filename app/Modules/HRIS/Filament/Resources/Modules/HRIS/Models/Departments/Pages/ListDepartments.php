<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\DepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
