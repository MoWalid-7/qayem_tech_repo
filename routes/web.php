<?php

use App\Http\Controllers\EvaloWebController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DepartmentController;
use App\Http\Controllers\Web\EmployeeController as WebEmployeeController;
use App\Http\Controllers\Web\HrController;
use App\Http\Controllers\Web\ManagerController as WebManagerController;
use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [EvaloWebController::class, 'index'])->name('home');
Route::get('/about', [EvaloWebController::class, 'about'])->name('about');
Route::get('/contact', [EvaloWebController::class, 'contact'])->name('contact');
Route::post('/contact', [EvaloWebController::class, 'processContact'])->name('contact.process')
    ->middleware('throttle:10,1');



// Language switcher
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Auth Routes (guests only)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process')
    ->middleware('throttle:5,1');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes — Manager or HR (any of the two guards)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Web\HrmsController;

Route::middleware(['auth.multi:manager,hr'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/ai-chat', [DashboardController::class, 'aiChat'])->name('ai.chat')
        ->withoutMiddleware(['auth.multi:manager,hr'])
        ->middleware(['auth.multi:manager,hr,employee', 'throttle:20,1']);
    Route::get('/get-profile', [DashboardController::class, 'getProfile'])->name('profile.get');

    // HR Users
    Route::post('/hr-users', [HrController::class, 'store'])->name('hr.store');
    Route::post('/hr-users/update', [HrController::class, 'update'])->name('hr.update');
    Route::post('/hr-users/delete', [HrController::class, 'destroy'])->name('hr.delete');

    // Departments
    Route::get('/departments', [HrmsController::class, 'departments'])->name('hrms.departments');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('dept.store');
    Route::post('/departments/update', [DepartmentController::class, 'update'])->name('dept.update');

    // Department Managers
    Route::post('/dept-managers', [WebManagerController::class, 'storeDeptManager'])->name('manager.store');
    Route::post('/manager-metrics', [WebManagerController::class, 'updateMetrics'])->name('manager.metrics');
    Route::post('/evaluate/manager/{manager}', [WebManagerController::class, 'evaluate'])->name('manager.evaluate');

    // Employees list page
    Route::get('/employees', [HrmsController::class, 'employees'])->name('hrms.employees');
    Route::post('/employees', [WebEmployeeController::class, 'store'])->name('employee.store');
    Route::post('/employee-metrics', [WebEmployeeController::class, 'updateMetrics'])->name('employee.metrics');
    Route::post('/evaluate/employee/{employee}', [WebEmployeeController::class, 'evaluate'])->name('employee.evaluate');

    // Attendance
    Route::get('/attendance', [HrmsController::class, 'attendance'])->name('hrms.attendance');

    // Leave Requests
    Route::get('/leave-requests', [HrmsController::class, 'leaveRequests'])->name('hrms.leave');

    // Payroll
    Route::get('/payroll', [HrmsController::class, 'payroll'])->name('hrms.payroll');

    // Manager's own profile
    Route::get('/manager/my-profile', [ProfileController::class, 'managerProfile'])->name('manager.profile');
});

/*
|--------------------------------------------------------------------------
| Protected Routes — Employee only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.multi:employee'])->group(function () {
    Route::get('/my-profile', [ProfileController::class, 'employeeProfile'])->name('employee.profile');
    
    // Attendance routes for Employee
    Route::post('/attendance/check-in', [\App\Http\Controllers\Web\AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
    Route::post('/attendance/check-out', [\App\Http\Controllers\Web\AttendanceController::class, 'checkOut'])->name('attendance.checkOut');
});
