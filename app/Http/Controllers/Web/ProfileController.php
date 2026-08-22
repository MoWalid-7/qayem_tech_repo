<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Employee self-service profile page.
     */
    public function employeeProfile()
    {
        $employee = Auth::guard('employee')->user();

        if (!$employee) {
            return redirect()->route('login');
        }

        $employee->load(['department', 'company', 'evaluations' => fn($q) => $q->orderByDesc('created_at')]);

        $latestEvaluation = $employee->evaluations->first();

        return view('evalo.employee-profile', compact('employee', 'latestEvaluation'));
    }

    /**
     * Manager self-service profile page.
     */
    public function managerProfile()
    {
        $manager = Auth::guard('manager')->user();

        if (!$manager) {
            return redirect()->route('login');
        }

        $manager->load(['department', 'company', 'evaluations' => fn($q) => $q->orderByDesc('created_at')]);

        $latestEvaluation = $manager->evaluations->first();

        return view('evalo.manager-profile', compact('manager', 'latestEvaluation'));
    }
}
