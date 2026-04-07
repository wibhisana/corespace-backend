<?php

namespace App\Modules\IAM\Filament\Resources\Users;

use App\Models\User;
use App\Modules\IAM\Filament\Resources\Users\Pages\CreateUser;
use App\Modules\IAM\Filament\Resources\Users\Pages\EditUser;
use App\Modules\IAM\Filament\Resources\Users\Pages\ListUsers;
use App\Modules\IAM\Filament\Resources\Users\Schemas\UserForm;
use App\Modules\IAM\Filament\Resources\Users\Tables\UsersTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Modules\IAM\Filament\Resources\Users\RelationManagers\LeaveQuotasRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LeaveQuotasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
