@extends('qayemtech.layout')

@section('title', 'Dashboard - QayemTech')
@section('body_class', 'bg-dark')

@section('styles')
<style>
    #hrTabs .nav-link {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-secondary);
        transition: all 0.3s ease;
    }

    #hrTabs .nav-link.active {
        background: #ffffff;
        color: var(--primary) !important;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .table-dark {
        --bs-table-bg: transparent;
    }

    .floating-chat-container {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
        cursor: grab;
    }

    .floating-chat-container:active {
        cursor: grabbing;
    }

    .floating-chat-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary);
        border: none;
        color: white;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
    }

    .floating-chat-btn:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .floating-chat-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 350px;
        height: 500px;
        min-width: 300px;
        min-height: 400px;
        max-width: 80vw;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px) scale(0.95);
        transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform-origin: bottom right;
        pointer-events: none;
        overflow: hidden;
        resize: both;
    }

    /* Resize handle indicator */
    .floating-chat-window::after {
        content: "";
        position: absolute;
        top: 5px;
        left: 5px;
        width: 15px;
        height: 15px;
        border-top: 2px solid rgba(255, 255, 255, 0.2);
        border-left: 2px solid rgba(255, 255, 255, 0.2);
        cursor: nw-resize;
    }

    .floating-chat-window.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }

    .ai-avatar-small {
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .chat-messages {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chat-bubble {
        max-width: 85%;
        padding: 10px 14px;
        border-radius: 18px;
        font-size: 0.85rem;
        line-height: 1.4;
    }

    .chat-bubble.ai {
        align-self: flex-start;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        border-bottom-left-radius: 4px;
    }

    .chat-bubble.user {
        align-self: flex-end;
        background: var(--primary);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .chat-bubble.loading {
        background: rgba(255, 255, 255, 0.03);
        font-style: italic;
        color: rgba(255, 255, 255, 0.5);
    }

    @keyframes animate-fade-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: animate-fade-in 0.3s ease forwards;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(79, 70, 229, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
        }
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    /* New Dashboard Enhancements */
    .dashboard-stat-card {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .dashboard-stat-card:hover {
        background: rgba(255, 255, 255, 0.07);
        transform: translateY(-5px);
        border-color: rgba(255, 255, 255, 0.15);
    }

    .dashboard-stat-card .icon-box {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }

    .dashboard-stat-card .bg-indigo {
        background: rgba(79, 70, 229, 0.15);
        color: #818cf8;
    }

    .dashboard-stat-card .bg-cyan {
        background: rgba(6, 182, 212, 0.15);
        color: #22d3ee;
    }

    .dashboard-stat-card .bg-emerald {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
    }

    .dashboard-stat-card .bg-amber {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
    }

    .dashboard-stat-card .stat-label {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dashboard-stat-card .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: white;
        margin-top: 0.25rem;
    }

    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<!-- Navbar -->
<nav class="qt-navbar">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <a class="navbar-brand me-0 me-md-2" href="{{ route('home') }}">{{ __('QayemTech') }}</a>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 d-none d-md-inline-block">{{ $company->name }} {{ __('Dashboard') }}</span>
        </div>
        <div class="d-flex align-items-center gap-3 gap-md-4">
            <div class="text-end d-none d-sm-block lh-1">
                <div class="text-white small fw-bold mb-1">{{ $user->name }}</div>
                <div class="text-secondary text-uppercase fw-medium" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                    @if($user instanceof \App\Models\Manager)
                    {{ $user->isGM() ? __('General Manager') : __('Department Manager') }}
                    @else
                    {{ __('HR Specialist') }}
                    @endif
                </div>
            </div>

            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-translate me-1"></i> {{ strtoupper(app()->getLocale()) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">English</a></li>
                    <li><a class="dropdown-item" href="{{ route('lang.switch', 'ar') }}">العربية</a></li>
                </ul>
            </div>

            <a href="{{ route('logout') }}" class="btn-logout-nav">
                <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4 mt-5">
    <!-- Quick Stats Row -->
    <div class="row g-4 mb-4 mt-3">
        <div class="col-md-3">
            <div class="dashboard-stat-card premium-card reveal-fade-up stagger-1">
                <div class="icon-box bg-indigo"><i class="bi bi-people-fill"></i></div>
                <div class="stat-label">{{ __('Total Employees') }}</div>
                <div class="stat-value">{{ count($employees) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-stat-card premium-card reveal-fade-up stagger-2">
                <div class="icon-box bg-cyan"><i class="bi bi-building"></i></div>
                <div class="stat-label">{{ __('Departments') }}</div>
                <div class="stat-value">{{ count($departments) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-stat-card premium-card reveal-fade-up stagger-3">
                <div class="icon-box bg-emerald"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-label">{{ __('Avg Performance') }}</div>
                <div class="stat-value">8.4<small class="text-secondary fw-normal fs-6">/10</small></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-stat-card premium-card reveal-fade-up stagger-4">
                <div class="icon-box bg-amber"><i class="bi bi-award-fill"></i></div>
                <div class="stat-label">{{ __('Active Plan') }}</div>
                @php
                $dashboardSub = $company->subscription('default');
                $activePlanName = ($dashboardSub && $dashboardSub->plan) ? $dashboardSub->plan->name : 'Advanced';
                @endphp
                <div class="stat-value">{{ __($activePlanName) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-stretch w-100 m-0">
        <!-- Company Info Card -->
        <div class="col-lg-4">
            <div class="glass-card premium-card h-100 p-4 reveal-fade-up d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 text-white">{{ __('Company Profile') }}</h5>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">{{ __('Active') }}</span>
                </div>

                <div class="mb-5 mt-3 text-center flex-grow-1 d-flex flex-column justify-content-center">
                    <div class="company-initials mb-3 mx-auto">{{ substr($company->name, 0, 1) }}</div>
                    <h3 class="text-white">{{ $company->name }}</h3>
                    <p class="text-secondary small">{{ $company->address }}</p>
                </div>

                <div class="list-group list-group-flush bg-transparent mt-auto">
                    <div class="list-group-item bg-transparent border-glass py-3 d-flex justify-content-between">
                        <span class="text-secondary">{{ __('Email') }}</span>
                        <span class="text-white">{{ $company->email }}</span>
                    </div>
                    <div class="list-group-item bg-transparent border-glass py-3 d-flex justify-content-between">
                        <span class="text-secondary">{{ __('Phone') }}</span>
                        <span class="text-white">{{ $company->phone }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan & Stats -->
        <div class="col-lg-8 d-flex flex-column gap-4">
            <div class="row g-4">
                <!-- Active Plan Card -->
                <div class="col-md-6">
                    <div class="glass-card premium-card p-4 reveal-fade-up h-100" style="animation-delay: 0.1s">
                        <h6 class="text-secondary text-uppercase smaller mb-3">{{ __('Subscription Details') }}</h6>
                        @php
                        $isGM = ($user instanceof \App\Models\Manager && $user->isGM());
                        $sub = $company->subscription('default');
                        @endphp
                        <div class="d-flex align-items-end gap-2 mb-3">
                            <h3 class="mb-0 text-white">{{ __($activePlanName) }}</h3>
                            <span class="text-primary-light small mb-2">/ {{ __('Yearly') }}</span>
                        </div>
                        <div class="progress bg-dark mb-2" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: 75%"></div>
                        </div>
                        <div class="text-secondary smaller">{{ __('Renewal date:') }} {{ $sub ? $sub->ends_at?->format('F d, Y') : 'Oct 14, 2026' }}</div>
                        <div class="mt-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3">{{ __('VIP Support') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Performance Chart Card -->
                <div class="col-md-6">
                    <div class="glass-card premium-card p-4 reveal-fade-up h-100" style="animation-delay: 0.2s">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-secondary text-uppercase smaller mb-0">{{ __('Performance Trend') }}</h6>
                            <i class="bi bi-bar-chart-fill text-primary"></i>
                        </div>
                        <div class="chart-container">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- HR Users Management (Only for GM) -->
                @if($isGM)
                <div class="col-12">
                    <div class="glass-card premium-card p-4 reveal-fade-up" style="animation-delay: 0.3s">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="text-white mb-0">{{ __('HR Team Management') }}</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addHrModal">
                                <i class="bi bi-plus-lg me-1"></i> {{ __('Add HR') }}
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover border-glass align-middle">
                                <thead>
                                    <tr class="text-secondary smaller text-uppercase">
                                        <th class="border-0 px-4">{{ __('Full Name') }}</th>
                                        <th class="border-0">{{ __('Email Address') }}</th>
                                        <th class="border-0">{{ __('Role') }}</th>
                                        <th class="border-0">{{ __('Created At') }}</th>
                                        <th class="border-0 text-end px-4">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hrs as $hr)
                                    <tr>
                                        <td class="px-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="hr-avatar">{{ substr($hr->name, 0, 1) }}</div>
                                                <div class="text-white fw-bold">{{ $hr->name }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $hr->email }}</td>
                                        <td>
                                            @if($hr->role === 'gm')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Owner / GM</span>
                                            @else
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">HR Specialist</span>
                                            @endif
                                        </td>
                                        <td class="text-secondary small">{{ $hr->created_at->format('M d, Y') }}</td>
                                        <td class="text-end px-4">
                                            @if($hr->id !== $user->id)
                                            <button class="btn btn-sm btn-outline-light border-glass rounded-pill px-3"
                                                onclick="openEditHrModal('{{ $hr->id }}', '{{ addslashes($hr->name) }}', '{{ $hr->email }}')">
                                                {{ __('Manage') }}
                                            </button>
                                            @else
                                            <span class="text-secondary smaller">{{ __('Current User') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-12 flex-grow-1 d-flex flex-column">
                    <div class="glass-card premium-card reveal-fade-up flex-grow-1 d-flex flex-column" style="animation-delay: 0.3s">
                        <div class="card-header border-glass bg-transparent p-0">
                            <ul class="nav nav-tabs border-0 p-2 gap-2" id="hrTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active rounded-pill px-4 border-0" id="depts-tab" data-bs-toggle="tab" data-bs-target="#depts" type="button">{{ __('Departments') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill px-4 border-0" id="managers-tab" data-bs-toggle="tab" data-bs-target="#managers" type="button">{{ __('Dept Managers') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill px-4 border-0" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees" type="button">{{ __('Employees') }}</button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content p-4" id="hrTabsContent">
                            <!-- Departments Tab -->
                            <div class="tab-pane fade show active" id="depts" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="text-secondary text-uppercase smaller mb-0">{{ __('Company Departments') }}</h6>
                                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addDeptModal">
                                        <i class="bi bi-plus-lg me-1"></i> {{ __('Add Dept') }}
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover border-glass align-middle">
                                        <thead>
                                            <tr class="text-secondary smaller text-uppercase">
                                                <th class="border-0 px-4">{{ __('Name') }}</th>
                                                <th class="border-0">{{ __('Manager') }}</th>
                                                <th class="border-0">{{ __('Staff Count') }}</th>
                                                <th class="border-0 text-end px-4">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($departments as $dept)
                                            <tr>
                                                <td class="px-4 text-white fw-bold">{{ $dept->name }}</td>
                                                <td>{{ $dept->manager ? $dept->manager->name : __('No Manager') }}</td>
                                                <td>{{ $dept->employees_count ?? count($dept->employees) }}</td>
                                                <td class="text-end px-4">
                                                    <button class="btn btn-sm btn-outline-light border-glass rounded-pill px-3"
                                                        data-id="{{ $dept->id }}" data-name="{{ addslashes($dept->name) }}" data-manager="{{ $dept->manager_id }}"
                                                        onclick="openEditDeptModal(this.getAttribute('data-id'), this.getAttribute('data-name'), this.getAttribute('data-manager'))">
                                                        {{ __('Edit') }}
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Managers Tab -->
                            <div class="tab-pane fade" id="managers" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="text-secondary text-uppercase smaller mb-0">{{ __('Department Managers') }}</h6>
                                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addManagerModal">
                                        <i class="bi bi-plus-lg me-1"></i> {{ __('Add Manager') }}
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover border-glass align-middle">
                                        <thead>
                                            <tr class="text-secondary smaller text-uppercase">
                                                <th class="border-0 px-4">{{ __('Name') }}</th>
                                                <th class="border-0">{{ __('Email') }}</th>
                                                <th class="border-0">{{ __('Dept Assigned') }}</th>
                                                <th class="border-0 text-center">{{ __('Hire Date') }}</th>
                                                <th class="border-0 text-center">{{ __('Done / Req') }}</th>
                                                <th class="border-0 text-end px-4">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($managers as $m)
                                            <tr>
                                                <td class="px-4">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="hr-avatar">{{ substr($m->name, 0, 1) }}</div>
                                                        <div class="text-white fw-bold">{{ $m->name }}</div>
                                                    </div>
                                                </td>
                                                <td class="text-secondary">{{ $m->email }}</td>
                                                <td>
                                                    @if($m->department)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary-light">{{ $m->department->name }}</span>
                                                    @else
                                                    <span class="text-secondary smaller italic">{{ __('Unassigned') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center text-secondary small">{{ $m->hire_date ? $m->hire_date->format('Y-m-d') : __('N/A') }}</td>
                                                <td class="text-center">
                                                    <span class="text-success fw-bold">{{ $m->tasks_completed }}</span>
                                                    <span class="text-secondary mx-1">/</span>
                                                    <span class="text-primary-light">{{ $m->tasks_requested }}</span>
                                                </td>
                                                <td class="text-end px-4">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button class="btn btn-sm btn-outline-light border-glass rounded-pill px-3"
                                                            data-type="manager" data-id="{{ $m->id }}"
                                                            onclick="openProfileModal(this.getAttribute('data-type'), this.getAttribute('data-id'))">
                                                            {{ __('Profile') }}
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-primary border-glass rounded-pill px-3"
                                                            data-id="{{ $m->id }}" data-name="{{ $m->name }}" data-type="manager"
                                                            data-hire-date="{{ $m->hire_date ? $m->hire_date->format('Y-m-d') : '' }}"
                                                            data-tasks-req="{{ $m->tasks_requested }}" data-tasks-done="{{ $m->tasks_completed }}"
                                                            onclick="openMetricsModal(this)">
                                                            {{ __('Update') }}
                                                        </button>
                                                        <button class="btn btn-sm btn-success rounded-pill px-3"
                                                            data-url="{{ route('manager.evaluate', $m->id) }}"
                                                            onclick="runAI(this.getAttribute('data-url'))">
                                                            {{ __('AI') }}
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Employees Tab -->
                            <div class="tab-pane fade" id="employees" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="text-secondary text-uppercase smaller mb-0">{{ __('Company Staff') }}</h6>
                                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                                        <i class="bi bi-plus-lg me-1"></i> {{ __('Add Employee') }}
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover border-glass align-middle">
                                        <thead>
                                            <tr class="text-secondary smaller text-uppercase">
                                                <th class="border-0 px-4">{{ __('Name') }}</th>
                                                <th class="border-0">{{ __('Position / Dept') }}</th>
                                                <th class="border-0 text-center">{{ __('Hire Date') }}</th>
                                                <th class="border-0 text-center">{{ __('Done / Req') }}</th>
                                                <th class="border-0 text-end px-4">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employees as $emp)
                                            <tr>
                                                <td class="px-4 text-white fw-bold">{{ $emp->name }}</td>
                                                <td class="text-secondary">
                                                    {{ __($emp->job_title) }}
                                                    <div class="smaller opacity-50">{{ $emp->department ? $emp->department->name : __('N/A') }}</div>
                                                </td>
                                                <td class="text-center text-secondary">{{ $emp->attendance_rate ?? 0 }}%</td>
                                                <td class="text-center text-secondary small">{{ $emp->hire_date ? $emp->hire_date->format('Y-m-d') : __('N/A') }}</td>
                                                <td class="text-center">
                                                    <span class="text-success fw-bold">{{ $emp->tasks_completed }}</span>
                                                    <span class="text-secondary mx-1">/</span>
                                                    <span class="text-primary-light">{{ $emp->tasks_requested }}</span>
                                                </td>
                                                <td class="text-end px-4">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button class="btn btn-sm btn-outline-light border-glass rounded-pill px-3"
                                                            data-type="employee" data-id="{{ $emp->id }}"
                                                            onclick="openProfileModal(this.getAttribute('data-type'), this.getAttribute('data-id'))">
                                                            {{ __('Profile') }}
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-primary border-glass rounded-pill px-3"
                                                            data-id="{{ $emp->id }}" data-name="{{ $emp->name }}" data-type="employee"
                                                            data-hire-date="{{ $emp->hire_date ? $emp->hire_date->format('Y-m-d') : '' }}"
                                                            data-attendance="{{ $emp->attendance_rate }}" data-tasks-req="{{ $emp->tasks_requested }}" data-tasks-done="{{ $emp->tasks_completed }}"
                                                            onclick="openMetricsModal(this)">
                                                            {{ __('Update') }}
                                                        </button>
                                                        <button class="btn btn-sm btn-success rounded-pill px-3"
                                                            data-url="{{ route('employee.evaluate', $emp->id) }}"
                                                            onclick="runAI(this.getAttribute('data-url'))">
                                                            {{ __('AI') }}
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

<!-- Add HR Modal (GM Only) -->
<div class="modal fade" id="addHrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-glass p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Add HR Account') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addHrForm">
                @csrf
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Name') }}" required>
                        <label>{{ __('Full Name') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" placeholder="{{ __('Email') }}" required>
                        <label>{{ __('Email Address') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required minlength="6">
                        <label>{{ __('Password') }}</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Create HR Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit HR Modal (GM Only) -->
<div class="modal fade" id="editHrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-glass p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Edit HR Account') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editHrForm">
                @csrf
                <input type="hidden" name="id" id="editHrId">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" id="editHrName" class="form-control" placeholder="{{ __('Name') }}" required>
                        <label>{{ __('Full Name') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" id="editHrEmail" class="form-control" placeholder="{{ __('Email') }}" required>
                        <label>{{ __('Email Address') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" placeholder="{{ __('Password (Leave blank to keep current)') }}" minlength="6">
                        <label>{{ __('New Password (Optional)') }}</label>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" onclick="deleteHrAccount()">
                            <i class="bi bi-trash3 me-1"></i> {{ __('Delete Account') }}
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Dept Modal -->
<div class="modal fade" id="addDeptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-glass p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Add New Department') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addDeptForm">
                @csrf
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Department Name') }}" required>
                        <label>{{ __('Department Name') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select name="manager_id" class="form-select">
                            <option value="">{{ __('Select Manager (Optional)') }}</option>
                            @foreach($managers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                        <label>{{ __('Department Manager') }}</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Create Department') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Manager Modal -->
<div class="modal fade" id="addManagerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-glass p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Add Department Manager') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addManagerForm">
                @csrf
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Name') }}" required>
                        <label>{{ __('Full Name') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" placeholder="{{ __('Email') }}" required>
                        <label>{{ __('Email Address') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required>
                        <label>{{ __('Password') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select name="department_id" class="form-select">
                            <option value="">{{ __('Assign to Department (Optional)') }}</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        <label>{{ __('Department') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="date" name="hire_date" class="form-control" placeholder="{{ __('Hire Date') }}">
                        <label>{{ __('Hire Date') }}</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Create Manager') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-glass p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Register New Employee') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addEmployeeForm">
                @csrf
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Name') }}" required>
                        <label>{{ __('Full Name') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" placeholder="{{ __('Email Address') }}" required>
                        <label>{{ __('Email Address') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select name="department_id" class="form-select" required>
                            <option value="">{{ __('Select Department') }}</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        <label>{{ __('Department') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="job_title" class="form-control" placeholder="{{ __('Job Title') }}">
                        <label>{{ __('Job Title') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="date" name="hire_date" class="form-control" placeholder="{{ __('Hire Date') }}">
                        <label>{{ __('Hire Date') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required minlength="6">
                        <label>{{ __('Password (for Employee Login)') }}</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Register Employee') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Dept Modal -->
<div class="modal fade" id="editDeptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-glass p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Edit Department') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDeptForm">
                @csrf
                <input type="hidden" name="id" id="editDeptId">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" id="editDeptName" class="form-control" placeholder="{{ __('Department Name') }}" required>
                        <label>{{ __('Department Name') }}</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select name="manager_id" id="editDeptManagerId" class="form-select">
                            <option value="">{{ __('Select Manager (Optional)') }}</option>
                            @foreach($managers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                        <label>{{ __('Department Manager') }}</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Metrics Modal -->
<div class="modal fade" id="metricsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-glass p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Update Performance Data:') }} <span id="metricsEmployeeName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateMetricsForm">
                @csrf
                <input type="hidden" name="id" id="metricsId">
                <input type="hidden" name="type" id="metricsType">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" id="attendanceFieldContainer">
                                <input type="number" name="attendance_rate" id="metricsAttendance" class="form-control" placeholder="{{ __('Attendance') }}" min="0" max="100">
                                <label>{{ __('Attendance Rate (%)') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="date" name="hire_date" id="metricsHireDate" class="form-control" placeholder="{{ __('Hire Date') }}">
                                <label>{{ __('Hire Date') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-floating mb-3">
                                <input type="number" name="tasks_requested" id="metricsRequested" class="form-control" placeholder="{{ __('Tasks Requested') }}" min="0" required>
                                <label>{{ __('Tasks Requested') }}</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating mb-3">
                                <input type="number" name="tasks_completed" id="metricsCompleted" class="form-control" placeholder="{{ __('Tasks Completed') }}" min="0" required>
                                <label>{{ __('Tasks Completed') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success rounded-pill px-4">{{ __('Save Performance Data') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border-glass p-4 overflow-hidden">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white">{{ __('Professional Profile') }}</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light border-glass rounded-pill px-3 py-1 d-flex align-items-center gap-1" onclick="printReport()">
                        <i class="bi bi-printer"></i> <span class="d-none d-sm-inline">{{ __('Print') }}</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary border-primary border-opacity-50 rounded-pill px-3 py-1 d-flex align-items-center gap-1" onclick="downloadReport()">
                        <i class="bi bi-download"></i> <span class="d-none d-sm-inline">{{ __('PDF') }}</span>
                    </button>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body pt-4" id="printableReportArea">
                <div class="row g-4">
                    <div class="col-md-4 text-center border-end border-glass">
                        <div class="hr-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;" id="profAvatar">?</div>
                        <h4 class="text-white mb-1" id="profName">-</h4>
                        <p class="text-primary-light small mb-3" id="profTitle">-</p>
                        <hr class="border-glass my-3">
                        <div class="text-start px-2">
                            <div class="mb-2">
                                <label class="text-secondary smaller text-uppercase d-block">{{ __('Department') }}</label>
                                <span class="text-white" id="profDept">-</span>
                            </div>
                            <div class="mb-2">
                                <label class="text-secondary smaller text-uppercase d-block">{{ __('Hire Date') }}</label>
                                <span class="text-white" id="profHireDate">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-white mb-0">{{ __('AI Performance Report') }}</h6>
                            <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 p-2" id="profScoreContainer">
                                {{ __('Score:') }} <span id="profScore">-</span>/10
                            </div>
                        </div>

                        <!-- Progress Stats -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between smaller mb-1">
                                <span class="text-secondary">{{ __('Task Completion Rate') }}</span>
                                <span class="text-white fw-bold" id="profTaskRate">0%</span>
                            </div>
                            <div class="progress bg-dark" style="height: 8px;">
                                <div class="progress-bar bg-primary" id="profProgressBar" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Report Content -->
                        <div class="report-sections" id="profReportContent">
                            <div class="bg-dark bg-opacity-50 rounded-3 p-4 mb-3 border border-glass border-start border-warning border-4 animate-up shadow-sm">
                                <h6 class="text-warning mb-2 d-flex align-items-center gap-2"><i class="bi bi-star-fill"></i> {{ __('Key Strengths') }}</h6>
                                <p class="text-light small mb-0 lh-lg" id="profStrengths">{{ __('No evaluation data yet.') }}</p>
                            </div>
                            <div class="bg-dark bg-opacity-50 rounded-3 p-4 mb-3 border border-glass border-start border-danger border-4 animate-up shadow-sm" style="animation-delay: 0.1s">
                                <h6 class="text-danger mb-2 d-flex align-items-center gap-2"><i class="bi bi-graph-down-arrow"></i> {{ __('Areas for Improvement') }}</h6>
                                <p class="text-light small mb-0 lh-lg" id="profWeaknesses">{{ __('No evaluation data yet.') }}</p>
                            </div>
                            <div class="bg-dark bg-opacity-50 rounded-3 p-4 border border-glass border-start border-info border-4 animate-up shadow-sm" style="animation-delay: 0.2s">
                                <h6 class="text-info mb-2 d-flex align-items-center gap-2"><i class="bi bi-lightbulb-fill"></i> {{ __('Growth Recommendations') }}</h6>
                                <p class="text-light small mb-0 lh-lg" id="profRecommendations">{{ __('No evaluation data yet.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating AI Chat Widget -->
<div class="floating-chat-container">
    <div class="floating-chat-window glass-card" id="floatingChatWindow">
        <div class="chat-header p-3 border-glass d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="ai-avatar-small"><i class="bi bi-robot"></i></div>
                <div>
                    <div class="text-white fw-bold smaller">Qayem AI</div>
                    <div class="text-success smaller" style="font-size: 0.7rem;"><i class="bi bi-circle-fill me-1"></i> {{ __('Online') }}</div>
                </div>
            </div>
            <button class="btn btn-sm btn-link text-secondary p-0" onclick="document.getElementById('floatingChatWindow').classList.remove('active')">
                <i class="bi bi-dash-lg"></i>
            </button>
        </div>
        <div class="chat-messages p-3 flex-grow-1 overflow-auto custom-scrollbar" id="chatMessages">
            <div class="chat-bubble ai animate-fade-in shadow-sm">
                {{ __('Hello :name! 👋 How can I help you today?', ['name' => explode(' ', $user->name)[0]]) }}
            </div>
        </div>
        <div class="chat-input-area p-3 border-glass">
            <form id="aiChatForm" class="d-flex gap-2">
                @csrf
                <input type="text" id="aiMessageInput" class="form-control bg-dark border-glass text-white rounded-pill px-3" placeholder="{{ __('Ask anything...') }}" required autocomplete="off">
                <button type="submit" class="btn btn-primary rounded-circle p-0" style="width: 40px; height: 40px;">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>
    <button class="floating-chat-btn shadow-lg pulse-animation" id="floatingChatBtn" style="z-index: 10000;" title="AI Assistant">
        <i class="bi bi-chat-dots-fill"></i>
    </button>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@php
$qayemConfig = [
'csrfToken' => csrf_token(),
'routes' => [
'hrStore' => route("hr.store"),
'hrUpdate' => route("hr.update"),
'hrDelete' => route("hr.delete"),
'deptStore' => route("dept.store"),
'deptUpdate' => route("dept.update"),
'managerStore' => route("manager.store"),
'employeeStore' => route("employee.store"),
'employeeMetrics' => route("employee.metrics"),
'managerMetrics' => route("manager.metrics"),
'aiChat' => route("ai.chat"),
'profileGet' => route("profile.get")
],
'translations' => [
'processing' => __("Processing..."),
'errorOccurred' => __("Error occurred"),
'networkError' => __("Network error")
],
'_i18n' => [
'thinking' => __("Thinking..."),
'errorMsg' => __("Sorry, I encountered an error.")
],
'chartData' => [
'label' => __("Efficiency"),
'labels' => [__("Jan"), __("Feb"), __("Mar"), __("Apr"), __("May"), __("Jun")]
]
];
@endphp
<script>
    window.QayemConfig = @json($qayemConfig);
</script>
<script src="{{ asset('qayemtech/js/dashboard.js') }}"></script>
@endsection