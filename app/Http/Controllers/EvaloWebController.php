<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class EvaloWebController extends Controller
{
    /**
     * Home page / Landing page
     */
    public function index()
    {
        return view('evalo.index');
    }

    /**
     * About page
     */
    public function about()
    {
        return view('evalo.about');
    }

    /**
     * Contact page
     */
    public function contact()
    {
        return view('evalo.contact');
    }

    /**
     * Subscription page (View)
     */
    public function subscription()
    {
        // Fetch plans from DB for the view
        $plans = DB::table('plans')->get();
        return view('evalo.subscription', compact('plans'));
    }

    /**
     * Process Subscription & Payment
     */
    public function processSubscription(Request $request)
    {
        // 1. Validation
        $request->validate([
            'company_name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'plan_id' => 'required|exists:plans,id',
            'payment_method_id' => 'required|string',
        ]);

        $plan = \App\Models\Plan::findOrFail($request->plan_id);

        try {
            Log::info('Subscription Request Data:', $request->all());
            DB::beginTransaction();

            // 1. Create Company
            $company = new \App\Models\Company();
            $company->name = $request->company_name;
            $company->email = $request->email;
            $company->phone = $request->phone;
            $company->address = $request->address;
            $company->save();

            // Create Manager (General Manager)
            $tempPassword = strtolower(str_replace(' ', '', $request->admin_name)) . '123';
            $manager = \App\Models\Manager::create([
                'name' => $request->admin_name,
                'email' => $request->email,
                'password' => bcrypt($tempPassword),
                'company_id' => $company->id,
                'role' => 'general_manager',
            ]);

            // 3. Process Subscription via Cashier
            $priceId = $plan->stripe_price_id;

            if (!$priceId) {
                throw new \Exception("The selected plan has no Stripe Price ID configured.");
            }

            $company->createOrGetStripeCustomer();
            $company->addPaymentMethod($request->payment_method_id);

            $subscription = $company->newSubscription('default', $priceId)
                ->create($request->payment_method_id);

            if ($subscription) {
                $subscription->update(['plan_id' => $plan->id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subscription successful! Your account has been created.',
                'credentials' => [
                    'email' => $request->email,
                    'password' => $tempPassword
                ],
                'redirect' => route('login')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cashier Subscription Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Subscription Error: ' . $e->getMessage()], 500);
        }
    }

    public function login()
    {
        if (Auth::guard('hr')->check()) {
            return redirect()->route('dashboard');
        }
        return view('evalo.login');
    }


    public function aiChat(Request $request)
    {
        $message = $request->message;
        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user() ?? Auth::guard('employee')->user();
        if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

        $company = $user->company;

        if ($user instanceof \App\Models\Employee) {
            $department = $user->department;
            $latestEval = $user->evaluations()->latest()->first();
            $role = $user->job_title ?? 'Employee';

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
            $role = ($user instanceof \App\Models\Manager) ? ($user->isGM() ? 'General Manager' : 'Department Manager') : 'HR Specialist';

            $context = "The company name is '{$company->name}'. ";
            $context .= "You are talking to '{$user->name}' who is a '{$role}'. ";
            $context .= "Departments: " . $company->departments->pluck('name')->implode(', ') . ". ";
            $context .= "Total Employees: " . $company->employees->count() . ". ";
            $context .= "Total Managers: " . $company->managers->count() . ". ";
            $context .= "Help this user manage the company performance and HR operations effectively.";
        }

        $context .= " IMPORTANT: Always respond to the user in the same language they use to message you (Arabic or English).";
        $context .= " Use structured Markdown (bullet points, bold text, and clear sections) to make your response organized and easy to read.";

        $gemini = new \App\Services\GeminiService();
        $response = $gemini->chat($message, $context);

        return response()->json(['response' => $response]);
    }

    public function dashboard()
    {
        $user = null;
        if (Auth::guard('manager')->check()) {
            $user = Auth::guard('manager')->user();
        } elseif (Auth::guard('hr')->check()) {
            $user = Auth::guard('hr')->user();
        }

        if (!$user) {
            return redirect()->route('login');
        }

        $company = $user->company;
        $isDM = ($user instanceof \App\Models\Manager && $user->isDM());

        // Calculate dynamic Avg Performance
        $avgPerformanceQuery = \App\Models\Evaluation::where(function ($q) use ($company) {
            $q->whereHas('employee', function ($sq) use ($company) {
                $sq->where('company_id', $company->id);
            })->orWhereHas('manager', function ($sq) use ($company) {
                $sq->where('company_id', $company->id);
            });
        });

        if ($isDM) {
            $myDeptId = $user->department?->id;
            $avgPerformanceQuery->whereHas('employee', function ($sq) use ($myDeptId) {
                $sq->where('department_id', $myDeptId);
            });
        }
        $avgPerformance = $avgPerformanceQuery->avg('score') ?? 0;

        // Calculate dynamic Chart Data (last 6 months)
        $chartDataValues = [];
        $chartLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $chartLabels[] = $date->translatedFormat('M');

            $avgQuery = \App\Models\Evaluation::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where(function ($q) use ($company) {
                    $q->whereHas('employee', function ($sq) use ($company) {
                        $sq->where('company_id', $company->id);
                    })->orWhereHas('manager', function ($sq) use ($company) {
                        $sq->where('company_id', $company->id);
                    });
                });

            if ($isDM) {
                $avgQuery->whereHas('employee', function ($sq) use ($myDeptId) {
                    $sq->where('department_id', $myDeptId);
                });
            }

            $avg = $avgQuery->avg('score') ?? 0;
            $chartDataValues[] = round($avg, 1);
        }

        $hrs = $company->hr_users;

        if ($isDM) {
            $managers = collect();
            $departments = $user->department ? collect([$user->department]) : collect();
            $employees = $user->department ? $user->department->employees : collect();
        } else {
            $managers = $company->managers()->where('role', 'department_manager')->get();
            $departments = $company->departments;
            $employees = $company->employees;
        }
        $recentActivities = [];
        $pendingTasks = [];
        $jsThinking = \Illuminate\Support\Js::from(__('Thinking...'));
        $jsErrorMsg = \Illuminate\Support\Js::from(__('Failed to reach AI assistant'));

        return view('evalo.dashboard', compact(
            'user',
            'company',
            'departments',
            'managers',
            'employees',
            'hrs',
            'recentActivities',
            'pendingTasks',
            'avgPerformance',
            'chartLabels',
            'chartDataValues'
        ));
    }

    /**
     * Get Profile Data (AJAX)
     */
    public function getProfile(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();

        if ($type === 'employee') {
            $entity = \App\Models\Employee::with(['department', 'evaluations' => function ($q) {
                $q->latest()->limit(1);
            }])->findOrFail($id);
        } else {
            $entity = \App\Models\Manager::with(['department', 'evaluations' => function ($q) {
                $q->latest()->limit(1);
            }])->findOrFail($id);
        }

        if ($entity->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // DM Restriction: Can only view their own department
        if ($user instanceof \App\Models\Manager && $user->isDM()) {
            if ($type === 'employee' && $entity->department_id !== $user->department?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized - Dept Mismatch'], 403);
            }
            if ($type === 'manager' && $entity->id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized - Manager Mismatch'], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $entity,
            'type' => $type,
            'latest_evaluation' => $entity->evaluations->first()
        ]);
    }

    /**
     * Process Login
     */
    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login Attempt for: ' . $credentials['email']);

        // 1. Try Manager Guard (GM or Dept Manager)
        if (Auth::guard('manager')->attempt($credentials)) {
            Log::info('Login Success (Manager) for: ' . $credentials['email']);
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard')
            ]);
        }

        // 2. Try HR Guard
        if (Auth::guard('hr')->attempt($credentials)) {
            Log::info('Login Success (HR) for: ' . $credentials['email']);
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard')
            ]);
        }

        // 3. Try Employee Guard
        if (Auth::guard('employee')->attempt($credentials)) {
            Log::info('Login Success (Employee) for: ' . $credentials['email']);
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'redirect' => route('employee.profile')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided credentials do not match our records.'
        ], 401);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('manager')->logout();
        Auth::guard('hr')->logout();
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Create HR Account
     */
    public function storeHrAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:hr_users,email',
            'password' => 'required|min:6',
        ]);

        $user = null;
        if (Auth::guard('manager')->check()) {
            $user = Auth::guard('manager')->user();
        } elseif (Auth::guard('hr')->check()) {
            $user = Auth::guard('hr')->user();
        }

        if (!$user || !($user->role === 'general_manager' || $user->role === 'gm')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        \App\Models\HrUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'company_id' => $user->company_id,
            'role' => 'hr',
        ]);

        return response()->json(['success' => true, 'message' => 'HR account created successfully!']);
    }

    /**
     * Update HR Account
     */
    public function updateHrAccount(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:hr_users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:hr_users,email,' . $request->id,
            'password' => 'nullable|min:6',
        ]);

        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();
        if (!$user || !($user->role === 'general_manager' || $user->role === 'gm')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $hr = \App\Models\HrUser::where('id', $request->id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $hr->update($data);

        return response()->json(['success' => true, 'message' => 'HR account updated successfully!']);
    }

    /**
     * Delete HR Account
     */
    public function deleteHrAccount(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:hr_users,id',
        ]);

        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();
        if (!$user || !($user->role === 'general_manager' || $user->role === 'gm')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $hr = \App\Models\HrUser::where('id', $request->id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        $hr->delete();

        return response()->json(['success' => true, 'message' => 'HR account deleted successfully!']);
    }

    /**
     * Store Department
     */
    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:managers,id'
        ]);
        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();

        if ($user instanceof \App\Models\Manager && $user->isDM()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized - Managers cannot create departments'], 403);
        }

        \App\Models\Department::create([
            'name' => $request->name,
            'company_id' => $user->company_id,
            'manager_id' => $request->manager_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Department created successfully!']);
    }

    /**
     * Update Department
     */
    public function updateDepartment(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:managers,id'
        ]);

        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();
        $dept = \App\Models\Department::where('id', $request->id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        $dept->update([
            'name' => $request->name,
            'manager_id' => $request->manager_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Department updated successfully!']);
    }

    /**
     * Store Department Manager
     */
    public function storeDeptManager(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:managers,email',
            'password' => 'required|min:6',
            'department_id' => 'nullable|exists:departments,id',
            'hire_date' => 'nullable|date',
        ]);
        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();

        if ($user instanceof \App\Models\Manager && $user->isDM()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized - Managers cannot create other managers'], 403);
        }

        $manager = \App\Models\Manager::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'hire_date' => $request->hire_date,
            'company_id' => $user->company_id,
            'role' => 'department_manager',
        ]);

        if ($request->department_id) {
            \App\Models\Department::where('id', $request->department_id)
                ->where('company_id', $user->company_id)
                ->update(['manager_id' => $manager->id]);
        }

        return response()->json(['success' => true, 'message' => 'Department Manager created successfully!']);
    }

    /**
     * Store Employee
     */
    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|min:6',
            'department_id' => 'required|exists:departments,id',
            'job_title' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
        ]);
        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();

        if ($user instanceof \App\Models\Manager && $user->isDM()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized - Managers cannot create employees'], 403);
        }

        $employee = \App\Models\Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'job_title' => $request->job_title,
            'hire_date' => $request->hire_date,
            'company_id' => $user->company_id,
            'department_id' => $request->department_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Employee registered successfully!']);
    }

    /**
     * Update Employee Metrics
     */
    public function updateEmployeeMetrics(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_rate' => 'required|integer|min:0|max:100',
            'tasks_completed' => 'required|integer|min:0',
            'tasks_requested' => 'required|integer|min:0',
            'hire_date' => 'nullable|date',
        ]);

        $employee = \App\Models\Employee::findOrFail($request->employee_id);
        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();

        if ($employee->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // DM Restriction: Can only update their own department
        if ($user instanceof \App\Models\Manager && $user->isDM()) {
            if ($employee->department_id !== $user->department?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized - Dept Mismatch'], 403);
            }
        }

        $employee->update([
            'attendance_rate' => $request->attendance_rate,
            'tasks_completed' => $request->tasks_completed,
            'tasks_requested' => $request->tasks_requested,
            'hire_date' => $request->hire_date,
        ]);

        return response()->json(['success' => true, 'message' => 'Performance data updated!']);
    }

    /**
     * Update Manager Metrics
     */
    public function updateManagerMetrics(Request $request)
    {
        $request->validate([
            'manager_id' => 'required|exists:managers,id',
            'attendance_rate' => 'required|integer|min:0|max:100',
            'tasks_completed' => 'required|integer|min:0',
            'tasks_requested' => 'required|integer|min:0',
            'hire_date' => 'nullable|date',
        ]);

        $manager = \App\Models\Manager::findOrFail($request->manager_id);
        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();

        if ($manager->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $manager->update([
            'attendance_rate' => $request->attendance_rate,
            'tasks_completed' => $request->tasks_completed,
            'tasks_requested' => $request->tasks_requested,
            'hire_date' => $request->hire_date,
        ]);

        return response()->json(['success' => true, 'message' => 'Manager performance data updated!']);
    }

    /**
     * AI Evaluation for Employee
     */
    public function evaluateEmployee(\App\Models\Employee $employee)
    {
        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();
        if ($employee->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // DM Restriction: Can only evaluate their own department
        if ($user instanceof \App\Models\Manager && $user->isDM()) {
            if ($employee->department_id !== $user->department?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized - Dept Mismatch'], 403);
            }
        }

        $gemini = new \App\Services\GeminiService();
        $result = $gemini->generateEvaluation($employee);

        if ($result && isset($result['score'])) {
            \App\Models\Evaluation::create([
                'employee_id' => $employee->id,
                'evaluation_text' => $result['text'],
                'score' => $result['score'],
                'strengths' => $result['strengths'] ?? null,
                'weaknesses' => $result['weaknesses'] ?? null,
                'recommendations' => $result['recommendations'] ?? null,
            ]);

            return response()->json(['success' => true, 'message' => 'AI Evaluation generated!']);
        }

        return response()->json(['success' => false, 'message' => 'AI Evaluation failed.'], 500);
    }

    /**
     * AI Evaluation for Manager
     */
    public function evaluateManager(\App\Models\Manager $manager)
    {
        $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user();
        if ($manager->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $gemini = new \App\Services\GeminiService();
        $result = $gemini->generateEvaluation($manager);

        if ($result && isset($result['score'])) {
            \App\Models\Evaluation::create([
                'manager_id' => $manager->id,
                'evaluation_text' => $result['text'],
                'score' => $result['score'],
                'strengths' => $result['strengths'] ?? null,
                'weaknesses' => $result['weaknesses'] ?? null,
                'recommendations' => $result['recommendations'] ?? null,
            ]);

            return response()->json(['success' => true, 'message' => 'AI Evaluation for manager generated!']);
        }

        return response()->json(['success' => false, 'message' => 'AI Evaluation failed.'], 500);
    }

    /**
     * Employee Profile Page
     */
    public function employeeProfile()
    {
        $employee = Auth::guard('employee')->user();
        if (!$employee) {
            return redirect()->route('login');
        }

        $employee->load(['department', 'company', 'evaluations' => function ($q) {
            $q->orderByDesc('created_at');
        }]);

        $latestEvaluation = $employee->evaluations->first();

        return view('evalo.employee-profile', compact('employee', 'latestEvaluation'));
    }

    /**
     * Manager Profile Page
     */
    public function managerProfile()
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) {
            return redirect()->route('login');
        }

        $manager->load(['department', 'company', 'evaluations' => function ($q) {
            $q->orderByDesc('created_at');
        }]);

        $latestEvaluation = $manager->evaluations->first();

        return view('evalo.manager-profile', compact('manager', 'latestEvaluation'));
    }


    /**
     * Process Contact Form Submission
     */
    public function processContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to('mo7mdw3lid@gmail.com')
                ->send(new \App\Mail\ContactMail(
                    $request->name,
                    $request->email,
                    $request->subject,
                    $request->message
                ));

            return response()->json(['success' => true, 'message' => __('Your message has been sent successfully! We will get back to you soon.')]);
        } catch (\Exception $e) {
            Log::error('Contact Form Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Sorry, an error occurred.')], 500);
        }
    }
}
