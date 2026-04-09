<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRequests;

use App\Modules\HRIS\Models\OvertimeRequest;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRequests\Pages\CreateOvertimeRequest;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRequests\Pages\EditOvertimeRequest;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRequests\Pages\ListOvertimeRequests;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRequests\Schemas\OvertimeRequestForm;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\OvertimeRequests\Tables\OvertimeRequestsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class OvertimeRequestResource extends Resource
{
    protected static ?string $model = OvertimeRequest::class;

    // Ikon dokumen pengajuan
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Pengajuan Lembur';
    protected static ?string $modelLabel = 'Pengajuan Lembur';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Kehadiran';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return OvertimeRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OvertimeRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOvertimeRequests::route('/'),
            'create' => CreateOvertimeRequest::route('/create'),
            'edit' => EditOvertimeRequest::route('/{record}/edit'),
        ];
    }
}
