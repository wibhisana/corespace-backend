<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\MeetingRoom;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Gedung Utama (Root)
        $gedungA = Location::firstOrCreate(['name' => 'Gedung A (Head Office)']);
        $gedungB = Location::firstOrCreate(['name' => 'Gedung B (Workshop)']);

        // Lantai (Children)
        $lt1 = Location::firstOrCreate(['name' => 'Lantai 1', 'parent_id' => $gedungA->id]);
        $lt2 = Location::firstOrCreate(['name' => 'Lantai 2', 'parent_id' => $gedungA->id]);
        $lt3 = Location::firstOrCreate(['name' => 'Lantai 3', 'parent_id' => $gedungA->id]);
        $ltB1 = Location::firstOrCreate(['name' => 'Lantai 1', 'parent_id' => $gedungB->id]);

        // Meeting Rooms
        MeetingRoom::firstOrCreate(
            ['name' => 'Ruang Jayakarta'],
            ['location_id' => $lt1->id, 'capacity' => 10, 'equipment' => ['Proyektor', 'TV 65 Inch', 'Video Conference'], 'is_active' => true, 'requires_approval' => false]
        );

        MeetingRoom::firstOrCreate(
            ['name' => 'Ruang Majapahit'],
            ['location_id' => $lt2->id, 'capacity' => 20, 'equipment' => ['Proyektor', 'Sound System', 'Papan Tulis'], 'is_active' => true, 'requires_approval' => true]
        );

        MeetingRoom::firstOrCreate(
            ['name' => 'Ruang Sriwijaya'],
            ['location_id' => $lt3->id, 'capacity' => 6, 'equipment' => ['TV 55 Inch', 'Video Conference'], 'is_active' => true, 'requires_approval' => false]
        );

        MeetingRoom::firstOrCreate(
            ['name' => 'Ruang Workshop'],
            ['location_id' => $ltB1->id, 'capacity' => 30, 'equipment' => ['Proyektor', 'Sound System'], 'is_active' => true, 'requires_approval' => true]
        );

        $this->command->info('Lokasi & Ruang Rapat berhasil dibuat!');
    }
}
