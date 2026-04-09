<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    // Daftarkan kolom yang boleh diisi (Sangat penting agar input Form tidak ditolak Laravel)
    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'sort_order',
        'unit_id',
    ];

    /**
     * Relasi ke Induk Departemen (Parent)
     * Contoh: "Tim IT" menginduk ke "Divisi Teknologi"
     */
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * Relasi ke Anak Departemen (Children)
     * Contoh: "Divisi Teknologi" memiliki anak "Tim IT" dan "Tim Data"
     */
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    /**
     * Relasi ke Karyawan (Satu Departemen punya banyak Karyawan)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi ke Unit (Satu Departemen hanya dimiliki oleh satu Unit)
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
