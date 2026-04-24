<?php

namespace App\Modules\HRIS\Filament\Resources\OvertimeRules\Pages;

use App\Modules\HRIS\Filament\Resources\OvertimeRules\OvertimeRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOvertimeRule extends CreateRecord
{
    protected static string $resource = OvertimeRuleResource::class;
}
