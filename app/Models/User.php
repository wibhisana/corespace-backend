<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Modules\HRIS\Models\Department;
use App\Modules\HRIS\Models\AttendanceGroup;
use App\Modules\HRIS\Models\Unit;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;

// #[Fillable(['name', 'email', 'password'])]
// #[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Tentukan siapa yang boleh login ke Filament panel.
     * Hanya user dengan minimal 1 role yang boleh masuk.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles()->exists();
    }

    protected $fillable = [
        // Data HR (Kategori 1)
        'name',
        'email',
        'password',
        'nik',
        'unit_id',
        'department_id',
        'manager_id',
        'attendance_group_id',
        'job_title',
        'join_date',
        'employment_status',

        // Data Karyawan / ESS (Kategori 2)
        'phone_number',
        'personal_email',
        'current_address',
        'gender',
        'birth_place',
        'birth_date',
        'marital_status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'id_card_number',
        'id_card_path',
        'tax_id',
        'tax_id_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'join_date' => 'date',
            'birth_date' => 'date',
        ];
    }

    /**
     * Cek apakah karyawan sudah melengkapi profil ESS-nya.
     */
    public function isProfileComplete(): bool
    {
        return !empty($this->phone_number)
            && !empty($this->id_card_number)
            && $this->employeeFinance
            && !empty($this->employeeFinance->account_number);
    }

    // Relasi ke Modul HRIS
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Atasan langsung karyawan ini.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Bawahan langsung karyawan ini.
     */
    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function employeeFinance()
    {
        return $this->hasOne(\App\Modules\HRIS\Models\EmployeeFinance::class);
    }

    // Relasi ke Attendance
    public function attendances()
    {
        return $this->hasMany(\App\Modules\HRIS\Models\Attendance::class);
    }

    // Relasi ke LeaveQuota (Satu Karyawan bisa punya banyak LeaveQuota untuk tahun yang berbeda)
    public function leaveQuotas()
    {
        return $this->hasMany(\App\Modules\HRIS\Models\LeaveQuota::class);
    }

    public function attendanceGroup()
    {
        return $this->belongsTo(AttendanceGroup::class);
    }

    public function leaveBalances()
    {
        // Panggil namespace yang tepat karena model ada di dalam modul HRIS
        return $this->hasMany(\App\Modules\HRIS\Models\LeaveBalance::class);
    }
}
