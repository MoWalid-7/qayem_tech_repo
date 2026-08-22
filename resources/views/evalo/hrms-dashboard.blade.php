@extends('evalo.hrms-layout')

@section('title', 'Dashboard — ' . $company->name)
@section('page_title', __('Dashboard'))

@section('styles')
<style>
/* Quick clock widget */
.live-clock {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-primary);
    font-variant-numeric: tabular-nums;
    letter-spacing: -1px;
    line-height: 1;
}
.live-date-sub { font-size: 0.72rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; }

/* Performance ring */
.perf-ring-wrap {
    position: relative;
    width: 110px;
    height: 110px;
    margin: 0 auto;
}

.perf-ring-wrap canvas { position: absolute; top: 0; left: 0; }

.perf-center-text {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.perf-center-text .val {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
}

.perf-center-text .lbl {
    font-size: 0.55rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Welcome Banner */
.welcome-banner {
    background: linear-gradient(135deg, rgba(99,102,241,0.18) 0%, rgba(6,182,212,0.10) 100%);
    border: 1px solid rgba(99,102,241,0.25);
    border-radius: 20px;
    padding: 1.5rem 2rem;
    position: relative;
    overflow: hidden;
}

.welcome-banner::after {
    content: '\F2B9';
    font-family: 'bootstrap-icons';
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 5rem;
    opacity: 0.06;
    color: #fff;
}

[dir="rtl"] .welcome-banner::after { right: auto; left: 1.5rem; }

/* Employee row in table */
.emp-name-cell { display: flex; align-items: center; gap: 0.75rem; }
</style>
@endsection

@section('content')
@php
$isGM  = ($user instanceof \App\Models\Manager && $user->isGM());
$isDM  = ($user instanceof \App\Models\Manager && $user->isDM());
$isHR  = ($user instanceof \App\Models\HrUser);
@endphp

{{-- ═══ Welcome Banner ═══ --}}
<div class="welcome-banner mb-4 animate-up">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="text-secondary smaller mb-1" style="font-size:.7rem; text-transform:uppercase; letter-spacing:1px;">
                {{ now()->hour < 12 ? __('Good Morning') : (now()->hour < 18 ? __('Good Afternoon') : __('Good Evening')) }}
            </div>
            <h3 class="text-white fw-bold mb-1">{{ $user->name }}</h3>
            <div class="text-secondary small">
                @if($isGM)  {{ __('General Manager') }} — {{ $company->name }}
                @elseif($isDM) {{ __('Department Manager') }} — {{ $user->department?->name }}
                @else {{ __('HR Specialist') }} — {{ $company->name }}
                @endif
            </div>
        </div>
        <div class="text-end">
            <div class="live-clock" id="liveClock">--:--:--</div>
            <div class="live-date-sub" id="liveDate">{{ now()->translatedFormat('l, d M Y') }}</div>
        </div>
    </div>
</div>

{{-- ═══ Stat Cards ═══ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card indigo animate-up stagger-1">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ count($employees) }}</div>
            <div class="stat-label">{{ __('Employees') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card cyan animate-up stagger-2">
            <div class="stat-icon"><i class="bi bi-building"></i></div>
            <div class="stat-value">{{ count($departments) }}</div>
            <div class="stat-label">{{ __('Departments') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card emerald animate-up stagger-3">
            <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="stat-value">{{ number_format($avgPerformance, 1) }}<small class="text-secondary fw-normal" style="font-size:0.9rem">/10</small></div>
            <div class="stat-label">{{ __('Avg Performance') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card amber animate-up stagger-4">
            <div class="stat-icon"><i class="bi bi-fingerprint"></i></div>
            <div class="stat-value">{{ count($attendances ?? []) }}</div>
            <div class="stat-label">{{ __('Checked In Today') }}</div>
        </div>
    </div>
</div>

{{-- ═══ Main Content Row ═══ --}}
<div class="row g-4">

    {{-- Performance Chart --}}
    <div class="col-lg-5">
        <div class="glass-card p-4 h-100 animate-up stagger-1">
            <div class="section-header mb-4">
                <div class="section-title">{{ __('Performance Trend') }}</div>
                <span class="badge-pill cyan"><i class="bi bi-lightning-charge-fill"></i> {{ __('Live') }}</span>
            </div>
            <div class="row g-3 align-items-center mb-4">
                <div class="col-auto">
                    <div class="perf-ring-wrap">
                        <canvas id="perfRingChart" width="110" height="110"></canvas>
                        <div class="perf-center-text">
                            <div class="val">{{ number_format($avgPerformance, 1) }}</div>
                            <div class="lbl">/ 10</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="text-white fw-bold mb-1">{{ $company->name }}</div>
                    <div class="text-secondary small mb-3">{{ __('Overall company performance score') }}</div>
                    <div class="modern-progress">
                        <div class="bar" style="width: {{ $avgPerformance * 10 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Right panel: tabs for data --}}
    <div class="col-lg-7">
        <div class="glass-card p-0 h-100 animate-up stagger-2 d-flex flex-column" style="overflow:hidden;">

            {{-- Tabs Header --}}
            <div class="p-3 border-bottom" style="border-color: var(--glass-border) !important;">
                <div class="modern-tabs" id="dashTabs">
                    @if(!$isDM)
                    <button class="tab-btn active" data-target="#tab-employees">{{ __('Employees') }}</button>
                    @endif
                    @if(!$isDM)
                    <button class="tab-btn" data-target="#tab-managers">{{ __('Managers') }}</button>
                    @endif
                    <button class="tab-btn @if($isDM) active @endif" data-target="#tab-departments">{{ $isDM ? __('My Dept') : __('Departments') }}</button>
                    <button class="tab-btn" data-target="#tab-attendance">
                        <i class="bi bi-fingerprint me-1"></i>{{ __('Attendance') }}
                    </button>
                </div>
            </div>

            {{-- Tab Content --}}
            <div class="flex-grow-1 overflow-auto p-3">

                {{-- Employees Tab --}}
                @if(!$isDM)
                <div id="tab-employees" class="tab-panel active">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title">{{ __('Company Staff') }}</div>
                        @if(!$isDM)
                        <button class="btn-modern primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                            <i class="bi bi-plus-lg"></i> {{ __('Add Employee') }}
                        </button>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Position') }}</th>
                                    <th class="text-center">{{ __('Attend.') }}</th>
                                    <th class="text-center">{{ __('Score') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $emp)
                                <tr>
                                    <td>
                                        <div class="emp-name-cell">
                                            <div class="avatar" style="background: linear-gradient(135deg, hsl({{ crc32($emp->name) % 360 }}, 60%, 50%), hsl({{ (crc32($emp->name) + 60) % 360 }}, 70%, 40%))">
                                                {{ strtoupper(substr($emp->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-white fw-600" style="font-size:.85rem; font-weight:600">{{ $emp->name }}</div>
                                                <div class="text-secondary" style="font-size:.7rem">{{ $emp->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:.8rem; color:var(--text-primary)">{{ $emp->job_title ?? '—' }}</div>
                                        <div style="font-size:.68rem; color:var(--text-secondary)">{{ $emp->department?->name ?? __('N/A') }}</div>
                                    </td>
                                    <td class="text-center">
                                        @php $ar = $emp->attendance_rate ?? 0; @endphp
                                        <span class="badge-pill {{ $ar >= 80 ? 'success' : ($ar >= 50 ? 'warning' : 'danger') }}">{{ $ar }}%</span>
                                    </td>
                                    <td class="text-center">
                                        @php $lastEval = $emp->evaluations ? $emp->evaluations->sortByDesc('created_at')->first() : null; @endphp
                                        @if($lastEval)
                                        <span class="badge-pill {{ $lastEval->score >= 8 ? 'success' : ($lastEval->score >= 6 ? 'cyan' : ($lastEval->score >= 4 ? 'warning' : 'danger')) }}">
                                            {{ $lastEval->score }}/10
                                        </span>
                                        @else
                                        <span class="text-secondary" style="font-size:.75rem">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <button class="btn-modern ghost py-1 px-2"
                                                data-type="employee" data-id="{{ $emp->id }}"
                                                onclick="openProfileModal(this.getAttribute('data-type'), this.getAttribute('data-id'))"
                                                style="font-size:.75rem">
                                                <i class="bi bi-person-lines-fill"></i>
                                            </button>
                                            <button class="btn-modern ghost py-1 px-2"
                                                data-id="{{ $emp->id }}" data-name="{{ $emp->name }}" data-type="employee"
                                                data-hire-date="{{ $emp->hire_date ? $emp->hire_date->format('Y-m-d') : '' }}"
                                                data-attendance="{{ $emp->attendance_rate }}" data-tasks-req="{{ $emp->tasks_requested }}" data-tasks-done="{{ $emp->tasks_completed }}"
                                                onclick="openMetricsModal(this)"
                                                style="font-size:.75rem">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn-modern success py-1 px-2"
                                                data-url="{{ route('employee.evaluate', $emp->id) }}"
                                                onclick="runAI(this.getAttribute('data-url'))"
                                                style="font-size:.75rem">
                                                <i class="bi bi-stars"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Managers Tab --}}
                @if(!$isDM)
                <div id="tab-managers" class="tab-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title">{{ __('Department Managers') }}</div>
                        <button class="btn-modern primary" data-bs-toggle="modal" data-bs-target="#addManagerModal">
                            <i class="bi bi-plus-lg"></i> {{ __('Add Manager') }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th class="text-center">{{ __('Attend.') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($managers as $m)
                                <tr>
                                    <td>
                                        <div class="emp-name-cell">
                                            <div class="avatar" style="background: linear-gradient(135deg, hsl({{ crc32($m->name) % 360 }}, 55%, 45%), hsl({{ (crc32($m->name) + 90) % 360 }}, 65%, 35%))">
                                                {{ strtoupper(substr($m->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-white" style="font-size:.85rem; font-weight:600">{{ $m->name }}</div>
                                                <div class="text-secondary" style="font-size:.7rem">{{ $m->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($m->department)
                                        <span class="badge-pill primary">{{ $m->department->name }}</span>
                                        @else
                                        <span class="text-secondary" style="font-size:.75rem">{{ __('Unassigned') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php $mar = $m->attendance_rate ?? 0; @endphp
                                        <span class="badge-pill {{ $mar >= 80 ? 'success' : ($mar >= 50 ? 'warning' : 'danger') }}">{{ $mar }}%</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <button class="btn-modern ghost py-1 px-2"
                                                data-type="manager" data-id="{{ $m->id }}"
                                                onclick="openProfileModal(this.getAttribute('data-type'), this.getAttribute('data-id'))"
                                                style="font-size:.75rem">
                                                <i class="bi bi-person-lines-fill"></i>
                                            </button>
                                            <button class="btn-modern ghost py-1 px-2"
                                                data-id="{{ $m->id }}" data-name="{{ $m->name }}" data-type="manager"
                                                data-attendance="{{ $m->attendance_rate }}"
                                                data-hire-date="{{ $m->hire_date ? $m->hire_date->format('Y-m-d') : '' }}"
                                                data-tasks-req="{{ $m->tasks_requested }}" data-tasks-done="{{ $m->tasks_completed }}"
                                                onclick="openMetricsModal(this)"
                                                style="font-size:.75rem">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn-modern success py-1 px-2"
                                                data-url="{{ route('manager.evaluate', $m->id) }}"
                                                onclick="runAI(this.getAttribute('data-url'))"
                                                style="font-size:.75rem">
                                                <i class="bi bi-stars"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Departments Tab --}}
                <div id="tab-departments" class="tab-panel @if($isDM) active @endif">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title">{{ $isDM ? __('My Department') : __('Departments') }}</div>
                        @if(!$isDM)
                        <button class="btn-modern primary" data-bs-toggle="modal" data-bs-target="#addDeptModal">
                            <i class="bi bi-plus-lg"></i> {{ __('Add Dept') }}
                        </button>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Manager') }}</th>
                                    <th class="text-center">{{ __('Staff') }}</th>
                                    @if(!$isDM)<th class="text-end">{{ __('Actions') }}</th>@endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $dept)
                                <tr>
                                    <td class="text-white fw-bold" style="font-size:.875rem">{{ $dept->name }}</td>
                                    <td>
                                        @if($dept->manager)
                                        <div class="emp-name-cell">
                                            <div class="avatar" style="width:28px;height:28px;font-size:.7rem;border-radius:8px">{{ substr($dept->manager->name,0,1) }}</div>
                                            <span style="font-size:.8rem">{{ $dept->manager->name }}</span>
                                        </div>
                                        @else
                                        <span class="text-secondary" style="font-size:.8rem">{{ __('No Manager') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill cyan">{{ $dept->employees_count ?? count($dept->employees) }}</span>
                                    </td>
                                    @if(!$isDM)
                                    <td class="text-end">
                                        <button class="btn-modern ghost py-1 px-2"
                                            data-id="{{ $dept->id }}" data-name="{{ addslashes($dept->name) }}" data-manager="{{ $dept->manager_id }}"
                                            onclick="openEditDeptModal(this.getAttribute('data-id'), this.getAttribute('data-name'), this.getAttribute('data-manager'))"
                                            style="font-size:.75rem">
                                            <i class="bi bi-pencil"></i> {{ __('Edit') }}
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Attendance Tab --}}
                <div id="tab-attendance" class="tab-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title">
                            <i class="bi bi-fingerprint me-1"></i>{{ __("Today's Attendance") }}
                        </div>
                        <span class="badge-pill emerald">{{ count($attendances ?? []) }} {{ __('Present') }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th class="text-center">{{ __('Check In') }}</th>
                                    <th class="text-center">{{ __('Check Out') }}</th>
                                    <th class="text-center">{{ __('Hours') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances ?? [] as $att)
                                <tr>
                                    <td>
                                        <div class="emp-name-cell">
                                            <div class="avatar" style="width:32px;height:32px;font-size:.8rem;border-radius:9px">
                                                {{ strtoupper(substr($att->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <span class="text-white" style="font-size:.85rem">{{ $att->user->name ?? __('Unknown') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-pill primary" style="font-size:.65rem">{{ str_replace('App\\Models\\', '', $att->user_type) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill success" style="font-size:.7rem">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                            {{ $att->check_in ? $att->check_in->format('h:i A') : '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($att->check_out)
                                        <span class="badge-pill danger" style="font-size:.7rem">
                                            <i class="bi bi-box-arrow-left"></i>
                                            {{ $att->check_out->format('h:i A') }}
                                        </span>
                                        @else
                                        <span class="text-secondary" style="font-size:.75rem">{{ __('Still In') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($att->check_in && $att->check_out)
                                        <span style="font-size:.8rem; color:var(--cyan)">
                                            {{ number_format($att->check_in->diffInMinutes($att->check_out) / 60, 1) }}h
                                        </span>
                                        @else
                                        <span class="text-secondary">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-fingerprint" style="font-size:2.5rem; color:var(--text-muted); display:block; margin-bottom:.75rem"></i>
                                        <div class="text-secondary small">{{ __('No attendance records for today') }}</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>{{-- end tab content --}}
        </div>
    </div>

    {{-- GM: HR Team Section --}}
    @if($isGM)
    <div class="col-12">
        <div class="glass-card p-4 animate-up">
            <div class="section-header">
                <div class="section-title">{{ __('HR Team Management') }}</div>
                <button class="btn-modern primary" data-bs-toggle="modal" data-bs-target="#addHrModal">
                    <i class="bi bi-plus-lg"></i> {{ __('Add HR') }}
                </button>
            </div>
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Joined') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hrs as $hr)
                        <tr>
                            <td>
                                <div class="emp-name-cell">
                                    <div class="avatar" style="background: linear-gradient(135deg, #7c3aed, #4338ca)">{{ strtoupper(substr($hr->name,0,1)) }}</div>
                                    <span class="text-white" style="font-size:.875rem; font-weight:600">{{ $hr->name }}</span>
                                </div>
                            </td>
                            <td style="font-size:.8rem">{{ $hr->email }}</td>
                            <td>
                                @if($hr->role === 'gm')
                                <span class="badge-pill danger">{{ __('Owner / GM') }}</span>
                                @else
                                <span class="badge-pill cyan">{{ __('HR Specialist') }}</span>
                                @endif
                            </td>
                            <td style="font-size:.8rem; color:var(--text-secondary)">{{ $hr->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                @if($hr->id !== $user->id)
                                <button class="btn-modern ghost py-1"
                                    onclick="openEditHrModal('{{ $hr->id }}', '{{ addslashes($hr->name) }}', '{{ $hr->email }}')"
                                    style="font-size:.78rem">
                                    <i class="bi bi-pencil"></i> {{ __('Manage') }}
                                </button>
                                @else
                                <span class="text-secondary" style="font-size:.75rem">{{ __('You') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>{{-- end main row --}}

{{-- ═══ Modals (keeping same logic, updated styling) ═══ --}}

{{-- Add HR Modal --}}
<div class="modal fade" id="addHrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Add HR Account') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addHrForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Full Name') }}" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="{{ __('Email Address') }}" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern primary w-100">{{ __('Create HR Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit HR Modal --}}
<div class="modal fade" id="editHrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Edit HR Account') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editHrForm">
                @csrf
                <input type="hidden" name="id" id="editHrId">
                <div class="modal-body p-4">
                    <div class="mb-3"><input type="text" name="name" id="editHrName" class="form-control" placeholder="{{ __('Full Name') }}" required></div>
                    <div class="mb-3"><input type="email" name="email" id="editHrEmail" class="form-control" placeholder="{{ __('Email Address') }}" required></div>
                    <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="{{ __('New Password (Optional)') }}" minlength="6"></div>
                    <button type="button" class="btn-modern danger w-100" onclick="deleteHrAccount()">
                        <i class="bi bi-trash3"></i> {{ __('Delete Account') }}
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern primary w-100">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Dept Modal --}}
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Add New Department') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addDeptForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="{{ __('Department Name') }}" required></div>
                    <div class="mb-3">
                        <select name="manager_id" class="form-select">
                            <option value="">{{ __('Select Manager (Optional)') }}</option>
                            @foreach($managers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern primary w-100">{{ __('Create Department') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Dept Modal --}}
<div class="modal fade" id="editDeptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Edit Department') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDeptForm">
                @csrf
                <input type="hidden" name="id" id="editDeptId">
                <div class="modal-body p-4">
                    <div class="mb-3"><input type="text" name="name" id="editDeptName" class="form-control" placeholder="{{ __('Department Name') }}" required></div>
                    <div class="mb-3">
                        <select name="manager_id" id="editDeptManagerId" class="form-select">
                            <option value="">{{ __('Select Manager (Optional)') }}</option>
                            @foreach($managers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern primary w-100">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Manager Modal --}}
<div class="modal fade" id="addManagerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Add Department Manager') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addManagerForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="{{ __('Full Name') }}" required></div>
                    <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="{{ __('Email Address') }}" required></div>
                    <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required></div>
                    <div class="mb-3">
                        <select name="department_id" class="form-select">
                            <option value="">{{ __('Assign to Department (Optional)') }}</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><input type="date" name="hire_date" class="form-control" placeholder="{{ __('Hire Date') }}"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern primary w-100">{{ __('Create Manager') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Employee Modal --}}
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Register New Employee') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addEmployeeForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="{{ __('Full Name') }}" required></div>
                    <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="{{ __('Email Address') }}" required></div>
                    <div class="mb-3">
                        <select name="department_id" class="form-select" required>
                            <option value="">{{ __('Select Department') }}</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><input type="text" name="job_title" class="form-control" placeholder="{{ __('Job Title') }}"></div>
                    <div class="mb-3"><input type="date" name="hire_date" class="form-control" placeholder="{{ __('Hire Date') }}"></div>
                    <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="{{ __('Password for Employee Login') }}" required minlength="6"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern primary w-100">{{ __('Register Employee') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Metrics Modal --}}
<div class="modal fade" id="metricsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Update Performance Data') }}: <span id="metricsEmployeeName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateMetricsForm">
                @csrf
                <input type="hidden" name="id" id="metricsId">
                <input type="hidden" name="type" id="metricsType">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-6" id="attendanceFieldContainer">
                            <input type="number" name="attendance_rate" id="metricsAttendance" class="form-control" placeholder="{{ __('Attendance Rate (%)') }}" min="0" max="100">
                        </div>
                        <div class="col-6">
                            <input type="date" name="hire_date" id="metricsHireDate" class="form-control" placeholder="{{ __('Hire Date') }}">
                        </div>
                        <div class="col-6">
                            <input type="number" name="tasks_requested" id="metricsRequested" class="form-control" placeholder="{{ __('Tasks Requested') }}" min="0" required>
                        </div>
                        <div class="col-6">
                            <input type="number" name="tasks_completed" id="metricsCompleted" class="form-control" placeholder="{{ __('Tasks Completed') }}" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern success w-100">{{ __('Save Performance Data') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Profile Modal --}}
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modern overflow-hidden">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('Professional Profile') }}</h5>
                <div class="d-flex gap-2">
                    <button class="btn-modern ghost py-1 px-2" onclick="printReport()"><i class="bi bi-printer"></i></button>
                    <button class="btn-modern ghost py-1 px-2" onclick="downloadReport()"><i class="bi bi-download"></i></button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-4" id="printableReportArea">
                <div class="row g-4">
                    <div class="col-md-4 text-center" style="border-right: 1px solid var(--glass-border)">
                        <div class="avatar xl mx-auto mb-3" id="profAvatar">?</div>
                        <h5 class="text-white mb-0" id="profName">-</h5>
                        <div class="badge-pill primary mt-2 mx-auto d-inline-flex" id="profTitle">-</div>
                        <hr style="border-color: var(--glass-border); margin: 1rem 0">
                        <div class="text-start">
                            <div class="mb-2 d-flex justify-content-between">
                                <span class="text-secondary" style="font-size:.75rem">{{ __('Department') }}</span>
                                <span class="text-white" style="font-size:.8rem" id="profDept">-</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <span class="text-secondary" style="font-size:.75rem">{{ __('Hire Date') }}</span>
                                <span class="text-white" style="font-size:.8rem" id="profHireDate">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-white mb-0">{{ __('AI Performance Report') }}</h6>
                            <span class="badge-pill success" id="profScoreContainer">
                                {{ __('Score') }}: <span id="profScore">-</span>/10
                            </span>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary" style="font-size:.75rem">{{ __('Task Completion Rate') }}</span>
                                <span class="text-white fw-bold" style="font-size:.8rem" id="profTaskRate">0%</span>
                            </div>
                            <div class="modern-progress">
                                <div class="bar" id="profProgressBar" style="width:0%"></div>
                            </div>
                        </div>
                        <div id="profReportContent">
                            <div class="p-3 rounded-3 mb-2" style="background:rgba(16,185,129,.07); border-left:3px solid var(--emerald)">
                                <div class="text-success fw-bold small mb-1"><i class="bi bi-star-fill me-1"></i>{{ __('Strengths') }}</div>
                                <p class="text-secondary small mb-0" id="profStrengths">{{ __('No evaluation data yet.') }}</p>
                            </div>
                            <div class="p-3 rounded-3 mb-2" style="background:rgba(244,63,94,.07); border-left:3px solid var(--rose)">
                                <div class="text-danger fw-bold small mb-1"><i class="bi bi-graph-down-arrow me-1"></i>{{ __('Areas to Improve') }}</div>
                                <p class="text-secondary small mb-0" id="profWeaknesses">{{ __('No evaluation data yet.') }}</p>
                            </div>
                            <div class="p-3 rounded-3" style="background:rgba(99,102,241,.07); border-left:3px solid var(--primary-light)">
                                <div class="text-primary fw-bold small mb-1"><i class="bi bi-lightbulb-fill me-1"></i>{{ __('Recommendations') }}</div>
                                <p class="text-secondary small mb-0" id="profRecommendations">{{ __('No evaluation data yet.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@php
$evaloConfig = [
    'csrfToken' => csrf_token(),
    'routes' => [
        'hrStore'        => route('hr.store'),
        'hrUpdate'       => route('hr.update'),
        'hrDelete'       => route('hr.delete'),
        'deptStore'      => route('dept.store'),
        'deptUpdate'     => route('dept.update'),
        'managerStore'   => route('manager.store'),
        'employeeStore'  => route('employee.store'),
        'employeeMetrics'=> route('employee.metrics'),
        'managerMetrics' => route('manager.metrics'),
        'aiChat'         => route('ai.chat'),
        'profileGet'     => route('profile.get'),
    ],
    'translations' => [
        'processing'   => __('Processing...'),
        'errorOccurred'=> __('Error occurred'),
        'networkError' => __('Network error'),
    ],
    '_i18n' => [
        'thinking' => __('Thinking...'),
        'errorMsg' => __('Sorry, I encountered an error.'),
    ],
    'chartData' => [
        'label'  => __('Efficiency'),
        'labels' => $chartLabels,
        'data'   => $chartDataValues,
    ],
];
@endphp
<script>window.EvaloConfig = Object.assign(window.EvaloConfig || {}, @json($evaloConfig));</script>

<script>
// ── Live Clock ─────────────────────────────────────────────
(function() {
    function tick() {
        const now = new Date();
        document.getElementById('liveClock').textContent = now.toLocaleTimeString('en-GB');
    }
    tick();
    setInterval(tick, 1000);
})();

// ── Modern Tabs ────────────────────────────────────────────
document.querySelectorAll('#dashTabs .tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('#dashTabs .tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        const target = document.querySelector(btn.dataset.target);
        if (target) target.classList.add('active');
    });
});

// ── Performance Ring Chart ──────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const ringCtx = document.getElementById('perfRingChart');
    if (ringCtx) {
        new Chart(ringCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $avgPerformance }}, {{ 10 - $avgPerformance }}],
                    backgroundColor: [
                        'rgba(99,102,241,0.85)',
                        'rgba(255,255,255,0.05)',
                    ],
                    borderWidth: 0,
                    borderRadius: 4,
                }]
            },
            options: {
                cutout: '75%',
                responsive: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                animation: { animateRotate: true, duration: 900 }
            }
        });
    }

    // Trend Line Chart
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        const ctx = trendCtx.getContext('2d');
        const grad = ctx.createLinearGradient(0, 0, 0, 220);
        grad.addColorStop(0, 'rgba(99,102,241,0.35)');
        grad.addColorStop(1, 'rgba(99,102,241,0)');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: window.EvaloConfig.chartData.labels,
                datasets: [{
                    label: window.EvaloConfig.chartData.label,
                    data: window.EvaloConfig.chartData.data,
                    borderColor: '#818cf8',
                    backgroundColor: grad,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#818cf8',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#94a3b8',
                        displayColors: false,
                        borderColor: 'rgba(255,255,255,.08)',
                        borderWidth: 1,
                    }
                },
                scales: {
                    y: { beginAtZero: true, max: 10, grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: '#64748b', stepSize: 2 } },
                    x: { grid: { display: false }, ticks: { color: '#64748b' } }
                }
            }
        });
    }
});
</script>

{{-- Tab panel inline style --}}
<style>
.tab-panel { display: none; }
.tab-panel.active { display: block; }
</style>

<script src="{{ asset('evalo/js/dashboard.js') }}"></script>
@endsection
