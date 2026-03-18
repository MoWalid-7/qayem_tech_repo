<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QayemWebController;

Route::get('/', [QayemWebController::class, 'index'])->name('home');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/about', [QayemWebController::class, 'about'])->name('about');
Route::get('/contact', [QayemWebController::class, 'contact'])->name('contact');
Route::post('/contact', [QayemWebController::class, 'processContact'])->name('contact.process');
Route::get('/subscribe', [QayemWebController::class, 'subscription'])->name('subscribe');
Route::post('/subscribe', [QayemWebController::class, 'processSubscription'])->name('subscribe.process');
Route::get('/login', [QayemWebController::class, 'login'])->name('login');
Route::post('/login', [QayemWebController::class, 'processLogin'])->name('login.process');
Route::get('/logout', [QayemWebController::class, 'logout'])->name('logout');

Route::get('/dashboard', [QayemWebController::class, 'dashboard'])->name('dashboard');
Route::post('/ai-chat', [QayemWebController::class, 'aiChat'])->name('ai.chat');
Route::get('/get-profile', [QayemWebController::class, 'getProfile'])->name('profile.get');
Route::post('/hr-users', [QayemWebController::class, 'storeHrAccount'])->name('hr.store');
Route::post('/departments', [QayemWebController::class, 'storeDepartment'])->name('dept.store');
Route::post('/departments/update', [QayemWebController::class, 'updateDepartment'])->name('dept.update');
Route::post('/dept-managers', [QayemWebController::class, 'storeDeptManager'])->name('manager.store');
Route::post('/employees', [QayemWebController::class, 'storeEmployee'])->name('employee.store');
Route::post('/employee-metrics', [QayemWebController::class, 'updateEmployeeMetrics'])->name('employee.metrics');
Route::post('/manager-metrics', [QayemWebController::class, 'updateManagerMetrics'])->name('manager.metrics');
Route::post('/evaluate/employee/{employee}', [QayemWebController::class, 'evaluateEmployee'])->name('employee.evaluate');
Route::post('/evaluate/manager/{manager}', [QayemWebController::class, 'evaluateManager'])->name('manager.evaluate');

// Employee portal routes
Route::get('/my-profile', [QayemWebController::class, 'employeeProfile'])->name('employee.profile');
Route::post('/my-ai-chat', [QayemWebController::class, 'employeeAiChat'])->name('employee.ai.chat');
