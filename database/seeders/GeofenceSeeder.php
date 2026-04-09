<?php

namespace Database\Seeders;

use App\Modules\HRIS\Models\Unit;
use Illuminate\Database\Seeder;

class GeofenceSeeder extends Seeder
{
    public function run(): void
    {
        // Set koordinat kantor pusat (contoh: Jakarta Pusat)
        Unit::where('name', 'KPN Corp Head Office')->update([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meters' => 100,
        ]);

        $this->command->info('Koordinat geofencing Unit berhasil diatur!');
    }
}
