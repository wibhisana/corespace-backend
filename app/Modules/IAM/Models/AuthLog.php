<?php

namespace App\Modules\IAM\Models;

use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    // Beri tahu Laravel kolom apa saja yang boleh diisi
    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'status',
        'message'
    ];
}
