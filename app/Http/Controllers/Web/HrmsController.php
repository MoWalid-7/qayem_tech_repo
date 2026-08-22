<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Manager;
use App\Traits\HasMultiGuardAuth;
use Illuminate\Support\Carbon;

class HrmsController extends Controller
{
    use HasMultiGuardAuth;

    /** Employees list page */
    public function employees()
    {
        $user    = $this->getCurrentUser();
        $company = $user->company;
        $isDM    = ($user instanceof Manager && $user->isDM());

        $query = Employee::with(['department', 'evaluations'])
            ->where('company_id', $company->id);

        if ($isDM) {
            $query->where('department_id', $user->department?->id);
        }

        $employees   = $query->latest()->get();
        $departments = Department::where('company_id', $company->id)->get();

        return view('evalo.hrms.employees', compact('user', 'employees', 'departments', 'company'));
    }

    /** Departments page */
    public function departments()
    {
        $user        = $this->getCurrentUser();
        $company     = $user->company;
        $departments = Department::with(['employees', 'manager'])
            ->where('company_id', $company->id)
            ->get();

        return view('evalo.hrms.departments', compact('user', 'departments', 'company'));
    }

    /** Attendance page */
    public function attendance()
    {
        $user    = $this->getCurrentUser();
        $company = $user->company;
        $isDM    = ($user instanceof Manager && $user->isDM());

        // Attendance uses morphTo 'user', so we filter by checking the employee table
        $empIds = \App\Models\Employee::where('company_id', $company->id)
            ->when($isDM, fn($q) => $q->where('department_id', $user->department?->id))
            ->pluck('id');

        $records = Attendance::with('user')
            ->where('user_type', \App\Models\Employee::class)
            ->whereIn('user_id', $empIds)
            ->orderByDesc('date')
            ->paginate(20);

        $today = Carbon::today();
        $todayRecords = Attendance::with('user')
            ->where('user_type', \App\Models\Employee::class)
            ->whereIn('user_id', $empIds)
            ->whereDate('date', $today)
            ->get();

        $presentToday = $todayRecords->whereNotNull('check_in')->count();
        $lateToday    = $todayRecords->filter(
            fn($a) => $a->check_in && Carbon::parse($a->check_in)->format('H:i') > '09:00'
        )->count();

        return view('evalo.hrms.attendance', compact('user', 'records', 'presentToday', 'lateToday', 'company'));
    }

    /** Leave Requests page */
    public function leaveRequests()
    {
        $user    = $this->getCurrentUser();
        $company = $user->company;

        $leaves = collect();
        if (class_exists(\App\Models\LeaveRequest::class)) {
            $leaves = \App\Models\LeaveRequest::with('employee')
                ->whereHas('employee', fn($q) => $q->where('company_id', $company->id))
                ->latest()->paginate(20);
        }

        return view('evalo.hrms.leave', compact('user', 'leaves', 'company'));
    }

    /** Payroll page */
    public function payroll()
    {
        $user      = $this->getCurrentUser();
        $company   = $user->company;
        $employees = Employee::with(['department'])
            ->where('company_id', $company->id)
            ->get();

        return view('evalo.hrms.payroll', compact('user', 'employees', 'company'));
    }
}
