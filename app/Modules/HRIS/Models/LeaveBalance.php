<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'total_quota',
        'used_quota',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    // Accessor cerdas untuk menghitung sisa cuti on-the-fly
    public function getRemainingQuotaAttribute()
    {
        return $this->total_quota - $this->used_quota;
    }
}
