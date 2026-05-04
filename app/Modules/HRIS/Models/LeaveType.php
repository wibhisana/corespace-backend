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
        'is_active',
        'is_carry_forwardable',
    ];

    protected $casts = [
        'is_unlimited'         => 'boolean',
        'requires_attachment'  => 'boolean',
        'is_active'            => 'boolean',
        'is_carry_forwardable' => 'boolean',
    ];

    public function balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }
}
