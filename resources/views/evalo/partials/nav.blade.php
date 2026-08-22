<nav class="navbar navbar-expand-lg qt-navbar">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ __('Evalo') }}</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#qtNavbar" aria-controls="qtNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="qtNavbar">
            <div class="navbar-nav mx-auto gap-lg-3">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">{{ __('Home') }}</a>
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">{{ __('About') }}</a>
                <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">{{ __('Contact') }}</a>
                <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">{{ __('Login') }}</a>
            </div>

            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                <button class="btn btn-sm btn-outline-light me-2" id="themeToggleBtn" title="{{ __('Toggle Theme') }}">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-translate me-1"></i> {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">English</a></li>
                        <li><a class="dropdown-item" href="{{ route('lang.switch', 'ar') }}">العربية</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</nav>