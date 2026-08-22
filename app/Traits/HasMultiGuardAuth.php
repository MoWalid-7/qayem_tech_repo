<?php

namespace App\Traits;

use App\Models\Employee;
use App\Models\HrUser;
use App\Models\Manager;
use Illuminate\Support\Facades\Auth;

trait HasMultiGuardAuth
{
    /**
     * Return the authenticated Manager or HrUser (dashboard-level users).
     */
    protected function getCurrentUser(): Manager|HrUser|null
    {
        return Auth::guard('manager')->user()
            ?? Auth::guard('hr')->user();
    }

    /**
     * Return the authenticated user across all guards (including employee).
     */
    protected function getCurrentUserAny(): Manager|HrUser|Employee|null
    {
        return Auth::guard('manager')->user()
            ?? Auth::guard('hr')->user()
            ?? Auth::guard('employee')->user();
    }
}
