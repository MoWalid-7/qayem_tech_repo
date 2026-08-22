@extends('evalo.layout')

@section('title', __('Login') . ' - ' . __('Evalo'))
@section('body_class', 'auth-wrapper')

@section('content')
<!-- Navbar -->
<nav class="qt-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="{{ route('home') }}">{{ __('Evalo') }}</a>

    </div>
</nav>

<div class="auth-card animate-up">
    <h2>{{ __('Welcome Back') }}</h2>
    <p class="subtitle">{{ __('Please login to access your dashboard') }}</p>

    <form id="loginForm">
        @csrf
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" placeholder=" " required>
            <label for="email">{{ __('Email Address') }}</label>
        </div>
        <div class="form-floating mb-3 position-relative">
            <input type="password" class="form-control" id="password" placeholder=" " required style="padding-right: 2.5rem;">
            <label for="password">{{ __('Password') }}</label>
            <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-secondary text-decoration-none shadow-none" id="togglePassword" style="z-index: 10;">
                <i class="bi bi-eye"></i>
            </button>
        </div>

        <button type="submit" class="btn-auth">{{ __('Login to Dashboard') }}</button>

        <div class="text-center mt-4">

        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Password toggle logic
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });


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