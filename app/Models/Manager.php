<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Manager extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\ManagerFactory> */
    use HasFactory, HasApiTokens;
    protected $fillable = [
        'name', 'email', 'password', 'role',
        'hire_date', 'attendance_rate', 'tasks_completed', 'tasks_requested',
        'company_id',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function isGM()
    {
        return $this->role === 'general_manager';
    }

    public function isDM()
    {
        return $this->role === 'department_manager';
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    /**
     * Employees in this manager's department.
     * Note: employees are linked to departments, not directly to managers.
     */
    public function employees()
    {
        return $this->hasManyThrough(
            Employee::class,
            Department::class,
            'manager_id', // FK on departments
            'department_id', // FK on employees
            'id',
            'id'
        );
    }
    public function department()
    {
        return $this->hasOne(Department::class, 'manager_id');
    }
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
