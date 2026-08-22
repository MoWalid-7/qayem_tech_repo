<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Manager;
use App\Services\GeminiService;
use App\Traits\HasMultiGuardAuth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Js;

class DashboardController extends Controller
{
    use HasMultiGuardAuth;

    /**
     * Main dashboard — GM, Dept Manager, and HR views.
     */
    public function index()
    {
        $user    = $this->getCurrentUser();
        $company = $user->company;
        $isDM    = ($user instanceof Manager && $user->isDM());

        // ── Eager load everything in one shot to avoid N+1 ──────────────────
        $company->loadMissing([
            'hr_users',
            'departments',
            'employees',
            'managers',
        ]);

        // ── Average performance ──────────────────────────────────────────────
        $avgPerformanceQuery = Evaluation::where(function ($q) use ($company) {
            $q->whereHas('employee', fn($sq) => $sq->where('company_id', $company->id))
              ->orWhereHas('manager', fn($sq) => $sq->where('company_id', $company->id));
        });

        if ($isDM) {
            $myDeptId = $user->department?->id;
            $avgPerformanceQuery->whereHas('employee', fn($sq) => $sq->where('department_id', $myDeptId));
        }

        $avgPerformance = round($avgPerformanceQuery->avg('score') ?? 0, 1);

        // ── Chart data (last 6 months) ───────────────────────────────────────
        $chartLabels     = [];
        $chartDataValues = [];

        for ($i = 5; $i >= 0; $i--) {
            $date          = Carbon::now()->subMonths($i);
            $chartLabels[] = $date->translatedFormat('M');

            $avgQuery = Evaluation::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where(function ($q) use ($company) {
                    $q->whereHas('employee', fn($sq) => $sq->where('company_id', $company->id))
                      ->orWhereHas('manager', fn($sq) => $sq->where('company_id', $company->id));
                });

            if ($isDM) {
                $avgQuery->whereHas('employee', fn($sq) => $sq->where('department_id', $myDeptId));
            }

            $chartDataValues[] = round($avgQuery->avg('score') ?? 0, 1);
        }

        // ── Scoped data per role ─────────────────────────────────────────────
        $hrs = $company->hr_users;

        if ($isDM) {
            $managers    = collect();
            $departments = $user->department ? collect([$user->department]) : collect();
            $employees   = $user->department ? $user->department->employees : collect();
        } else {
            $managers    = $company->managers->where('role', 'department_manager');
            $departments = $company->departments;
            $employees   = $company->employees;
        }

        // ── JS localization helpers ──────────────────────────────────────────
        $jsThinking  = Js::from(__('Thinking...'));
        $jsErrorMsg  = Js::from(__('Failed to reach AI assistant'));

        // ── Today's Attendances ──────────────────────────────────────────────
        $employeeIds = $employees->pluck('id')->toArray();
        $managerIds = $managers->pluck('id')->toArray();
        
        $attendances = \App\Models\Attendance::with('user')
            ->whereDate('date', now()->toDateString())
            ->where(function ($query) use ($employeeIds, $managerIds) {
                $query->where(function($q) use ($employeeIds) {
                    $q->where('user_type', \App\Models\Employee::class)
                      ->whereIn('user_id', $employeeIds);
                })->orWhere(function($q) use ($managerIds) {
                    $q->where('user_type', \App\Models\Manager::class)
                      ->whereIn('user_id', $managerIds);
                });
            })
            ->latest('check_in')
            ->get();

        return view('evalo.hrms-dashboard', compact(
            'user', 'company', 'departments', 'managers',
            'employees', 'hrs', 'avgPerformance',
            'chartLabels', 'chartDataValues',
            'attendances',
            'jsThinking', 'jsErrorMsg'
        ));
    }

    /**
     * AI Chat endpoint (AJAX).
     */
    public function aiChat(Request $request)
    {
        $message = $request->input('message');
        $user    = $this->getCurrentUserAny();

        $company = $user->company;

        if ($user instanceof \App\Models\Employee) {
            $department = $user->department;
            $latestEval = $user->evaluations()->latest()->first();
            $role       = $user->job_title ?? 'Employee';

            $context  = "You are talking to an employee named '{$user->name}'. ";
            $context .= "Their job title is '{$role}'. ";
            $context .= "They work at '{$company->name}' in the '{$department?->name}' department. ";
            if ($latestEval) {
                $context .= "Their latest AI performance score is {$latestEval->score}/10. ";
                $context .= "Strengths: {$latestEval->strengths}. ";
                $context .= "Areas for improvement: {$latestEval->weaknesses}. ";
                $context .= "Recommendations: {$latestEval->recommendations}. ";
            }
            $context .= "Help the employee understand their performance, how they can improve, and answer any HR-related questions.";
        } else {
            $role = ($user instanceof Manager)
                ? ($user->isGM() ? 'General Manager' : 'Department Manager')
                : 'HR Specialist';

            $context  = "The company name is '{$company->name}'. ";
            $context .= "You are talking to '{$user->name}' who is a '{$role}'. ";
            $context .= "Departments: " . $company->departments->pluck('name')->implode(', ') . ". ";
            $context .= "Total Employees: " . $company->employees->count() . ". ";
            $context .= "Total Managers: " . $company->managers->count() . ". ";
            $context .= "Help this user manage the company performance and HR operations effectively.";
        }

        $context .= " IMPORTANT: Always respond to the user in the same language they use to message you (Arabic or English).";
        $context .= " Use structured Markdown (bullet points, bold text, and clear sections) to make your response organized and easy to read.";

        $gemini   = new GeminiService();
        $response = $gemini->chat($message, $context);

        return response()->json(['response' => $response]);
    }

    /**
     * Get a single profile card data via AJAX.
     */
    public function getProfile(Request $request)
    {
        $id   = $request->input('id');
        $type = $request->input('type');
        $user = $this->getCurrentUser();

        if ($type === 'employee') {
            $entity = \App\Models\Employee::with(['department', 'evaluations' => fn($q) => $q->latest()->limit(1)])
                ->findOrFail($id);
        } else {
            $entity = Manager::with(['department', 'evaluations' => fn($q) => $q->latest()->limit(1)])
                ->findOrFail($id);
        }

        if ($entity->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // DM Restriction
        if ($user instanceof Manager && $user->isDM()) {
            if ($type === 'employee' && $entity->department_id !== $user->department?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized - Dept Mismatch'], 403);
            }
            if ($type === 'manager' && $entity->id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized - Manager Mismatch'], 403);
            }
        }

        return response()->json([
            'success'           => true,
            'data'              => $entity,
            'type'              => $type,
            'latest_evaluation' => $entity->evaluations->first(),
        ]);
    }
}
