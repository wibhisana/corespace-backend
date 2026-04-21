<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'shift_id',
        'date',
        'clock_in',
        'clock_out',
        'lateness_minutes',
        'early_out_minutes',
        'status',
        'clock_in_location',
        'clock_out_location',
        'notes',
        // Kolom geofencing API Mobile
        'type',
        'latitude',
        'longitude',
        'photo_path',
    ];

    // Mengubah format string dari database menjadi objek Carbon (DateTime)
    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
