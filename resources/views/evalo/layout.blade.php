<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Evalo - SaaS Platform')</title>
    <!-- Bootstrap 5 -->
    @if(app()->getLocale() == 'ar')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link href="{{ asset('evalo/css/styles.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('evalo/css/modern.css') }}?v={{ time() }}" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://js.stripe.com/v3/"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Marked.js for AI Markdown -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    @yield('styles')
</head>

<body class="@yield('body_class') modern-body">
    @yield('content')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('evalo/js/app.js') }}"></script>
    @php
    $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user() ?? Auth::guard('employee')->user();
    @endphp

    @if($user)
    @php
    $evaloBaseConfig = [
    'csrfToken' => csrf_token(),
    'routes' => [
    'aiChat' => route("ai.chat"),
    ],
    '_i18n' => [
    'thinking' => __("Thinking..."),
    'errorMsg' => __("Sorry, I encountered an error.")
    ]
    ];
    @endphp
    <script id="evalo-base-config" type="application/json">
        @json($evaloBaseConfig)
    </script>
    <script>
        window.EvaloConfig = Object.assign(window.EvaloConfig || {}, JSON.parse(document.getElementById('evalo-base-config').textContent));
    </script>

    <!-- Floating AI Chat Widget -->
    <div class="floating-chat-container">
        <div class="floating-chat-window glass-card" id="floatingChatWindow">
            <div class="chat-header p-3 border-glass d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="ai-avatar-small"><i class="bi bi-robot"></i></div>
                    <div>
                        <div class="text-white fw-bold smaller">Evalo AI</div>
                        <div class="text-success smaller" style="font-size: 0.7rem;"><i class="bi bi-circle-fill me-1"></i> {{ __('Online') }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-link text-secondary p-0" id="maximizeChat" title="{{ __('Maximize') }}">
                        <i class="bi bi-arrows-fullscreen" style="font-size: 0.8rem;"></i>
                    </button>
                    <button class="btn btn-sm btn-link text-secondary p-0" onclick="document.getElementById('floatingChatWindow').classList.remove('active')" title="{{ __('Minimize') }}">
                        <i class="bi bi-dash-lg"></i>
                    </button>
                </div>
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
    @endif

    <script>
        // Theme Toggle Logic
        document.addEventListener('DOMContentLoaded', () => {
            const htmlEl = document.documentElement;
            const themeBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');
            
            // Check local storage
            const savedTheme = localStorage.getItem('evalo-theme');
            if (savedTheme === 'light') {
                htmlEl.classList.add('light-mode');
                if(themeIcon) themeIcon.classList.replace('bi-moon-stars-fill', 'bi-brightness-high-fill');
            }

            if(themeBtn) {
                themeBtn.addEventListener('click', () => {
                    htmlEl.classList.toggle('light-mode');
                    if (htmlEl.classList.contains('light-mode')) {
                        localStorage.setItem('evalo-theme', 'light');
                        if(themeIcon) themeIcon.classList.replace('bi-moon-stars-fill', 'bi-brightness-high-fill');
                    } else {
                        localStorage.setItem('evalo-theme', 'dark');
                        if(themeIcon) themeIcon.classList.replace('bi-brightness-high-fill', 'bi-moon-stars-fill');
                    }
                });
            }
        });
    </script>

    @yield('scripts')
</body>

</html>