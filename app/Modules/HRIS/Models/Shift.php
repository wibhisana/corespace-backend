<?php

namespace App\Modules\HRIS\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['name', 'type', 'start_time', 'end_time', 'grace_period'];

    public function attendanceGroups()
    {
        return $this->hasMany(AttendanceGroup::class);
    }
}
