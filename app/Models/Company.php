<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory, \Laravel\Cashier\Billable;

    protected $guarded = [];
    public function hr_users()
    {
        return $this->hasMany(HrUser::class);
    }
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
    public function managers()
    {
        return $this->hasMany(Manager::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    public function plans()
    {
        return $this->hasManyThrough(Plan::class, Subscription::class, 'company_id', 'id', 'id', 'plan_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
    public function bulk_uploads()
    {
        return $this->hasMany(BulkUpload::class);
    }
    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
}
