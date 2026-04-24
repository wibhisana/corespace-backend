<?php

namespace App\Modules\HRIS\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    // Daftarkan kolom yang boleh diisi
    protected $fillable = [
        'name',
        'parent_id', // Penting untuk hierarki (Gedung -> Lantai)
    ];

    /**
     * Relasi ke Induk Lokasi (Parent)
     * Contoh: "Lantai 1" menginduk ke "Gedung Pusat"
     */
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    /**
     * Relasi ke Anak Lokasi (Children)
     * Contoh: "Gedung Pusat" memiliki anak "Lantai 1" dan "Lantai 2"
     */
    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    /**
     * Relasi ke Ruang Rapat (Satu lokasi bisa punya banyak ruang rapat)
     * Contoh: "Lantai 1" punya "Ruang Jayakarta" dan "Ruang Sunda Kelapa"
     */
    public function meetingRooms()
    {
        return $this->hasMany(MeetingRoom::class);
    }
}
