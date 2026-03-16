<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Manager extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\ManagerFactory> */
    use HasFactory, HasApiTokens;
    protected $guarded = [];

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
    public function employees()
    {
        return $this->hasMany(Employee::class);
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
