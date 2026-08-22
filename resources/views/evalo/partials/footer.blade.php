<footer class="qt-footer">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-4">
                <a href="{{ route('home') }}" class="navbar-brand d-inline-block mb-3">{{ __('Evalo') }}</a>
                <p class="text-secondary small">{{ __('Leading the way in AI-driven HR technology since 2026.') }}</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="fs-5">🐦</a>
                    <a href="#" class="fs-5">💼</a>
                    <a href="#" class="fs-5">📷</a>
                </div>
            </div>
            <div class="col-lg-2 offset-lg-2">
                <h6>{{ __('Company') }}</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('about') }}">{{ __('Our Team') }}</a></li>
                    <li><a href="#">{{ __('Careers') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-2">
                <h6>{{ __('Product') }}</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}">{{ __('Features') }}</a></li>
                    <li><a href="{{ route('about') }}">{{ __('Security') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-2">
                <h6>{{ __('Support') }}</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
                    <li><a href="#">{{ __('Help Center') }}</a></li>
                    <li><a href="#">{{ __('API Docs') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center pt-4 border-top border-secondary border-opacity-25">
            <p class="text-secondary small">{{ __('© 2026 Evalo. All rights reserved.') }}</p>
        </div>
    </div>
</footer>