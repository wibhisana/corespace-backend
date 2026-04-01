<?php

namespace App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\Pages;

use App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
