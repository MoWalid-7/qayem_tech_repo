<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Evalo HRMS')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    @if(app()->getLocale() == 'ar')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Modern CSS -->
    <link href="{{ asset('evalo/css/modern.css') }}?v=1.0.0" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    @yield('styles')
</head>

<body class="modern-body @yield('body_class')">
    <div class="hrms-wrapper">
        
        <!-- Sidebar -->
        <aside class="hrms-sidebar glass-panel">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="bi bi-hexagon-fill"></i>
                </div>
                <span class="brand-text">Evalo<span class="text-primary">HRMS</span></span>
            </div>

            <nav class="sidebar-nav mt-4">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>
                
                @if(Auth::guard('manager')->check() || Auth::guard('hr')->check())
                <div class="nav-label mt-4">{{ __('Management') }}</div>
                <a href="{{ route('hrms.employees') }}" class="nav-item {{ request()->routeIs('hrms.employees') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>{{ __('Employees') }}</span>
                </a>
                <a href="{{ route('hrms.departments') }}" class="nav-item {{ request()->routeIs('hrms.departments') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>{{ __('Departments') }}</span>
                </a>
                <a href="{{ route('hrms.attendance') }}" class="nav-item {{ request()->routeIs('hrms.attendance') ? 'active' : '' }}">
                    <i class="bi bi-fingerprint"></i>
                    <span>{{ __('Attendance') }}</span>
                </a>
                <a href="{{ route('hrms.leave') }}" class="nav-item {{ request()->routeIs('hrms.leave') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-check"></i>
                    <span>{{ __('Leave Requests') }}</span>
                </a>
                <a href="{{ route('hrms.payroll') }}" class="nav-item {{ request()->routeIs('hrms.payroll') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i>
                    <span>{{ __('Payroll') }}</span>
                </a>
                @endif
                
                @if(Auth::guard('employee')->check())
                <a href="{{ route('employee.profile') }}" class="nav-item {{ request()->routeIs('employee.profile') ? 'active' : '' }}">
                    <i class="bi bi-person-vcard"></i>
                    <span>{{ __('My Profile') }}</span>
                </a>
                @endif
            </nav>
            
            <!-- User Info at bottom of sidebar -->
            @php $user = Auth::guard('manager')->user() ?? Auth::guard('hr')->user() ?? Auth::guard('employee')->user(); @endphp
            @if($user)
            <div class="sidebar-user mt-auto">
                <div class="user-avatar">{{ substr($user->name, 0, 1) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-role">{{ class_basename($user) }}</div>
                </div>
                <a href="{{ route('logout') }}" class="logout-btn" title="{{ __('Logout') }}">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
            @endif
        </aside>

        <!-- Main Content -->
        <main class="hrms-main">
            <!-- Topbar -->
            <header class="hrms-topbar glass-panel">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="page-title">
                        <h4 class="mb-0 fw-bold">@yield('page_title', __('Dashboard'))</h4>
                        <div class="text-secondary small">{{ now()->translatedFormat('l, d F Y') }}</div>
                    </div>
                    
                    <div class="topbar-actions d-flex align-items-center gap-3">
                        <button class="action-btn" id="themeToggleBtn" title="{{ __('Toggle Dark/Light Mode') }}">
                            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                        </button>
                        <div class="dropdown">
                            <button class="action-btn" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-translate"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end glass-dropdown">
                                <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">English</a></li>
                                <li><a class="dropdown-item" href="{{ route('lang.switch', 'ar') }}">العربية</a></li>
                            </ul>
                        </div>
                        <button class="action-btn position-relative">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

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
        'routes' => ['aiChat' => route('ai.chat')],
        '_i18n' => [
            'thinking' => __('Thinking...'),
            'errorMsg' => __('Sorry, I encountered an error.')
        ]
    ];
    @endphp
    <script id="evalo-base-config" type="application/json">@json($evaloBaseConfig)</script>
    <script>
        window.EvaloConfig = Object.assign(window.EvaloConfig || {}, JSON.parse(document.getElementById('evalo-base-config').textContent));
    </script>

    <!-- AI Floating Chat Widget -->
    <div class="floating-chat-container">
        <div class="floating-chat-window glass-panel" id="floatingChatWindow" style="
            position: fixed;
            bottom: 5rem;
            right: 1.5rem;
            width: 340px;
            height: 450px;
            border-radius: 18px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 9998;
        ">
            <div class="p-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--glass-border)">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:.9rem;color:#fff">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div>
                        <div class="text-white fw-bold" style="font-size:.82rem">Evalo AI</div>
                        <div class="text-success" style="font-size:.65rem"><i class="bi bi-circle-fill me-1"></i>{{ __('Online') }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="action-btn" id="maximizeChat" title="{{ __('Maximize') }}" style="width:28px;height:28px;font-size:.75rem">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                    <button class="action-btn" onclick="document.getElementById('floatingChatWindow').style.display='none'" style="width:28px;height:28px;font-size:.75rem">
                        <i class="bi bi-dash-lg"></i>
                    </button>
                </div>
            </div>
            <div class="chat-messages p-3 flex-grow-1 overflow-auto custom-scrollbar" id="chatMessages" style="flex:1;overflow-y:auto">
                <div class="chat-bubble ai animate-fade-in shadow-sm" style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:12px;padding:.65rem 1rem;margin-bottom:.5rem;font-size:.85rem;color:var(--text-primary)">
                    {{ __('Hello :name! 👋 How can I help you today?', ['name' => explode(' ', $user->name)[0]]) }}
                </div>
            </div>
            <div class="p-3" style="border-top: 1px solid var(--glass-border)">
                <form id="aiChatForm" class="d-flex gap-2">
                    @csrf
                    <input type="text" id="aiMessageInput" class="form-control flex-grow-1" placeholder="{{ __('Ask anything...') }}" required autocomplete="off" style="border-radius:10px">
                    <button type="submit" class="btn-modern primary" style="border-radius:10px;padding:.45rem .8rem;flex-shrink:0">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>
        </div>

        <button class="floating-chat-btn" id="floatingChatBtn" title="AI Assistant"
            onclick="const w=document.getElementById('floatingChatWindow'); w.style.display = w.style.display==='flex'?'none':'flex'">
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
                themeIcon.classList.replace('bi-moon-stars-fill', 'bi-brightness-high-fill');
            }

            themeBtn.addEventListener('click', () => {
                htmlEl.classList.toggle('light-mode');
                if (htmlEl.classList.contains('light-mode')) {
                    localStorage.setItem('evalo-theme', 'light');
                    themeIcon.classList.replace('bi-moon-stars-fill', 'bi-brightness-high-fill');
                } else {
                    localStorage.setItem('evalo-theme', 'dark');
                    themeIcon.classList.replace('bi-brightness-high-fill', 'bi-moon-stars-fill');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
