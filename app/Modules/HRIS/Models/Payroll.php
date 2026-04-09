<?php

namespace App\Modules\HRIS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'basic_salary',
        'total_present',
        'deduction',
        'net_salary',
        'allowance_details',
        'deduction_details',
        'total_allowances',
        'total_deductions',
        'status',
        'payment_date',
        'is_paid',
    ];

    protected $casts = [
        'allowance_details' => 'array',
        'deduction_details' => 'array',
        'payment_date' => 'date',
        'is_paid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
