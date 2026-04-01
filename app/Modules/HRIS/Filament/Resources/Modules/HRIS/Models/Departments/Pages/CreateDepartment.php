<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\DepartmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;
}
