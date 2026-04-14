<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaloWebController;

Route::get('/', [EvaloWebController::class, 'index'])->name('home');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/about', [EvaloWebController::class, 'about'])->name('about');
Route::get('/contact', [EvaloWebController::class, 'contact'])->name('contact');
Route::post('/contact', [EvaloWebController::class, 'processContact'])->name('contact.process');
Route::get('/subscribe', [EvaloWebController::class, 'subscription'])->name('subscribe');
Route::post('/subscribe', [EvaloWebController::class, 'processSubscription'])->name('subscribe.process');
Route::get('/login', [EvaloWebController::class, 'login'])->name('login');
Route::post('/login', [EvaloWebController::class, 'processLogin'])->name('login.process');
Route::get('/logout', [EvaloWebController::class, 'logout'])->name('logout');

Route::get('/dashboard', [EvaloWebController::class, 'dashboard'])->name('dashboard');
Route::post('/ai-chat', [EvaloWebController::class, 'aiChat'])->name('ai.chat');
Route::get('/get-profile', [EvaloWebController::class, 'getProfile'])->name('profile.get');
Route::post('/hr-users', [EvaloWebController::class, 'storeHrAccount'])->name('hr.store');
Route::post('/hr-users/update', [EvaloWebController::class, 'updateHrAccount'])->name('hr.update');
Route::post('/hr-users/delete', [EvaloWebController::class, 'deleteHrAccount'])->name('hr.delete');
Route::post('/departments', [EvaloWebController::class, 'storeDepartment'])->name('dept.store');
Route::post('/departments/update', [EvaloWebController::class, 'updateDepartment'])->name('dept.update');
Route::post('/dept-managers', [EvaloWebController::class, 'storeDeptManager'])->name('manager.store');
Route::post('/employees', [EvaloWebController::class, 'storeEmployee'])->name('employee.store');
Route::post('/employee-metrics', [EvaloWebController::class, 'updateEmployeeMetrics'])->name('employee.metrics');
Route::post('/manager-metrics', [EvaloWebController::class, 'updateManagerMetrics'])->name('manager.metrics');
Route::post('/evaluate/employee/{employee}', [EvaloWebController::class, 'evaluateEmployee'])->name('employee.evaluate');
Route::post('/evaluate/manager/{manager}', [EvaloWebController::class, 'evaluateManager'])->name('manager.evaluate');

// Employee portal routes
Route::get('/my-profile', [EvaloWebController::class, 'employeeProfile'])->name('employee.profile');

// Manager portal routes
Route::get('/manager/my-profile', [EvaloWebController::class, 'managerProfile'])->name('manager.profile');
