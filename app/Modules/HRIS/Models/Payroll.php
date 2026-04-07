<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    // 👇 Daftarkan kolom yang boleh diisi oleh sistem (Mass Assignment)
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'basic_salary',
        'total_present',
        'deduction',
        'net_salary',
        'is_paid',
    ];

    // 👇 Relasi ke Karyawan (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
