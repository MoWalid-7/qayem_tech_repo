<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateEvaluationJob;
use App\Models\Employee;
use App\Models\Manager;
use App\Traits\HasMultiGuardAuth;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use HasMultiGuardAuth;

    /**
     * Create a new employee. DMs cannot create employees.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:employees,email',
            'password'      => 'required|min:6',
            'department_id' => 'required|exists:departments,id',
            'job_title'     => 'nullable|string|max:255',
            'hire_date'     => 'nullable|date',
        ]);

        $user = $this->getCurrentUser();

        if ($user instanceof Manager && $user->isDM()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized - Managers cannot create employees'], 403);
        }

        Employee::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'job_title'     => $request->job_title,
            'hire_date'     => $request->hire_date,
            'company_id'    => $user->company_id,
            'department_id' => $request->department_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Employee registered successfully!']);
    }

    /**
     * Update an employee's performance metrics.
     */
    public function updateMetrics(Request $request)
    {
        $request->validate([
            'employee_id'     => 'required|exists:employees,id',
            'attendance_rate' => 'required|integer|min:0|max:100',
            'tasks_completed' => 'required|integer|min:0',
            'tasks_requested' => 'required|integer|min:0',
            'hire_date'       => 'nullable|date',
        ]);

        $user     = $this->getCurrentUser();
        $employee = Employee::findOrFail($request->employee_id);

        if ($employee->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // DM Restriction: can only update their own department
        if ($user instanceof Manager && $user->isDM()) {
            if ($employee->department_id !== $user->department?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized - Dept Mismatch'], 403);
            }
        }

        $employee->update([
            'attendance_rate' => $request->attendance_rate,
            'tasks_completed' => $request->tasks_completed,
            'tasks_requested' => $request->tasks_requested,
            'hire_date'       => $request->hire_date,
        ]);

        return response()->json(['success' => true, 'message' => 'Performance data updated!']);
    }

    /**
     * Dispatch AI evaluation for an employee (async via Queue).
     */
    public function evaluate(Employee $employee)
    {
        $user = $this->getCurrentUser();

        if ($employee->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($user instanceof Manager && $user->isDM()) {
            if ($employee->department_id !== $user->department?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized - Dept Mismatch'], 403);
            }
        }

        GenerateEvaluationJob::dispatch($employee);

        return response()->json([
            'success' => true,
            'message' => __('AI Evaluation is being generated! Results will appear shortly.'),
        ]);
    }
}
