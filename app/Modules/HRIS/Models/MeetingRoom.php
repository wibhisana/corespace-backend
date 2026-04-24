<?php
namespace App\Modules\HRIS\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    protected $fillable = ['location_id', 'name', 'capacity', 'equipment', 'is_active', 'requires_approval'];

    // 💡 CASTING PENTING: Mengubah JSON dari database menjadi Array di PHP
    protected $casts = [
        'equipment' => 'array',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    public function location() { return $this->belongsTo(Location::class); }
    public function bookings() { return $this->hasMany(RoomBooking::class); }
}
