<?php

namespace App\Modules\HRIS\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'default_quota',
        'is_unlimited',
        'requires_attachment',
        'is_active'
    ];

    public function balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }
}
