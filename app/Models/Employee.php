<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'job_title', 'hire_date',
        'salary', 'attendance_rate', 'tasks_completed', 'tasks_requested',
        'company_id', 'department_id',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'employee';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'hire_date' => 'date',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
