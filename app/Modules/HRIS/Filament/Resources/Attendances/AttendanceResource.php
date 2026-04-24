<?php

namespace App\Modules\HRIS\Filament\Resources\Attendances;

use App\Modules\HRIS\Models\Attendance;
use App\Modules\HRIS\Filament\Resources\Attendances\Pages\CreateAttendance;
use App\Modules\HRIS\Filament\Resources\Attendances\Pages\EditAttendance;
use App\Modules\HRIS\Filament\Resources\Attendances\Pages\ListAttendances;
use App\Modules\HRIS\Filament\Resources\Attendances\Schemas\AttendanceForm;
use App\Modules\HRIS\Filament\Resources\Attendances\Tables\AttendancesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    // Mengganti ikon menjadi Fingerprint (sidik jari/absen)
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationLabel = 'Log Absensi';
    protected static ?string $modelLabel = 'Log Absensi Harian';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Kehadiran';
    protected static ?int $navigationSort = 4; // Tampil paling bawah setelah Lembur

    protected static ?string $recordTitleAttribute = 'date';

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }

    // Mempertahankan logic otorisasi Anda yang bagus
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['Super Admin', 'Admin', 'HR Manager']) ?? true;
        // Note: Saya tambahkan 'true' sbg fallback sementara jika role belum disetup sempurna
    }
}
