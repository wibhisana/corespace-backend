<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    // Daftarkan kolom yang boleh diisi
    protected $fillable = [
        'name',
        // Tambahkan nama kolom lain di sini jika ada (misal: 'description')
    ];

    // Relasi ke Karyawan (Satu Departemen punya banyak Karyawan)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
