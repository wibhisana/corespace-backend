<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AttendanceGroup extends Model
{
    protected $fillable = ['name', 'description', 'shift_id', 'is_active'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
