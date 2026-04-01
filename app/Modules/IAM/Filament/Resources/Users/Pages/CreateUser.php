<?php

namespace App\Modules\IAM\Filament\Resources\Users\Pages;

use App\Modules\IAM\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
