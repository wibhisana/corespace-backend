<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\UnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
