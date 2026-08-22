@extends('evalo.layout')

@section('title', __('My Profile') . ' - ' . $employee->name)
@section('body_class', 'bg-dark')

@section('styles')
<style>
    .score-badge {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 800;
        border: 3px solid;
        margin: 0 auto 1rem;
    }

    .score-excellent {
        background: rgba(16, 185, 129, 0.15);
        border-color: #10b981;
        color: #10b981;
    }

    .score-good {
        background: rgba(59, 130, 246, 0.15);
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .score-average {
        background: rgba(245, 158, 11, 0.15);
        border-color: #f59e0b;
        color: #f59e0b;
    }

    .score-poor {
        background: rgba(239, 68, 68, 0.15);
        border-color: #ef4444;
        color: #ef4444;
    }

    .report-section {
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        border-left: 4px solid;
    }

    .report-strengths {
        background: rgba(16, 185, 129, 0.08);
        border-color: #10b981;
    }

    .report-weaknesses {
        background: rgba(239, 68, 68, 0.08);
        border-color: #ef4444;
    }

    .report-recs {
        background: rgba(99, 102, 241, 0.08);
        border-color: #818cf8;
    }

    .stat-pill {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
    }


    .eval-history-item {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
    }

    .chart-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 1.5rem;
        height: 100%;
    }

    .chart-container-profile {
        position: relative;
        height: 200px;
        width: 100%;
    }

    /* ===== Full Report Formatted ===== */
    .report-body h1,
    .report-body h2,
    .report-body h3 {
        color: #c7d2fe;
        font-weight: 700;
        margin-top: 1.2rem;
        margin-bottom: 0.4rem;
    }

    .report-body h1 {
        font-size: 1.1rem;
    }

    .report-body h2 {
        font-size: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        padding-bottom: 4px;
    }

    .report-body h3 {
        font-size: 0.9rem;
        color: #a5b4fc;
    }

    .report-body p {
        color: #cbd5e1;
        font-size: 0.875rem;
        line-height: 1.8;
        margin-bottom: 0.5rem;
    }

    .report-body em {
        color: #94a3b8;
    }

    .report-body h1,
    .report-body h2,
    .report-body h3 {
        color: #f8fafc;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .report-body h1 {
        font-size: 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 0.5rem;
    }

    .report-body h2 {
        font-size: 1.1rem;
    }

    .report-body h3 {
        font-size: 1rem;
        color: #10b981;
    }

    .report-body p {
        color: #94a3b8;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .report-body li {
        color: #94a3b8;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        list-style: none;
        position: relative;
        padding-left: 1.25rem;
    }

    .report-body li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #10b981;
        font-weight: bold;
    }

    .report-body strong {
        color: #fff;
        font-weight: 600;
    }


    @media print {

        .floating-chat-container,
        .qt-navbar,
        .btn-logout-nav {
            display: none !important;
        }

        body {
            background: white !important;
            color: black !important;
        }

        .glass-card {
            background: #f8f9fa !important;
            border: 1px solid #ddd !important;
        }

        .text-white,
        .text-light {
            color: #111 !important;
        }

        .text-secondary {
            color: #666 !important;
        }

        .report-body p,
        .report-body ul {
            color: #222 !important;
        }

        .report-body h1,
        .report-body h2,
        .report-body h3 {
            color: #333 !important;
        }
    }
</style>
@endsection

@section('content')

{{-- Navbar --}}
<nav class="qt-navbar">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 d-none d-md-inline-block">
                {{ __('Employee Portal') }}
            </span>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light border-glass rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-sm-block lh-1">
                <div class="text-white small fw-bold mb-1">{{ $employee->name }}</div>
                <div class="text-secondary text-uppercase fw-medium" style="font-size:0.65rem; letter-spacing:0.5px;">{{ __('Employee') }}</div>
            </div>

            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-translate me-1"></i> {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
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

