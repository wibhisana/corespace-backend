<?php

namespace App\Modules\HRIS\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Attendance extends Model
{
    // kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'notes',
    ];

    // Relasi balik ke Karyawan (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
