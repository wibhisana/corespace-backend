<?php

namespace App\Modules\HRIS\Filament\Resources\Payrolls\Pages;

use App\Modules\HRIS\Filament\Resources\Payrolls\PayrollResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;
}
