<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateEvaluationJob;
use App\Models\Department;
use App\Models\Manager;
use App\Traits\HasMultiGuardAuth;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    use HasMultiGuardAuth;

    /**
     * Create a new Department Manager. DMs cannot create other managers.
     */
    public function storeDeptManager(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:managers,email',
            'password'      => 'required|min:6',
            'department_id' => 'nullable|exists:departments,id',
            'hire_date'     => 'nullable|date',
        ]);

        $user = $this->getCurrentUser();

        if ($user instanceof Manager && $user->isDM()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized - Managers cannot create other managers'], 403);
        }

        $manager = Manager::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'hire_date'  => $request->hire_date,
            'company_id' => $user->company_id,
            'role'       => 'department_manager',
        ]);

        if ($request->filled('department_id')) {
            Department::where('id', $request->department_id)
                ->where('company_id', $user->company_id)
                ->update(['manager_id' => $manager->id]);
        }

        return response()->json(['success' => true, 'message' => 'Department Manager created successfully!']);
    }

    /**
     * Update a manager's performance metrics.
     */
    public function updateMetrics(Request $request)
    {
        $request->validate([
            'manager_id'      => 'required|exists:managers,id',
            'attendance_rate' => 'required|integer|min:0|max:100',
            'tasks_completed' => 'required|integer|min:0',
            'tasks_requested' => 'required|integer|min:0',
            'hire_date'       => 'nullable|date',
        ]);

        $user    = $this->getCurrentUser();
        $manager = Manager::findOrFail($request->manager_id);

        if ($manager->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $manager->update([
            'attendance_rate' => $request->attendance_rate,
            'tasks_completed' => $request->tasks_completed,
            'tasks_requested' => $request->tasks_requested,
            'hire_date'       => $request->hire_date,
        ]);

        return response()->json(['success' => true, 'message' => 'Manager performance data updated!']);
    }

    /**
     * Dispatch AI evaluation for a manager (async via Queue).
     */
    public function evaluate(Manager $manager)
    {
        $user = $this->getCurrentUser();

        if ($manager->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        GenerateEvaluationJob::dispatch($manager);

        return response()->json([
            'success' => true,
            'message' => __('AI Evaluation is being generated! Results will appear shortly.'),
        ]);
    }
}
