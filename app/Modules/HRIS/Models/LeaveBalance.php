<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LeaveBalance extends Model
{
    public const SOURCE_ANNUAL = 'annual';
    public const SOURCE_CARRY_FORWARD = 'carry_forward';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'source',
        'total_quota',
        'used_quota',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function scopeActive(Builder $query, ?Carbon $asOf = null): Builder
    {
        $cutoff = ($asOf ?? Carbon::today())->toDateString();

        return $query->where(function (Builder $q) use ($cutoff) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>=', $cutoff);
        });
    }

    public function getRemainingQuotaAttribute(): int
    {
        return max(0, (int) $this->total_quota - (int) $this->used_quota);
    }
}
