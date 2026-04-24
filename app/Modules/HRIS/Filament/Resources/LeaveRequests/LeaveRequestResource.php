<?php

namespace App\Modules\HRIS\Filament\Resources\LeaveRequests;

use App\Modules\HRIS\Models\LeaveRequest;
use App\Modules\HRIS\Filament\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Modules\HRIS\Filament\Resources\LeaveRequests\Pages\EditLeaveRequest;
use App\Modules\HRIS\Filament\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Modules\HRIS\Filament\Resources\LeaveRequests\Schemas\LeaveRequestForm;
use App\Modules\HRIS\Filament\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Data Isolation Multi-Tier:
     * - Super Admin & HR Manager: lihat semua leave request
     * - Manager: lihat miliknya sendiri + leave bawahan langsungnya
     * - Staff: hanya lihat miliknya sendiri
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // Global access untuk Super Admin & HR Manager
        if ($user->hasAnyRole(['super_admin', 'hr_manager'])) {
            return $query;
        }

        // Manager scope: own + subordinates' requests
        return $query->where(function (Builder $q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('user', fn (Builder $sub) => $sub->where('manager_id', $user->id));
        });
    }
}
