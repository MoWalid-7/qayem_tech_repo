@extends('evalo.layout')

@section('title', __('About Us') . ' - ' . __('Evalo'))

@section('content')
@include('evalo.partials.nav')

<section class="qt-hero" style="height: 60vh; min-height: 400px;">
    <div class="container animate-up">
        <h1 class="display-3">{{ __('Empowering Human Potential') }}</h1>
        <p class="lead">{{ __('We combine peak technology with human-centric insights to revolutionize performance management.') }}</p>
    </div>
</section>

<section class="py-5 bg-dark">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 animate-up">
                <h2 class="section-title text-start mb-4">{{ __('Our Mission') }}</h2>
                <p class="text-secondary mb-4">
                    {{ __("Founded in 2026, Evalo was born out of a simple realization: performance management is broken. Most systems focus on metrics that don't matter, ignoring the growth potential of individuals.") }}
                </p>
                <p class="text-secondary">
                    {{ __("Our mission is to bridge that gap by using AI to provide meaningful, actionable insights that help employees grow and companies thrive. We believe that when people understand their value, they bring their best selves to work.") }}
                </p>
            </div>
            <div class="col-lg-6 animate-up delay-1">
                <div class="glass-card">
                    <h4 class="mb-4 text-gradient">{{ __('The Evalo Advantage') }}</h4>
                    <div class="mb-3">
                        <h6 class="text-primary-light">✓ {{ __('AI-First Approach') }}</h6>
                        <p class="text-secondary small">{{ __('Deep integration with Gemini AI for real-time advisory.') }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary-light">✓ {{ __('Seamless Integration') }}</h6>
                        <p class="text-secondary small">{{ __('Fits perfectly with your existing HR workflows.') }}</p>
                    </div>
                    <div>
                        <h6 class="text-primary-light">✓ {{ __('Ethical Data Design') }}</h6>
                        <p class="text-secondary small">{{ __('Security and privacy are at our core.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-gradient">
    <div class="container text-center">
        <h2 class="page-title mb-5">{{ __('How We Build Trust') }}</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value">{{ __('99.9%') }}</div>
                    <div class="stat-label">{{ __('Uptime') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value">{{ __('256-bit') }}</div>
                    <div class="stat-label">{{ __('Encryption') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value">{{ __('24/7') }}</div>
                    <div class="stat-label">{{ __('Support') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value">{{ __('Zero') }}</div>
                    <div class="stat-label">{{ __('Data Leaks') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-dark">
    <div class="container py-5">
        <div class="text-center mb-5 animate-up">
            <h2 class="section-title">{{ __('Our Core Values') }}</h2>
            <p class="section-subtitle">{{ __('What drives us every single day.') }}</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 animate-up delay-1">
                <div class="glass-card text-center">
                    <div class="fs-1 mb-3">💡</div>
                    <h5>{{ __('Innovation') }}</h5>
                    <p class="text-secondary small">{{ __('We are constantly pushing the boundaries of what is possible in HR tech.') }}</p>
                </div>
            </div>
            <div class="col-md-4 animate-up delay-2">
                <div class="glass-card text-center">
                    <div class="fs-1 mb-3">🤝</div>
                    <h5>{{ __('Integrity') }}</h5>
                    <p class="text-secondary small">{{ __('Transparency and trust are at the heart of everything we build.') }}</p>
                </div>
            </div>
            <div class="col-md-4 animate-up delay-3">
                <div class="glass-card text-center">
                    <div class="fs-1 mb-3">🚀</div>
                    <h5>{{ __('Impact') }}</h5>
                    <p class="text-secondary small">{{ __('We measure our success by the growth and well-being of the people who use our tools.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-gradient">
    <div class="container py-5 text-center">
        <h2 class="section-title mb-5">{{ __('Meet the Visionaries') }}</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-3 animate-up delay-1">
                <div class="glass-card p-4">
                    <div class="profile-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">QA</div>
                    <h6>{{ __('Evalo Al-Awwal') }}</h6>
                    <p class="text-primary-light small mb-0">{{ __('Founder & CEO') }}</p>
                </div>
            </div>
            <div class="col-md-3 animate-up delay-2">
                <div class="glass-card p-4">
                    <div class="profile-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">SM</div>
                    <h6>{{ __('Sarah Mitchell') }}</h6>
                    <p class="text-primary-light small mb-0">{{ __('CTO') }}</p>
                </div>
            </div>
            <div class="col-md-3 animate-up delay-3">
                <div class="glass-card p-4">
                    <div class="profile-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">DA</div>
                    <h6>{{ __('David Ahmed') }}</h6>
                    <p class="text-primary-light small mb-0">{{ __('Head of Product') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

@include('evalo.partials.footer')
@endsection