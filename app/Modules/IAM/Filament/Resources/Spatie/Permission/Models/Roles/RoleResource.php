<?php

namespace App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles;

use Spatie\Permission\Models\Role;
use App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\Pages\CreateRole;
use App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\Pages\EditRole;
use App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\Pages\ListRoles;
use App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\Schemas\RoleForm;
use App\Modules\IAM\Filament\Resources\Spatie\Permission\Models\Roles\Tables\RolesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
