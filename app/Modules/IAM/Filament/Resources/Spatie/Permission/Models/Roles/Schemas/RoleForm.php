<?php

namespace App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Input untuk nama Role (misal: Super Admin, HR Manager)
                TextInput::make('name')
                    ->label('Role Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                // (Opsional/Bonus) Dropdown untuk memilih Permissions jika nanti Anda buat
                Select::make('permissions')
                    ->relationship('permissions', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
