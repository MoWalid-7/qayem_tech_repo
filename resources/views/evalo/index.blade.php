@extends('evalo.layout')

@section('title', 'Evalo - AI-Powered Performance Management')

@section('content')
@include('evalo.partials.nav')

<!-- Hero Section -->
<section class="qt-hero">
    <div class="container animate-up">
        <div class="row align-items-center">
            <div class="col-lg-7 text-start">
                <h1 class="display-3 fw-bold">{!! __('Elevate Every :span', ['span' => '<span>' . __('Performance') . '</span>']) !!}</h1>
                <p class="lead mb-4">{{ __('The all-in-one SaaS platform that uses Gemini AI to transform how you evaluate, mentor, and grow your workforce.') }}</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="btn-hero btn-hero-primary m-0">{{ __('Login') }}</a>
                    <a href="{{ route('about') }}" class="btn-hero btn-hero-outline m-0">{{ __('Our Mission') }}</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="glass-card p-5 text-center animate-up delay-1">
                    <div class="profile-avatar mb-3">Q</div>
                    <h5>{{ __('Smart Dashboard') }}</h5>
                    <p class="text-secondary small">{{ __('Real-time insights optimized for every role in your company.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Services Section -->
<section class="py-5 bg-dark">
    <div class="container py-5">
        <div class="text-center mb-5 animate-up">
            <h2 class="section-title">{{ __('Our Ecosystem') }}</h2>
            <p class="section-subtitle">{{ __('A comprehensive suite of tools designed for modern organizational excellence.') }}</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 animate-up delay-1">
                <div class="glass-card h-100">
                    <h5 class="text-primary-light mb-3">{{ __('AI Evaluation') }}</h5>
                    <p class="text-secondary small mb-4">{{ __('Unbiased, data-driven performance reviews powered by Gemini AI. Get sentiment analysis and trend tracking across all departments.') }}</p>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2">✓ {{ __('Sentiment Analysis') }}</li>
                        <li class="mb-2">✓ {{ __('Automated Scoring') }}</li>
                        <li>✓ {{ __('Multi-source Feedback') }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 animate-up delay-2">
                <div class="glass-card h-100">
                    <h5 class="text-primary-light mb-3">{{ __('Talent Mapping') }}</h5>
                    <p class="text-secondary small mb-4">{{ __('Visualize your workforce potential with our advanced skill matrix and succession planning tools.') }}</p>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2">✓ {{ __('Skill Gap Detection') }}</li>
                        <li class="mb-2">✓ {{ __('High-Potential ID') }}</li>
                        <li>✓ {{ __('Custom Goal Tracking') }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 animate-up delay-3">
                <div class="glass-card h-100">
                    <h5 class="text-primary-light mb-3">{{ __('Managerial Insights') }}</h5>
                    <p class="text-secondary small mb-4">{{ __('Empower your leaders with real-time department health dashboards and bottleneck identification.') }}</p>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2">✓ {{ __('Department Health Index') }}</li>
                        <li class="mb-2">✓ {{ __('Team Velocity Reports') }}</li>
                        <li>✓ {{ __('1-on-1 Prep Assistant') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-5 bg-gradient">
    <div class="container py-5">
        <div class="text-center mb-5 animate-up">
            <h2 class="section-title">{{ __('How It Works') }}</h2>
            <p class="section-subtitle">{{ __('Simplified workflows for complex performance data.') }}</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 animate-up delay-1">
                <div class="glass-card h-100 border-0 bg-dark-surface">
                    <div class="text-primary-light fs-1 mb-3">01</div>
                    <h4>{{ __('Register Company') }}</h4>
                    <p class="text-secondary">{{ __('Set up your enterprise profile and select a plan that fits your employee count.') }}</p>
                </div>
            </div>
            <div class="col-md-4 animate-up delay-2">
                <div class="glass-card h-100 border-0 bg-dark-surface">
                    <div class="text-primary-light fs-1 mb-3">02</div>
                    <h4>{{ __('Onboard Team') }}</h4>
                    <p class="text-secondary">{{ __('Import employees and define roles from Department Managers to HR Specialists.') }}</p>
                </div>
            </div>
            <div class="col-md-4 animate-up delay-3">
                <div class="glass-card h-100 border-0 bg-dark-surface">
                    <div class="text-primary-light fs-1 mb-3">03</div>
                    <h4>{{ __('Grow Together') }}</h4>
                    <p class="text-secondary">{{ __('Use AI advisory and real-time evaluations to drive career development.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-dark border-top border-secondary border-opacity-10">
    <div class="container py-5">
        <div class="text-center mb-5 animate-up">
            <h2 class="section-title">{{ __('What Our Clients Say') }}</h2>
            <p class="section-subtitle">{{ __('Trusted by forward-thinking HR leaders worldwide.') }}</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 animate-up delay-1">
                <div class="glass-card h-100">
                    <div class="mb-3 text-warning">★★★★★</div>
                    <p class="text-secondary italic">"{{ __('Evalo has completely transformed how we handle employee growth. The AI advisory is a game-changer for our managers.') }}"</p>
                    <hr class="border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-avatar mb-0" style="width: 40px; height: 40px; font-size: 1rem;">JS</div>
                        <div>
                            <h6 class="mb-0">{{ __('Jane Smith') }}</h6>
                            <p class="small text-secondary mb-0">{{ __('CHRO, TechGlobal') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 animate-up delay-2">
                <div class="glass-card h-100">
                    <div class="mb-3 text-warning">★★★★★</div>
                    <p class="text-secondary italic">"{{ __('The role-based navigation makes it so easy for everyone to see exactly what they need. Best HR tool we\'ve used in years.') }}"</p>
                    <hr class="border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-avatar mb-0" style="width: 40px; height: 40px; font-size: 1rem;">MK</div>
                        <div>
                            <h6 class="mb-0">{{ __('Mark Khalid') }}</h6>
                            <p class="small text-secondary mb-0">{{ __('CEO, Innovate Ltd') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 animate-up delay-3">
                <div class="glass-card h-100">
                    <div class="mb-3 text-warning">★★★★★</div>
                    <p class="text-secondary italic">"{{ __('Scaling our department evaluations was a breeze. The data insights are sharp and actionable. Highly recommend.') }}"</p>
                    <hr class="border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-avatar mb-0" style="width: 40px; height: 40px; font-size: 1rem;">LA</div>
                        <div>
                            <h6 class="mb-0">{{ __('Laila Ahmed') }}</h6>
                            <p class="small text-secondary mb-0">{{ __('HR Director, NileSoft') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-gradient">
    <div class="container py-5">
        <div class="text-center mb-5 animate-up">
            <h2 class="section-title">{{ __('Common Questions') }}</h2>
            <p class="section-subtitle">{{ __('Everything you need to know about getting started.') }}</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-flush glass-card p-0 overflow-hidden" id="faqAccordion">
                    <div class="accordion-item bg-transparent border-bottom border-secondary border-opacity-10">
                        <h2 class="accordion-header">
                            <button class="accordion-button bg-transparent text-white collapsed py-4 px-4 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                {{ __('How long does integration take?') }}
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary px-4 pb-4">
                                {{ __('Integration is instantaneous. Once you subscribe, you can immediately begin adding employees and departments to start the evaluation process.') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent border-bottom border-secondary border-opacity-10">
                        <h2 class="accordion-header">
                            <button class="accordion-button bg-transparent text-white collapsed py-4 px-4 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                {{ __('Can I change my plan later?') }}
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary px-4 pb-4">
                                {{ __('Yes, you can upgrade or downgrade your plan at any time through the HR Dashboard. Changes are applied at the start of your next billing cycle.') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button bg-transparent text-white collapsed py-4 px-4 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                {{ __('Is our employee data secure?') }}
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary px-4 pb-4">
                                {{ __('Absolutely. We use enterprise-grade 256-bit encryption for all stored data and comply with global data protection standards to ensure the highest level of security.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 text-center">
    <div class="container py-5 animate-up">
        <h2 class="mb-4">{{ __('Ready to optimize your team?') }}</h2>
        <p class="lead text-secondary mb-5">{{ __('Join hundreds of companies using Evalo to build a better workplace.') }}</p>
        <a href="{{ route('login') }}" class="btn-hero btn-hero-primary px-5">{{ __('Login Now') }}</a>
    </div>
</section>

@include('evalo.partials.footer')
@endsection