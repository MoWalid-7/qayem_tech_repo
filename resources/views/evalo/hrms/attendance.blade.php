@extends('evalo.hrms-layout')

@section('title', __('Attendance') . ' — Evalo HRMS')
@section('page_title', __('Attendance'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">{{ __('Attendance') }}</h5>
        <p class="text-secondary small mb-0">{{ __('Track daily attendance and punctuality') }}</p>
    </div>
    <span class="badge-pill cyan">
        <i class="bi bi-calendar3 me-1"></i> {{ now()->translatedFormat('d F Y') }}
    </span>
</div>

{{-- Today Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card emerald p-3">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <div class="stat-value">{{ $presentToday }}</div>
            <div class="stat-label">{{ __('Present Today') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card amber p-3">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
            <div class="stat-value">{{ $lateToday }}</div>
            <div class="stat-label">{{ __('Late Arrivals') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card rose p-3">
            <div class="stat-icon"><i class="bi bi-person-x-fill"></i></div>
            <div class="stat-value">{{ $records->total() }}</div>
            <div class="stat-label">{{ __('Total Records') }}</div>
        </div>
    </div>
</div>

{{-- Attendance Table --}}
<div class="glass-card p-0 overflow-hidden">
    <div class="p-4 border-bottom" style="border-color: var(--glass-border) !important;">
        <h6 class="mb-0 fw-bold">{{ __('Attendance Log') }}</h6>
    </div>
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Check In') }}</th>
                    <th>{{ __('Check Out') }}</th>
                    <th>{{ __('Duration') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $rec)
                @php
                    $checkIn  = $rec->check_in  ? \Carbon\Carbon::parse($rec->check_in)  : null;
                    $checkOut = $rec->check_out ? \Carbon\Carbon::parse($rec->check_out) : null;
                    $duration = ($checkIn && $checkOut) ? $checkIn->diff($checkOut)->format('%H:%I hrs') : '—';
                    $isLate   = $checkIn && $checkIn->format('H:i') > '09:00';
                    $person   = $rec->user;
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar" style="width:32px;height:32px;font-size:.75rem;border-radius:8px">
                                {{ substr($person->name ?? '?', 0, 1) }}
                            </div>
                            <div class="fw-semibold">{{ $person->name ?? '—' }}</div>
                        </div>
                    </td>
                    <td class="text-secondary small">{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</td>
                    <td>
                        @if($checkIn)
                            <span class="{{ $isLate ? 'text-warning' : 'text-success' }} fw-semibold small">
                                {{ $checkIn->format('H:i') }}
                            </span>
                        @else
                            <span class="text-secondary">—</span>
                        @endif
                    </td>
                    <td>
                        @if($checkOut)
                            <span class="small text-secondary">{{ $checkOut->format('H:i') }}</span>
                        @else
                            <span class="text-secondary">—</span>
                        @endif
                    </td>
                    <td class="small text-secondary">{{ $duration }}</td>
                    <td>
                        @if(!$checkIn)
                            <span class="badge-pill danger">{{ __('Absent') }}</span>
                        @elseif($isLate)
                            <span class="badge-pill warning">{{ __('Late') }}</span>
                        @else
                            <span class="badge-pill success">{{ __('On Time') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-secondary">
                        <i class="bi bi-fingerprint fs-1 d-block mb-3 opacity-25"></i>
                        {{ __('No attendance records found.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
    <div class="p-3 border-top d-flex justify-content-end" style="border-color: var(--glass-border) !important;">
        {{ $records->links() }}
    </div>
    @endif
</div>
@endsection
