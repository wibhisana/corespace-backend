<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Modules\HRIS\Models\Department;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class EmployeeDepartmentChart extends ChartWidget
{
    protected ?string $heading = 'Persebaran Karyawan per Departemen';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->hasAnyRole(['super_admin', 'hr_manager']);
    }

    protected function getData(): array
    {
        // Ambil semua departemen beserta jumlah karyawannya (relasi users)
        $departments = Department::withCount('users')->get();

        $labels = $departments->pluck('name')->toArray();
        $data = $departments->pluck('users_count')->toArray();

        // Warna untuk tiap slice grafik
        $colors = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Karyawan',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
