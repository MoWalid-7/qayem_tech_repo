@extends('evalo.layout')

@section('title', __('Contact Us') . ' - ' . __('Evalo'))

@section('content')
@include('evalo.partials.nav')

<section class="qt-hero" style="height: 50vh; min-height: 350px;">
    <div class="container animate-up">
        <h1 class="display-4">{{ __('Get in Touch') }}</h1>
        <p class="lead">{{ __('Have questions or need support? Our team is here to help you optimize your team.') }}</p>
    </div>
</section>

<section class="py-5 bg-dark">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-5 animate-up">
                <h2 class="section-title text-start mb-4">{{ __('Contact Information') }}</h2>
                <p class="text-secondary mb-5">{{ __('Reach out to us through any of these channels. We typically respond within 24 hours.') }}</p>

                <div class="d-flex flex-column gap-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="glass-card p-3 rounded-circle mb-0" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <span class="fs-4">📧</span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ __('Email Support') }}</h6>
                            <p class="text-secondary small mb-0">support@evalo.com</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="glass-card p-3 rounded-circle mb-0" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <span class="fs-4">📞</span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ __('Phone') }}</h6>
                            <p class="text-secondary small mb-0">+1 (555) 123-4567</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="glass-card p-3 rounded-circle mb-0" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <span class="fs-4">📍</span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ __('Office') }}</h6>
                            <p class="text-secondary small mb-0">{{ __('123 Innovation Drive, Cairo, Egypt') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 animate-up delay-1">
                <div class="glass-card">
                    <h4 class="mb-4">{{ __('Send us a Message') }}</h4>
                    <form id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="name" placeholder="{{ __('Your Name') }}" required>
                                    <label for="name">{{ __('Your Name') }}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" placeholder="{{ __('name@example.com') }}" required>
                                    <label for="email">{{ __('Email Address') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="subject" placeholder="{{ __('Subject') }}" required>
                            <label for="subject">{{ __('Subject') }}</label>
                        </div>
                        <div class="form-floating mb-4">
                            <textarea class="form-control" id="message" placeholder="{{ __('Your Message') }}" style="height: 150px" required></textarea>
                            <label for="message">{{ __('Your Message') }}</label>
                        </div>
                        <button type="submit" class="btn-auth">{{ __('Send Message') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@include('evalo.partials.footer')
@endsection

@section('scripts')
<script>
    document.getElementById('contactForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = '{{ __("Sending...") }}';

        const data = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value,
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route("contact.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    showToast(res.message);
                    this.reset();
                } else {
                    showToast(res.message || 'Error sending message', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('{{ __("Sorry, an error occurred.") }}', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = originalText;
            });
    });
</script>
@endsection