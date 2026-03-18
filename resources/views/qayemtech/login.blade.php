@extends('qayemtech.layout')

@section('title', __('Login') . ' - ' . __('QayemTech'))
@section('body_class', 'auth-wrapper')

@section('content')
<!-- Navbar -->
<nav class="qt-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="{{ route('home') }}">{{ __('QayemTech') }}</a>
        <a href="{{ route('subscribe') }}" class="nav-link">{{ __('Subscribe') }}</a>
    </div>
</nav>

<div class="auth-card animate-up">
    <h2>{{ __('Welcome Back') }}</h2>
    <p class="subtitle">{{ __('Please login to access your dashboard') }}</p>

    <form id="loginForm">
        @csrf
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" placeholder="{{ __('name@example.com') }}" required>
            <label for="email">{{ __('Email Address') }}</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" placeholder="{{ __('Password') }}" required>
            <label for="password">{{ __('Password') }}</label>
        </div>

        <button type="submit" class="btn-auth">{{ __('Login to Dashboard') }}</button>

        <div class="text-center mt-4">
            <p class="text-secondary small">{{ __('Don\'t have an account?') }} <a href="{{ route('subscribe') }}" class="text-primary-light text-decoration-none">{{ __('Subscribe') }}</a></p>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('.btn-auth');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> {{ __("Logging in...") }}';

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('{{ route("login.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email,
                    password
                })
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect;
            } else {
                showToast(result.message || '{{ __("Login failed") }}', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __("Login to Dashboard") }}';
            }
        } catch (err) {
            showToast('{{ __("Something went wrong. Please try again.") }}', 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '{{ __("Login to Dashboard") }}';
        }
    });

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `qt-toast bg-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endsection