<div class="container" style="padding-top: calc(68px + 2.5rem); padding-bottom: 3rem;">

    {{-- ===== Row 1: Profile + Score ===== --}}
    <div class="row g-4 mb-4">

        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100 animate-up text-center d-flex flex-column align-items-center justify-content-center">
                <div class="company-initials mb-3" style="width:80px;height:80px;font-size:2rem;">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                </div>
                <h4 class="text-white mb-1">{{ $employee->name }}</h4>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 mb-4">
                    {{ __('Employee') }}
                </span>

                <div class="w-100 mt-2">
                    @foreach([
                    ['label' => __('Department'), 'value' => __($employee->department?->name ?? 'N/A')],
                    ['label' => __('Company'), 'value' => __($employee->company?->name ?? 'N/A')],
                    ['label' => __('Hire Date'), 'value' => $employee->hire_date ? $employee->hire_date->format('Y-m-d') : __('N/A')],
                    ['label' => __('Email'), 'value' => $employee->email],
                    ] as $row)
                    <div class="d-flex justify-content-between py-2 border-bottom border-glass">
                        <span class="text-secondary small">{{ $row['label'] }}</span>
                        <span class="text-white small fw-medium">{{ $row['value'] }}</span>
                    </div>
                    @endforeach
                </div>
                
                {{-- Attendance Actions --}}
                <div class="mt-4 pt-3 border-top border-glass w-100">
                    <h6 class="text-secondary smaller text-uppercase mb-3">{{ __('Daily Attendance') }}</h6>
                    <div class="d-flex gap-2 justify-content-center">
                        <button onclick="recordAttendance('check-in')" class="btn btn-sm btn-outline-success border-glass rounded-pill px-3 flex-grow-1">
                            <i class="bi bi-box-arrow-in-right me-1"></i> {{ __('Check In') }}
                        </button>
                        <button onclick="recordAttendance('check-out')" class="btn btn-sm btn-outline-danger border-glass rounded-pill px-3 flex-grow-1">
                            <i class="bi bi-box-arrow-left me-1"></i> {{ __('Check Out') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats + Score --}}
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100 animate-up" style="animation-delay:0.1s">
                <h6 class="text-secondary text-uppercase smaller mb-4">{{ __('Performance Overview') }}</h6>

                {{-- Score Circle --}}
                @php
                $score = $latestEvaluation?->score ?? null;
                $scoreClass = 'score-average';
                if ($score >= 8) $scoreClass = 'score-excellent';
                elseif ($score >= 6) $scoreClass = 'score-good';
                elseif ($score < 4 && $score !==null) $scoreClass='score-poor' ;
                    @endphp
                    <div class="text-center mb-4">
                    <div class="score-badge {{ $scoreClass }}" id="evalScoreBadge">
                        {{ $score ?? '–' }}
                    </div>
                    <div class="text-secondary small">{{ __('AI Performance Score') }} / 10</div>
            </div>

            {{-- Stats Row --}}
            <div class="row g-3">
                <div class="col-4">
                    <div class="stat-pill">
                        <div class="text-white fw-bold fs-4">{{ $employee->attendance_rate ?? 0 }}%</div>
                        <div class="text-secondary smaller">{{ __('Attendance') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-pill">
                        <div class="text-success fw-bold fs-4">{{ $employee->tasks_completed ?? 0 }}</div>
                        <div class="text-secondary smaller">{{ __('Tasks Done') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-pill">
                        @php $rate = ($employee->tasks_requested ?? 0) > 0 ? round(($employee->tasks_completed / $employee->tasks_requested) * 100) : 0; @endphp
                        <div class="text-primary fw-bold fs-4">{{ $rate }}%</div>
                        <div class="text-secondary smaller">{{ __('Completion') }}</div>
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="mt-4 mb-4">
                <div class="d-flex justify-content-between smaller mb-1">
                    <span class="text-secondary">{{ __('Task Completion Rate') }}</span>
                    <span class="text-white fw-bold">{{ $rate }}%</span>
                </div>
                <div class="progress bg-dark" style="height:8px">
                    <div class="progress-bar bg-primary" style="width:{{ $rate }}%"></div>
                </div>
            </div>

            {{-- Performance Chart Row --}}
            <div class="row g-3">
                <div class="col-12">
                    <div class="chart-box">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-secondary text-uppercase smaller mb-0">{{ __('Monthly Performance Trend') }}</h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary-light smaller px-2">{{ __('Live Update') }}</span>
                        </div>
                        <div class="chart-container-profile">
                            <canvas id="employeeTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Row 2: Latest Evaluation Report ===== --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="glass-card p-4 animate-up" style="animation-delay:0.2s">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-white mb-0">
                    <i class="bi bi-robot me-2 text-primary"></i>{{ __('AI Evaluation Report') }}
                </h5>
                <div class="d-flex align-items-center gap-2">
                    @if($latestEvaluation)
                    <span class="text-secondary smaller me-2" id="evalDate">
                        {{ __('Generated') }}: {{ $latestEvaluation->created_at->translatedFormat('M d, Y') }}
                    </span>
                    <button onclick="printReport()" class="btn btn-sm btn-outline-light border-glass rounded-pill px-3 py-1">
                        <i class="bi bi-printer me-1"></i> {{ __('Print Report') }}
                    </button>
                    @endif
                </div>
            </div>

            @if($latestEvaluation)
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="report-section report-strengths h-100">
                        <h6 class="text-success mb-2"><i class="bi bi-star-fill me-2"></i>{{ __('Strengths') }}</h6>
                        <p class="text-light small mb-0 lh-lg" id="evalStrengths">{{ $latestEvaluation->strengths ?? __('N/A') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="report-section report-weaknesses h-100">
                        <h6 class="text-danger mb-2"><i class="bi bi-graph-down-arrow me-2"></i>{{ __('Areas to Improve') }}</h6>
                        <p class="text-light small mb-0 lh-lg" id="evalWeaknesses">{{ $latestEvaluation->weaknesses ?? __('N/A') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="report-section report-recs h-100">
                        <h6 class="text-info mb-2"><i class="bi bi-lightbulb-fill me-2"></i>{{ __('Recommendations') }}</h6>
                        <p class="text-light small mb-0 lh-lg" id="evalRecommendations">{{ $latestEvaluation->recommendations ?? __('N/A') }}</p>
                    </div>
                </div>
            </div>

            {{-- Full Report Text --}}
            @if($latestEvaluation->evaluation_text)
            <div class="mt-3 p-4 rounded-3" id="fullReportArea" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07)">
                <h6 class="text-secondary smaller text-uppercase mb-3">{{ __('Full Detailed Report') }}</h6>
                <div class="report-body" id="reportBodyContent">
                    @php
                    $reportLines = explode("\n", $latestEvaluation->evaluation_text);
                    @endphp
                    @foreach($reportLines as $line)
                    @if(preg_match('/^### (.+)/', $line, $m))
                    <h3>{{ $m[1] }}</h3>
                    @elseif(preg_match('/^## (.+)/', $line, $m))
                    <h2>{{ $m[1] }}</h2>
                    @elseif(preg_match('/^# (.+)/', $line, $m))
                    <h1>{{ $m[1] }}</h1>
                    @elseif(preg_match('/^[-*•] (.+)/', trim($line), $m))
                    <li>{{ $m[1] }}</li>
                    @elseif(!empty(trim($line)))
                    <p>{!! preg_replace(['/\*\*(.+?)\*\*/','/\*(.+?)\*/'], ['<strong>$1</strong>','<em>$1</em>'], e($line)) !!}</p>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            @else
            <div class="text-center py-5">
                <i class="bi bi-clipboard-x text-secondary" style="font-size:3rem"></i>
                <p class="text-secondary mt-3 mb-0">{{ __('No evaluation report yet.') }}</p>
                <p class="text-secondary small">{{ __('Your manager will generate an AI evaluation for you soon.') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>


{{-- ===== Row 3: Previous Evaluations ===== --}}
@php $oldEvals = $latestEvaluation ? $employee->evaluations->where('id', '!=', $latestEvaluation->id) : $employee->evaluations; @endphp
@if($oldEvals->count() > 0)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="glass-card p-4 animate-up" style="animation-delay:0.3s">
            <h6 class="text-secondary text-uppercase smaller mb-3">{{ __('Evaluation History') }}</h6>
            @foreach($oldEvals as $idx => $eval)
            <div class="eval-history-item d-flex justify-content-between align-items-center mb-2"
                style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#evalDetail{{ $idx }}" aria-expanded="false">
                <div>
                    <span class="text-secondary small">{{ $eval->created_at->translatedFormat('M d, Y') }}</span>
                    <p class="text-white small mb-0 mt-1">{{ Str::limit($eval->evaluation_text, 80) }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge @if($eval->score >= 8) bg-success @elseif($eval->score >= 6) bg-primary @elseif($eval->score >= 4) bg-warning text-dark @else bg-danger @endif">
                        {{ $eval->score }}/10
                    </span>
                    <i class="bi bi-chevron-down text-secondary"></i>
                </div>
            </div>
            <div class="collapse" id="evalDetail{{ $idx }}">
                <div class="p-3 rounded-3 mb-3" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06)">
                    @if($eval->strengths)
                    <div class="mb-2"><span class="text-success small fw-bold">✦ {{ __('Strengths') }}:</span><br><span class="text-light small">{{ $eval->strengths }}</span></div>
                    @endif
                    @if($eval->weaknesses)
                    <div class="mb-2"><span class="text-danger small fw-bold">✦ {{ __('Areas to Improve') }}:</span><br><span class="text-light small">{{ $eval->weaknesses }}</span></div>
                    @endif
                    @if($eval->recommendations)
                    <div class="mb-2"><span class="text-info small fw-bold">✦ {{ __('Recommendations') }}:</span><br><span class="text-light small">{{ $eval->recommendations }}</span></div>
                    @endif
                    @if($eval->evaluation_text)
                    <hr style="border-color:rgba(255,255,255,0.08)">
                    <div class="report-body small">
                        @php $histLines = explode("\n", $eval->evaluation_text); @endphp
                        @foreach($histLines as $hline)
                        @if(!empty(trim($hline)))<p class="mb-1">{{ $hline }}</p>@endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif



</div>


@endsection

@section('scripts')
<script>
    function printReport() {
        window.print();
    }

    function recordAttendance(type) {
        const url = type === 'check-in' 
            ? '{{ route("attendance.checkIn") }}' 
            : '{{ route("attendance.checkOut") }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                // Optionally reload to update status
                // window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert("{{ __('An error occurred.') }}");
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('employeeTrendChart');
        if (!ctx) return;
        const chartCtx = ctx.getContext('2d');
        const gradient = chartCtx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["{{ __('Jan') }}", "{{ __('Feb') }}", "{{ __('Mar') }}", "{{ __('Apr') }}", "{{ __('May') }}", "{{ __('Jun') }}"],
                datasets: [{
                    label: "{{ __('Score') }}",
                    data: [5.5, 6.2, 7.0, 7.8, 8.2, 8.4],
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#94a3b8',
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        grid: {
                            color: 'rgba(255,255,255,0.03)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            stepSize: 2
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection