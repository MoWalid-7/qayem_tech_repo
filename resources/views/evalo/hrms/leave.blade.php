@extends('evalo.hrms-layout')

@section('title', __('Leave Requests') . ' — Evalo HRMS')
@section('page_title', __('Leave Requests'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">{{ __('Leave Requests') }}</h5>
        <p class="text-secondary small mb-0">{{ __('Review and manage employee leave') }}</p>
    </div>
</div>

@if($leaves->isEmpty())
{{-- Empty state with coming soon design --}}
<div class="glass-card text-center py-5 px-4" style="max-width:520px; margin: 4rem auto;">
    <div class="mb-4" style="width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(6,182,212,.1));border:1px solid rgba(99,102,241,.2);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto;">
        <i class="bi bi-calendar2-check text-primary-color"></i>
    </div>
    <h5 class="fw-bold mb-2">{{ __('Leave Management') }}</h5>
    <p class="text-secondary mb-4">{{ __('No leave requests have been submitted yet. Once employees submit leave requests, they will appear here for your review.') }}</p>
    <div class="d-flex gap-2 justify-content-center flex-wrap">
        <span class="badge-pill primary"><i class="bi bi-check2-circle me-1"></i>{{ __('Approve') }}</span>
        <span class="badge-pill danger"><i class="bi bi-x-circle me-1"></i>{{ __('Reject') }}</span>
        <span class="badge-pill warning"><i class="bi bi-clock me-1"></i>{{ __('Pending') }}</span>
    </div>
</div>
@else
{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $pending  = $leaves->where('status', 'pending')->count();
        $approved = $leaves->where('status', 'approved')->count();
        $rejected = $leaves->where('status', 'rejected')->count();
    @endphp
    <div class="col-md-4">
        <div class="stat-card amber p-3">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value">{{ $pending }}</div>
            <div class="stat-label">{{ __('Pending') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card emerald p-3">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-value">{{ $approved }}</div>
            <div class="stat-label">{{ __('Approved') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card rose p-3">
            <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-value">{{ $rejected }}</div>
            <div class="stat-label">{{ __('Rejected') }}</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="glass-card p-0 overflow-hidden">
    <div class="p-4 border-bottom" style="border-color: var(--glass-border) !important;">
        <h6 class="mb-0 fw-bold">{{ __('Leave Requests Log') }}</h6>
    </div>
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('From') }}</th>
                    <th>{{ __('To') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaves as $leave)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar" style="width:32px;height:32px;font-size:.75rem;border-radius:8px">
                                {{ substr($leave->employee->name ?? '?', 0, 1) }}
                            </div>
                            <div class="fw-semibold">{{ $leave->employee->name ?? '—' }}</div>
                        </div>
                    </td>
                    <td class="small text-secondary">{{ $leave->type ?? __('Annual') }}</td>
                    <td class="small text-secondary">{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</td>
                    <td class="small text-secondary">{{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</td>
                    <td>
                        @if($leave->status === 'approved')
                            <span class="badge-pill success">{{ __('Approved') }}</span>
                        @elseif($leave->status === 'rejected')
                            <span class="badge-pill danger">{{ __('Rejected') }}</span>
                        @else
                            <span class="badge-pill warning">{{ __('Pending') }}</span>
                        @endif
                    </td>
                    <td>
                        @if(($leave->status ?? 'pending') === 'pending')
                        <div class="d-flex gap-1">
                            <button class="btn-modern success btn-sm py-1 px-2">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button class="btn-modern danger btn-sm py-1 px-2">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if(method_exists($leaves, 'hasPages') && $leaves->hasPages())
    <div class="p-3 border-top" style="border-color: var(--glass-border) !important;">
        {{ $leaves->links() }}
    </div>
    @endif
</div>
@endif
@endsection
