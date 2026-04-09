<?php

namespace App\Modules\HRIS\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'type'];

    /**
     * Relasi ke Departemen (Satu Unit punya banyak Departemen)
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
