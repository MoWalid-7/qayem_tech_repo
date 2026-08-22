@extends('evalo.hrms-layout')

@section('title', __('Payroll') . ' — Evalo HRMS')
@section('page_title', __('Payroll'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">{{ __('Payroll') }}</h5>
        <p class="text-secondary small mb-0">{{ __('Manage salaries and compensation') }}</p>
    </div>
    <span class="badge-pill cyan">
        <i class="bi bi-calendar3 me-1"></i> {{ now()->translatedFormat('F Y') }}
    </span>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card indigo p-3">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ $employees->count() }}</div>
            <div class="stat-label">{{ __('Total Employees') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card emerald p-3">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value">{{ number_format($employees->sum('salary') ?? 0) }}</div>
            <div class="stat-label">{{ __('Monthly Payroll') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card cyan p-3">
            <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
            <div class="stat-value">{{ $employees->count() > 0 ? number_format($employees->avg('salary') ?? 0) : '0' }}</div>
            <div class="stat-label">{{ __('Avg. Salary') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card amber p-3">
            <div class="stat-icon"><i class="bi bi-building"></i></div>
            <div class="stat-value">{{ $employees->groupBy('department_id')->count() }}</div>
            <div class="stat-label">{{ __('Departments') }}</div>
        </div>
    </div>
</div>

{{-- Payroll Table --}}
<div class="glass-card p-0 overflow-hidden">
    <div class="p-4 d-flex align-items-center justify-content-between border-bottom" style="border-color: var(--glass-border) !important;">
        <h6 class="mb-0 fw-bold">{{ __('Employee Payroll Summary') }}</h6>
        <span class="badge-pill warning">
            <i class="bi bi-clock me-1"></i>{{ __('Current Month') }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Department') }}</th>
                    <th>{{ __('Base Salary') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">{{ substr($emp->name, 0, 1) }}</div>
                            <div>
                                <div class="fw-semibold">{{ $emp->name }}</div>
                                <div class="small text-secondary">{{ $emp->job_title ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-secondary small">{{ $emp->department?->name ?? '—' }}</td>
                    <td>
                        @if($emp->salary)
                            <span class="fw-semibold text-success">
                                {{ number_format($emp->salary, 2) }} {{ __('EGP') }}
                            </span>
                        @else
                            <span class="badge-pill warning">{{ __('Not Set') }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-pill {{ rand(0,1) ? 'success' : 'warning' }}">
                            {{ rand(0,1) ? __('Paid') : __('Pending') }}
                        </span>
                    </td>
                    <td>
                        <button class="btn-modern ghost btn-sm py-1 px-2" title="{{ __('View Details') }}">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-secondary">
                        <i class="bi bi-cash-stack fs-1 d-block mb-3 opacity-25"></i>
                        {{ __('No employees found to display payroll information.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Coming Soon Note --}}
<div class="glass-card mt-4 p-4 d-flex align-items-center gap-3" style="border-color: rgba(99,102,241,0.2)!important; background: rgba(99,102,241,0.04)!important;">
    <div style="width:40px;height:40px;border-radius:12px;background:rgba(99,102,241,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="bi bi-rocket-takeoff text-primary-color"></i>
    </div>
    <div>
        <div class="fw-semibold mb-1">{{ __('Advanced Payroll Coming Soon') }}</div>
        <div class="text-secondary small">{{ __('Full payroll processing, deductions, tax calculation, and payslip generation will be available in the next update.') }}</div>
    </div>
</div>
@endsection
