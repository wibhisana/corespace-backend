<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LeaveQuota extends Model
{
    protected $fillable = ['user_id', 'year', 'quota', 'used', 'expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
