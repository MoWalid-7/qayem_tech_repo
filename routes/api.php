<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\ManagerController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Evalo REST API Routes
|--------------------------------------------------------------------------
|
| Authentication: Laravel Sanctum (Bearer Token)
| Consumer: HrUser (one per company)
|
*/

// ── Public Auth Routes ────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// ── Protected Routes (requires Sanctum token) ─────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    // Company (singleton — HrUser belongs to one company)
    Route::prefix('company')->group(function () {
        Route::get('/', [CompanyController::class, 'show']);
        Route::put('/', [CompanyController::class, 'update']);
    });

    // Managers
    Route::apiResource('managers', ManagerController::class);

    // Departments
    Route::apiResource('departments', DepartmentController::class);

    // Employees
    Route::apiResource('employees', EmployeeController::class);

    // Evaluations
    Route::apiResource('evaluations', EvaluationController::class);

    // Plans (read-only — managed by Admin)
    Route::get('plans', [PlanController::class, 'index']);
    Route::get('plans/{plan}', [PlanController::class, 'show']);

    // Subscriptions (read-only — managed by Admin)
    Route::get('subscriptions', [SubscriptionController::class, 'index']);
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show']);
});
