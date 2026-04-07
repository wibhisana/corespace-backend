<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmployeeFinance extends Model
{
    // Daftarkan kolom yang diizinkan untuk diisi
    protected $fillable = [
        'user_id',
        'basic_salary',
        'bank_name',
        'account_number'
    ];

    // 👇 Fungsi krusial untuk keamanan tingkat tinggi (Enkripsi at Rest)
    protected function casts(): array
    {
        return [
            'basic_salary' => 'encrypted',
        ];
    }

    // Relasi balik ke Karyawan (Satu brankas milik satu karyawan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
