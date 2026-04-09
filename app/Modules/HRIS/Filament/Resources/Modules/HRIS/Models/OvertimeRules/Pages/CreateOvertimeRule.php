<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRules\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRules\OvertimeRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOvertimeRule extends CreateRecord
{
    protected static string $resource = OvertimeRuleResource::class;
}
