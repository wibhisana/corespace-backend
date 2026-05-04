<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class EmployeeStatsOverview extends BaseWidget
{
    // Atur urutan tampil (semakin kecil semakin di atas)
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        /** @var User $user */
        $user = Auth::user();

        $isGlobalViewer = $user->hasAnyRole(['super_admin', 'hr_manager']);

        // Base query karyawan aktif
        $baseQuery = User::query()->where('employment_status', '!=', 'Resigned');

        // Scope ke departemen sendiri jika bukan super_admin/hr_manager
        if (! $isGlobalViewer) {
            $baseQuery->where('department_id', $user->department_id);
        }

        $totalEmployees = (clone $baseQuery)->count();

        $totalDescription = $isGlobalViewer
            ? 'Seluruh perusahaan'
            : 'Di departemen Anda';

        // (Simulasi) Nanti ini dihubungkan dengan tabel Attendance
        $presentToday = 0;

        // (Simulasi) Nanti ini dihubungkan dengan tabel Leave
        $onLeaveToday = 0;

        return [
            Stat::make('Total Karyawan Aktif', $totalEmployees)
                ->description($totalDescription)
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Hadir Hari Ini', $presentToday)
                ->description('Dari total karyawan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Efek grafik kecil (sparkline)

            Stat::make('Cuti / Izin Hari Ini', $onLeaveToday)
                ->description('Membutuhkan persetujuan/aktif')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
        ];
    }
}
