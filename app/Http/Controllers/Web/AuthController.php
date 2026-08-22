<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Traits\HasMultiGuardAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use HasMultiGuardAuth;

    /**
     * Show the login page.
     */
    public function login()
    {
        if (Auth::guard('manager')->check() || Auth::guard('hr')->check()) {
            return redirect()->route('dashboard');
        }

        return view('evalo.login');
    }

    /**
     * Process login for Manager, HR, or Employee guards.
     */
    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt for: ' . $credentials['email']);

        // 1. Try Manager guard (GM or Dept Manager)
        if (Auth::guard('manager')->attempt($credentials)) {
            Log::info('Login success (Manager): ' . $credentials['email']);
            $request->session()->regenerate();
            return response()->json([
                'success'  => true,
                'redirect' => route('dashboard'),
            ]);
        }

        // 2. Try HR guard
        if (Auth::guard('hr')->attempt($credentials)) {
            Log::info('Login success (HR): ' . $credentials['email']);
            $request->session()->regenerate();
            return response()->json([
                'success'  => true,
                'redirect' => route('dashboard'),
            ]);
        }

        // 3. Try Employee guard
        if (Auth::guard('employee')->attempt($credentials)) {
            Log::info('Login success (Employee): ' . $credentials['email']);
            $request->session()->regenerate();
            return response()->json([
                'success'  => true,
                'redirect' => route('employee.profile'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided credentials do not match our records.',
        ], 401);
    }

    /**
     * Log out all guards and redirect to login.
     */
    public function logout(Request $request)
    {
        // Preserve locale before invalidating session
        $locale = session('locale');

        Auth::guard('manager')->logout();
        Auth::guard('hr')->logout();
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Restore locale
        if ($locale) {
            session(['locale' => $locale]);
        }

        return redirect()->route('login');
    }
}
