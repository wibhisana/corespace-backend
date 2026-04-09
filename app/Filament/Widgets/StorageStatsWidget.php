<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\File;

class StorageStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // === DISK SERVER ===
        $storagePath = storage_path('app');
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;

        $totalGB = round($totalSpace / (1024 ** 3), 2);
        $usedGB = round($usedSpace / (1024 ** 3), 2);
        $freeGB = round($freeSpace / (1024 ** 3), 2);
        $percentage = round(($usedSpace / $totalSpace) * 100, 1);

        // === PRIVATE STORAGE (Dokumen Karyawan) ===
        $privatePath = storage_path('app/private/employees');
        $fileCount = 0;
        $privateSize = 0;

        if (File::isDirectory($privatePath)) {
            $files = File::allFiles($privatePath);
            $fileCount = count($files);
            $privateSize = collect($files)->sum(fn ($f) => $f->getSize());
        }

        $privateMB = round($privateSize / (1024 ** 2), 2);

        // === TOTAL KARYAWAN ===
        $totalUsers = User::count();
        $usersWithDocs = User::whereNotNull('id_card_path')
            ->orWhereNotNull('tax_id_path')
            ->count();

        // Estimasi kapasitas: 25MB per karyawan (KTP + NPWP + dokumen lain)
        $estimateCapacity = $freeGB > 0 ? floor(($freeSpace / (25 * 1024 * 1024))) : 0;

        return [
            Stat::make('Disk Server', "{$usedGB} GB / {$totalGB} GB")
                ->description("Tersisa {$freeGB} GB ({$percentage}% terpakai)")
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color($percentage > 85 ? 'danger' : ($percentage > 70 ? 'warning' : 'success')),

            Stat::make('Dokumen Karyawan', "{$fileCount} berkas ({$privateMB} MB)")
                ->description("{$usersWithDocs}/{$totalUsers} karyawan sudah upload dokumen")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Estimasi Kapasitas', "± {$estimateCapacity} karyawan lagi")
                ->description('Asumsi 25MB per karyawan (KTP+NPWP+dll)')
                ->descriptionIcon('heroicon-m-users')
                ->color($estimateCapacity < 100 ? 'warning' : 'success'),
        ];
    }
}
