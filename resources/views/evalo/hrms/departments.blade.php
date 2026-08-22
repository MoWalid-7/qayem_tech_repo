@extends('evalo.hrms-layout')

@section('title', __('Departments') . ' — Evalo HRMS')
@section('page_title', __('Departments'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">{{ __('Departments') }}</h5>
        <p class="text-secondary small mb-0">{{ __('Manage your company structure') }}</p>
    </div>
    <button class="btn-modern primary" data-bs-toggle="modal" data-bs-target="#addDeptModal">
        <i class="bi bi-plus-lg"></i> {{ __('Add Department') }}
    </button>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card indigo p-3">
            <div class="stat-icon"><i class="bi bi-building"></i></div>
            <div class="stat-value">{{ $departments->count() }}</div>
            <div class="stat-label">{{ __('Total Departments') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card cyan p-3">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ $departments->sum(fn($d) => $d->employees->count()) }}</div>
            <div class="stat-label">{{ __('Total Employees') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card emerald p-3">
            <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="stat-value">{{ $departments->filter(fn($d) => $d->manager)->count() }}</div>
            <div class="stat-label">{{ __('Managed Departments') }}</div>
        </div>
    </div>
</div>

{{-- Departments Grid --}}
<div class="row g-4">
    @forelse($departments as $dept)
    <div class="col-md-6 col-lg-4">
        <div class="glass-card h-100 p-4">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar" style="border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--cyan));">
                        {{ strtoupper(substr($dept->name, 0, 2)) }}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ $dept->name }}</h6>
                        <span class="small text-secondary">
                            {{ $dept->employees->count() }} {{ __('employees') }}
                        </span>
                    </div>
                </div>
            </div>

            @if($dept->manager)
            <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded-3" style="background: var(--glass-bg); border: 1px solid var(--glass-border)">
                <div class="avatar" style="width:28px;height:28px;font-size:.7rem;border-radius:8px">
                    {{ substr($dept->manager->name, 0, 1) }}
                </div>
                <div>
                    <div class="small fw-semibold">{{ $dept->manager->name }}</div>
                    <div class="text-secondary" style="font-size:.65rem">{{ __('Dept. Manager') }}</div>
                </div>
            </div>
            @else
            <div class="small text-secondary mb-3 py-2">
                <i class="bi bi-exclamation-circle me-1 text-warning"></i>{{ __('No manager assigned') }}
            </div>
            @endif

            {{-- Employee mini-list --}}
            <div class="d-flex flex-wrap gap-1">
                @foreach($dept->employees->take(5) as $emp)
                    <span class="badge-pill primary" title="{{ $emp->name }}">{{ substr($emp->name, 0, 1) }}</span>
                @endforeach
                @if($dept->employees->count() > 5)
                    <span class="badge-pill cyan">+{{ $dept->employees->count() - 5 }}</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="glass-card text-center py-5">
            <i class="bi bi-building fs-1 opacity-25 d-block mb-3"></i>
            <p class="text-secondary">{{ __('No departments yet. Create one to organize your team.') }}</p>
            <button class="btn-modern primary mt-2" data-bs-toggle="modal" data-bs-target="#addDeptModal">
                <i class="bi bi-plus-lg"></i> {{ __('Create First Department') }}
            </button>
        </div>
    </div>
    @endforelse
</div>

{{-- Add Department Modal --}}
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modern">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-building me-2"></i>{{ __('Add Department') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('dept.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-secondary">{{ __('Department Name') }}</label>
                        <input type="text" name="name" class="form-control" required placeholder="{{ __('e.g. Engineering') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary">{{ __('Description') }}</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="{{ __('Optional…') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern ghost" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-modern primary">{{ __('Create Department') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
