@extends('evalo.hrms-layout')

@section('title', __('Employees') . ' — Evalo HRMS')
@section('page_title', __('Employees'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">{{ __('All Employees') }}</h5>
        <p class="text-secondary small mb-0">{{ __('Manage your company workforce') }}</p>
    </div>
    <button class="btn-modern primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
        <i class="bi bi-plus-lg"></i> {{ __('Add Employee') }}
    </button>
</div>

{{-- Stats Row --}}
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
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <div class="stat-value">{{ $employees->where('status', 'active')->count() }}</div>
            <div class="stat-label">{{ __('Active') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card cyan p-3">
            <div class="stat-icon"><i class="bi bi-building"></i></div>
            <div class="stat-value">{{ $departments->count() }}</div>
            <div class="stat-label">{{ __('Departments') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card amber p-3">
            <div class="stat-icon"><i class="bi bi-star-fill"></i></div>
            <div class="stat-value">{{ $employees->flatMap->evaluations->avg('score') ? round($employees->flatMap->evaluations->avg('score'), 1) : '—' }}</div>
            <div class="stat-label">{{ __('Avg. Score') }}</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="glass-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" id="empSearch" class="form-control" placeholder="{{ __('Search by name or email…') }}">
        </div>
        <div class="col-md-3">
            <select id="deptFilter" class="form-select">
                <option value="">{{ __('All Departments') }}</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- Employees Table --}}
<div class="glass-card p-0 overflow-hidden">
    <div class="p-4 border-bottom" style="border-color: var(--glass-border) !important;">
        <h6 class="mb-0 fw-bold">{{ __('Employee Directory') }}</h6>
    </div>
    <div class="table-responsive">
        <table class="modern-table" id="empTable">
            <thead>
                <tr>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Department') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Performance') }}</th>
                    <th>{{ __('Joined') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr data-dept="{{ $emp->department_id }}" data-name="{{ strtolower($emp->name) }} {{ strtolower($emp->email) }}">
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">{{ substr($emp->name, 0, 1) }}</div>
                            <div>
                                <div class="fw-semibold text-primary-color">{{ $emp->name }}</div>
                                <div class="small text-secondary">{{ $emp->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $emp->department?->name ?? '—' }}</td>
                    <td>{{ $emp->job_title ?? '—' }}</td>
                    <td>
                        @php $avg = round($emp->evaluations->avg('score') ?? 0, 1); @endphp
                        @if($avg > 0)
                            <span class="badge-pill {{ $avg >= 75 ? 'success' : ($avg >= 50 ? 'warning' : 'danger') }}">
                                {{ $avg }}%
                            </span>
                        @else
                            <span class="text-secondary small">{{ __('No eval yet') }}</span>
                        @endif
                    </td>
                    <td class="text-secondary small">{{ $emp->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn-modern ghost btn-sm py-1 px-2" onclick="openEvalModal({{ $emp->id }}, '{{ addslashes($emp->name) }}')" title="{{ __('Evaluate') }}">
                                <i class="bi bi-lightning-charge"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-secondary">
                        <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                        {{ __('No employees found. Add your first employee to get started.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Evaluate Modal --}}
<div class="modal fade" id="evaluateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modern">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-lightning-charge me-2 text-warning"></i>{{ __('Evaluate Employee') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="evalForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-secondary mb-3">{{ __('Evaluating:') }} <strong id="evalEmpName" class="text-primary-color"></strong></p>
                    <div class="mb-3">
                        <label class="form-label small text-secondary">{{ __('Score (0–100)') }}</label>
                        <input type="number" name="score" min="0" max="100" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary">{{ __('Notes') }}</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="{{ __('Optional notes…') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern ghost" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-modern primary">{{ __('Submit Evaluation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live search + department filter
    const searchInput  = document.getElementById('empSearch');
    const deptFilter   = document.getElementById('deptFilter');
    const rows         = document.querySelectorAll('#empTable tbody tr[data-name]');

    function filterTable() {
        const q    = searchInput.value.toLowerCase();
        const dept = deptFilter.value;
        rows.forEach(row => {
            const nameMatch = row.dataset.name.includes(q);
            const deptMatch = !dept || row.dataset.dept === dept;
            row.style.display = nameMatch && deptMatch ? '' : 'none';
        });
    }
    searchInput.addEventListener('input', filterTable);
    deptFilter.addEventListener('change', filterTable);

    // Evaluate modal
    function openEvalModal(empId, empName) {
        document.getElementById('evalEmpName').textContent = empName;
        document.getElementById('evalForm').action = `/evaluate/employee/${empId}`;
        new bootstrap.Modal(document.getElementById('evaluateModal')).show();
    }
</script>
@endsection
