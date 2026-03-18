@extends('qayemtech.layout')

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

    .floating-chat-container {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
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
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        animation: pulse 2s infinite;
    }

    .floating-chat-btn:hover {
        transform: scale(1.1) rotate(5deg);
    }

    .floating-chat-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 360px;
        height: 520px;
        display: flex;
        flex-direction: column;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform-origin: bottom right;
        pointer-events: none;
    }

    .floating-chat-window.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: all;
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

    .report-body ul {
        padding-left: 1.25rem;
        color: #cbd5e1;
        font-size: 0.875rem;
        line-height: 1.8;
        margin-bottom: 0.5rem;
    }

    .report-body strong {
        color: #e2e8f0;
    }

    .report-body em {
        color: #94a3b8;
    }

    /* ===== Expandable Chat ===== */
    .floating-chat-window.expanded {
        width: 520px !important;
        height: 700px !important;
    }

    .floating-chat-window.fullscreen {
        position: fixed !important;
        inset: auto 0 0 auto !important;
        width: 420px !important;
        height: 90vh !important;
        border-radius: 16px 16px 0 0;
    }

    .chat-resize-handle {
        position: absolute;
        top: 0;
        left: 0;
        width: 18px;
        height: 18px;
        cursor: nw-resize;
        background: linear-gradient(135deg, transparent 50%, rgba(255, 255, 255, 0.15) 50%);
        border-top-left-radius: 16px;
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
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-sm-block lh-1">
                <div class="text-white small fw-bold mb-1">{{ $employee->name }}</div>
                <div class="text-secondary text-uppercase fw-medium" style="font-size:0.65rem; letter-spacing:0.5px;">
                    {{ __($employee->job_title ?? 'Employee') }}
                </div>
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
                    {{ __($employee->job_title ?? 'Employee') }}
                </span>

                <div class="w-100 mt-2">
                    @foreach([
                    ['label' => __('Department'), 'value' => __($employee->department?->name ?? 'N/A')],
                    ['label' => __('Company'), 'value' => __($employee->company?->name ?? 'N/A')],
                    ['label' => __('Email'), 'value' => $employee->email],
                    ] as $row)
                    <div class="d-flex justify-content-between py-2 border-bottom border-glass">
                        <span class="text-secondary small">{{ $row['label'] }}</span>
                        <span class="text-white small fw-medium">{{ $row['value'] }}</span>
                    </div>
                    @endforeach
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
                    <div class="score-badge {{ $scoreClass }}">
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
                        <div class="text-success fw-bold fs-4">{{ $employee->tasks_completed }}</div>
                        <div class="text-secondary smaller">{{ __('Tasks Done') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-pill">
                        @php $rate = $employee->tasks_requested > 0 ? round(($employee->tasks_completed / $employee->tasks_requested) * 100) : 0; @endphp
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
                    <span class="text-secondary smaller me-2">
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
                        <p class="text-light small mb-0 lh-lg">{{ $latestEvaluation->strengths ?? __('N/A') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="report-section report-weaknesses h-100">
                        <h6 class="text-danger mb-2"><i class="bi bi-graph-down-arrow me-2"></i>{{ __('Areas to Improve') }}</h6>
                        <p class="text-light small mb-0 lh-lg">{{ $latestEvaluation->weaknesses ?? __('N/A') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="report-section report-recs h-100">
                        <h6 class="text-info mb-2"><i class="bi bi-lightbulb-fill me-2"></i>{{ __('Recommendations') }}</h6>
                        <p class="text-light small mb-0 lh-lg">{{ $latestEvaluation->recommendations ?? __('N/A') }}</p>
                    </div>
                </div>
            </div>

            {{-- Full Report Text --}}
            @if($latestEvaluation->evaluation_text)
            <div class="mt-3 p-4 rounded-3" id="fullReportArea" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07)">
                <h6 class="text-secondary smaller text-uppercase mb-3">{{ __('Full Detailed Report') }}</h6>
                <div class="report-body" id="reportBodyContent">
                    {{-- rendered by JS via window.rawReportText --}}
                </div>
            </div>
            @php $rawReportText = $latestEvaluation->evaluation_text; @endphp
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

{{-- ===== Row 3: Evaluation History ===== --}}
@if($employee->evaluations->count() > 1)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="glass-card p-4 animate-up" style="animation-delay:0.3s">
            <h6 class="text-secondary text-uppercase smaller mb-3">{{ __('Evaluation History') }}</h6>
            @foreach($employee->evaluations->skip(1)->take(5) as $eval)
            <div class="eval-history-item d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-secondary small">{{ $eval->created_at->translatedFormat('M d, Y') }}</span>
                    <p class="text-white small mb-0 mt-1">{{ Str::limit($eval->evaluation_text, 100) }}</p>
                </div>
                <span class="badge ms-3 @if($eval->score >= 8) bg-success @elseif($eval->score >= 6) bg-primary @elseif($eval->score >= 4) bg-warning text-dark @else bg-danger @endif">
                    {{ $eval->score }}/10
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

</div>

{{-- ===== Floating AI Chat ===== --}}
<div class="floating-chat-container">
    <div class="floating-chat-window glass-card" id="floatingChatWindow" style="position:relative">
        <div class="chat-resize-handle" id="chatResizeHandle"></div>
        <div class="chat-header p-3 border-glass d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="ai-avatar-small"><i class="bi bi-robot"></i></div>
                <div>
                    <div class="text-white fw-bold smaller">Qayem AI</div>
                    <div class="text-success smaller" style="font-size:0.7rem"><i class="bi bi-circle-fill me-1"></i>{{ __('Online') }}</div>
                </div>
            </div>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-link text-secondary p-0 px-1" id="chatExpandBtn" title="Expand">
                    <i class="bi bi-arrows-fullscreen" style="font-size:0.85rem"></i>
                </button>
                <button class="btn btn-sm btn-link text-secondary p-0 px-1" onclick="document.getElementById('floatingChatWindow').classList.remove('active')" title="{{ __('Minimize') }}">
                    <i class="bi bi-dash-lg"></i>
                </button>
            </div>
        </div>
        <div class="chat-messages p-3 flex-grow-1 overflow-auto custom-scrollbar" id="chatMessages">
            <div class="chat-bubble ai animate-fade-in">
                {{ __('Hello :name! 👋 I\'m here to help you understand your performance and improve your skills. Ask me about your evaluation or any career question!', ['name' => explode(' ', $employee->name)[0]]) }}
            </div>
        </div>
        <div class="chat-input-area p-3 border-glass">
            <form id="employeeChatForm" class="d-flex gap-2">
                @csrf
                <input type="text" id="aiMessageInput" class="form-control bg-dark border-glass text-white rounded-pill px-3"
                    placeholder="{{ __('Ask anything...') }}" required autocomplete="off">
                <button type="submit" class="btn btn-primary rounded-circle p-0" style="width:40px;height:40px;">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>
    <button class="floating-chat-btn shadow-lg" id="floatingChatBtn" title="AI Assistant">
        <i class="bi bi-chat-dots-fill"></i>
    </button>
</div>

@endsection

@section('scripts')
<script>
    // ===== Markdown to HTML renderer =====
    @if(isset($rawReportText))
        (function() {
            const raw = (@json($rawReportText));
            let html = raw
                .replace(/\r\n/g, '\n')
                .replace(/### (.+)/g, '<h3>$1</h3>')
                .replace(/## (.+)/g, '<h2>$1</h2>')
                .replace(/# (.+)/g, '<h1>$1</h1>')
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.+?)\*/g, '<em>$1</em>')
                .replace(/^\d+\. (.+)/gm, '<li style="margin-bottom:4px">$1</li>')
                .replace(/^\* (.+)/gm, '<li style="margin-bottom:4px">$1</li>')
                .replace(/((?:<li[^>]*>.*<\/li>\n?)+)/g, '<ul>$1</ul>')
                .split('\n\n').map(p => p.startsWith('<') ? p : '<p>' + p + '</p>').join('');
            const el = document.getElementById('reportBodyContent');
            if (el) el.innerHTML = html;
        })();
    @endif

    // ===== Print Report =====
    function printReport() {
        window.print();
    }

    // ===== Floating Chat Toggle =====
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('floatingChatBtn');
        const win = document.getElementById('floatingChatWindow');
        const inp = document.getElementById('aiMessageInput');
        const expandBtn = document.getElementById('chatExpandBtn');

        // Toggle open/close
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            win.classList.toggle('active');
            if (win.classList.contains('active') && inp) inp.focus();
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!win.contains(e.target) && !btn.contains(e.target)) {
                win.classList.remove('active');
            }
        });

        // Expand toggle (cycle: normal → expanded → fullscreen → normal)
        let expandState = 0;
        expandBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            expandState = (expandState + 1) % 3;
            win.classList.remove('expanded', 'fullscreen');
            if (expandState === 1) {
                win.classList.add('expanded');
                expandBtn.title = 'Fullscreen';
            } else if (expandState === 2) {
                win.classList.add('fullscreen');
                expandBtn.title = 'Collapse';
            } else {
                expandBtn.title = 'Expand';
            }
        });

        // ===== Drag Resize =====
        const handle = document.getElementById('chatResizeHandle');
        let isDragging = false,
            startX, startY, startW, startH;

        handle.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            startW = win.offsetWidth;
            startH = win.offsetHeight;
            e.preventDefault();
        });
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            const dw = startX - e.clientX;
            const dh = startY - e.clientY;
            const newW = Math.min(700, Math.max(320, startW + dw));
            const newH = Math.min(window.innerHeight * 0.85, Math.max(400, startH + dh));
            win.style.width = newW + 'px';
            win.style.height = newH + 'px';
        });
        document.addEventListener('mouseup', () => {
            isDragging = false;
        });
    });

    // ===== AI Chat Submit =====
    document.getElementById('employeeChatForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const inp = document.getElementById('aiMessageInput');
        const container = document.getElementById('chatMessages');
        const message = inp.value.trim();
        if (!message) return;

        const userBubble = document.createElement('div');
        userBubble.className = 'chat-bubble user animate-fade-in';
        userBubble.innerText = message;
        container.appendChild(userBubble);
        inp.value = '';
        container.scrollTop = container.scrollHeight;

        const loadingBubble = document.createElement('div');
        loadingBubble.className = 'chat-bubble ai loading animate-fade-in';
        loadingBubble.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ' + (@json(__('Thinking...')));
        container.appendChild(loadingBubble);
        container.scrollTop = container.scrollHeight;

        try {
            const response = await fetch("{{ route('employee.ai.chat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message
                })
            });
            const result = await response.json();
            if (loadingBubble.parentNode) container.removeChild(loadingBubble);

            const aiBubble = document.createElement('div');
            aiBubble.className = 'chat-bubble ai animate-fade-in';
            aiBubble.innerText = result.response || (@json(__('Sorry, an error occurred.')));
            container.appendChild(aiBubble);
            container.scrollTop = container.scrollHeight;
        } catch (err) {
            if (loadingBubble.parentNode) container.removeChild(loadingBubble);
            const errBubble = document.createElement('div');
            errBubble.className = 'chat-bubble ai animate-fade-in';
            errBubble.innerText = (@json(__('Failed to get a response. Please try again.')));
            container.appendChild(errBubble);
        }
    });

    // ===== Employee Performance Trend Chart =====
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
                    pointRadius: 4,
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