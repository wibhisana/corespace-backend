<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\PayrollResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;
}
