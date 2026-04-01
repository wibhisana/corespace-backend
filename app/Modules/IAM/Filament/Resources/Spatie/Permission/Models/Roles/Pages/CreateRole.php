<?php

namespace App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\Pages;

use App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
