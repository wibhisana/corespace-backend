<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    // Daftarkan kolom yang boleh diisi oleh sistem
    protected $fillable = [
        'user_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status'
    ];

    // Relasi ke Karyawan yang mengajukan cuti
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
