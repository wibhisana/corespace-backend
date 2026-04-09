<?php

namespace App\Modules\HRIS\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeRule extends Model
{
    protected $fillable = [
        'name', 'calculation_method', 'compensation_type', 'requires_approval', 'is_active'
    ];
}
