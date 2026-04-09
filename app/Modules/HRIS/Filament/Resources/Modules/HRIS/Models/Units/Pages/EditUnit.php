<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Units\UnitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
