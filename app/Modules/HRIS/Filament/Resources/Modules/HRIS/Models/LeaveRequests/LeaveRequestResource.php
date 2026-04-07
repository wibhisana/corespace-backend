<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests;

use App\Modules\HRIS\Models\LeaveRequest;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\Pages\CreateLeaveRequest;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\Pages\EditLeaveRequest;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\Pages\ListLeaveRequests;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\Schemas\LeaveRequestForm;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveRequests\Tables\LeaveRequestsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'leave_type';

    public static function form(Schema $schema): Schema
    {
        return LeaveRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveRequestsTable::configure($table);
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
            'index' => ListLeaveRequests::route('/'),
            'create' => CreateLeaveRequest::route('/create'),
            'edit' => EditLeaveRequest::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Tambahkan tanda tanya (?->) dan (?? false) agar tidak crash jika user null
        return auth()->user()?->hasAnyRole(['Super Admin', 'HR Manager']) ?? false;
    }
}
