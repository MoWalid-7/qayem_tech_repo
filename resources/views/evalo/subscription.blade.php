@extends('evalo.layout')

@section('title', 'Subscribe - Evalo')
@section('body_class', 'sub-wrapper')

@section('content')
@include('evalo.partials.nav')

<div class="container mt-5">
    <div class="text-center mb-5 animate-up">
        <h1 class="page-title">{{ __('Transform Your Enterprise') }}</h1>
        <p class="text-secondary" id="sub-subtitle">{{ __('Complete the 3-step registration process to get started.') }}</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="glass-card p-0 overflow-hidden animate-up delay-1">
                <div class="row g-0">
                    <!-- Left Info Panel -->
                    <div class="col-lg-4 bg-primary p-5 text-white d-none d-lg-block">
                        <h4 class="mb-4">{{ __('Why Evalo?') }}</h4>
                        <ul class="list-unstyled">
                            <li class="mb-4 d-flex gap-3">
                                <span class="fs-4">🚀</span>
                                <div>
                                    <h6>{{ __('Instant Setup') }}</h6>
                                    <p class="small text-white-50">{{ __('Go live in under 5 minutes.') }}</p>
                                </div>
                            </li>
                            <li class="mb-4 d-flex gap-3">
                                <span class="fs-4">🛡️</span>
                                <div>
                                    <h6>{{ __('Secure Payments') }}</h6>
                                    <p class="small text-white-50">{{ __('PCI-compliant mock gateway.') }}</p>
                                </div>
                            </li>
                            <li class="d-flex gap-3">
                                <span class="fs-4">📈</span>
                                <div>
                                    <h6>{{ __('Unified Dashboard') }}</h6>
                                    <p class="small text-white-50">{{ __('All insights in one place.') }}</p>
                                </div>
                            </li>
                        </ul>

                        <hr class="border-white border-opacity-25 my-4">

                        <div class="mt-4">
                            <h6>{{ __('Need a Custom Plan?') }}</h6>
                            <p class="small text-white-50">{{ __('Contact our sales team for enterprises with 500+ employees.') }}</p>
                            <a href="{{ route('contact') }}" class="btn btn-sm btn-light w-100 mt-2">{{ __('Contact Sales') }}</a>
                        </div>
                    </div>

                    <!-- Right Form Panel -->
                    <div class="col-lg-8 p-4 p-md-5">
                        <form id="multiStepSubForm" action="{{ route('subscribe.process') }}" method="POST">
                            @csrf
                            <!-- Step 1: Company Info -->
                            <div id="step1" class="step">
                                <h4 class="mb-4">{{ __('Step 1: Company Details') }}</h4>
                                <div class="form-floating mb-3">
                                    <input type="text" name="company_name" class="form-control" placeholder="TechCorp" required>
                                    <label>{{ __('Company Name') }}</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" name="admin_name" class="form-control" placeholder="John Doe" required>
                                    <label>{{ __('Admin Full Name') }}</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="admin@company.com" required>
                                    <label>{{ __('Business Email') }}</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="tel" name="phone" class="form-control" placeholder="+1234567890" required>
                                    <label>{{ __('Phone Number') }}</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <textarea name="address" class="form-control" style="height: 100px" placeholder="{{ __('Company Address') }}" required></textarea>
                                    <label>{{ __('Company Address') }}</label>
                                </div>
                                <button type="button" class="btn-auth" onclick="nextStep(2)">{{ __('Next: Plan Selection') }}</button>
                            </div>

                            <!-- Step 2: Plan Selection -->
                            <div id="step2" class="step d-none">
                                <h4 class="mb-4">{{ __('Step 2: Choose Your Plan') }}</h4>
                                <div class="row g-3 mb-4">
                                    @foreach($plans as $plan)
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check plan-selector" name="plan_id" id="plan{{ $plan->id }}"
                                            value="{{ $plan->id }}"
                                            data-name="{{ __($plan->name) }}"
                                            data-price="{{ $plan->price_per_employee }}"
                                            @if($loop->first) checked @endif>
                                        <label class="btn btn-outline-primary w-100 p-4 h-100 text-start" for="plan{{ $plan->id }}">
                                            <h5 class="mb-1">{{ __($plan->name) }}</h5>
                                            <p class="mb-1 text-primary-light fs-4 fw-bold">${{ $plan->price_per_employee }}<small class="fs-6 text-secondary fw-normal">/{{ __('user') }}</small></p>
                                            <p class="small text-secondary mb-0">{{ __('For :min-:max employees.', ['min' => $plan->min_employees, 'max' => $plan->max_employees]) }}</p>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>



                                <div class="d-flex gap-3">
                                    <button type="button" class="btn btn-outline-secondary w-50 py-3 rounded-4" onclick="nextStep(1)">{{ __('Back') }}</button>
                                    <button type="button" class="btn-auth w-50 mt-0" onclick="nextStep(3)">{{ __('Next: Payment') }}</button>
                                </div>
                            </div>

                            <!-- Step 3: Payment UI -->
                            <div id="step3" class="step d-none">
                                <h4 class="mb-4">{{ __('Step 3: Secure Checkout') }}</h4>
                                <div class="mb-4 p-3 rounded-4 bg-dark-surface border border-secondary border-opacity-25">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-secondary">{{ __('Summary') }}</span>
                                        <span id="summaryPlan" class="badge bg-primary">{{ __('Selected Plan') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-secondary">{{ __('Grand Total') }}</span>
                                        <span class="fw-bold" id="grandTotal">$0.00</span>
                                    </div>
                                </div>

                                <!-- Stripe Element Container -->
                                <div class="mb-4">
                                    <label class="text-secondary small mb-2 d-block">{{ __('Credit or Debit Card') }}</label>
                                    <div id="card-element" class="form-control py-3" style="background: var(--dark-surface); border: 1px solid var(--glass-border);">
                                        <!-- A Stripe Element will be inserted here. -->
                                    </div>
                                    <!-- Used to display form errors. -->
                                    <div id="card-errors" role="alert" class="text-danger small mt-2"></div>
                                </div>

                                <div class="d-flex gap-3">
                                    <button type="button" class="btn btn-outline-secondary w-50 py-3 rounded-4" onclick="nextStep(2)">{{ __('Back') }}</button>
                                    <button type="submit" id="submit-button" class="btn-auth w-50 mt-0">{{ __('Pay & Subscribe') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const elements = stripe.elements();

    const style = {
        base: {
            color: '#f8fafc',
            fontFamily: '"Inter", sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#94a3b8'
            }
        },
        invalid: {
            color: '#ef4444',
            iconColor: '#ef4444'
        }
    };

    const card = elements.create('card', {
        style: style,
        hidePostalCode: true
    });
    card.mount('#card-element');

    card.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    function nextStep(step) {
        if (step === 3) {
            updateSummary();
        }
        document.querySelectorAll('.step').forEach(s => s.classList.add('d-none'));
        document.getElementById('step' + step).classList.remove('d-none');
    }

    function updateSummary() {
        const selected = document.querySelector('.plan-selector:checked');
        if (selected) {
            document.getElementById('summaryPlan').textContent = selected.dataset.name;
            document.getElementById('grandTotal').textContent = '$' + parseFloat(selected.dataset.price).toFixed(2);
        }
    }

    // Initialize summary on load
    document.addEventListener('DOMContentLoaded', updateSummary);

    document.getElementById('multiStepSubForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('submit-button');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

        const {
            paymentMethod,
            error
        } = await stripe.createPaymentMethod(
            'card', card
        );

        if (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Pay & Subscribe';
            return;
        }

        const formData = new FormData(this);
        formData.append('payment_method_id', paymentMethod.id);

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });

            const result = await response.json();

            if (result.success) {
                // Show success state
                document.getElementById('sub-subtitle').innerHTML = `<span class="text-success fw-bold">Success!</span> ${result.message}`;

                const emailVal = result.credentials?.email || formData.get('email') || '';
                const passwordVal = result.credentials?.password || 'Sent to email';

                const stepFormContainer = document.querySelector('.col-lg-8');
                stepFormContainer.innerHTML = `
                    <div class="text-center py-5 animate-up">
                        <div class="mb-4">
                            <span class="display-1">🎉</span>
                        </div>
                        <h4 class="mb-3">Welcome to Evalo!</h4>
                        <p class="text-secondary mb-4">Your administrative account has been created successfully.</p>
                        
                        <div class="glass-card mb-4 p-4 text-start bg-success bg-opacity-10 border-success border-opacity-25" style="max-width: 400px; margin: 0 auto; border: 1px solid rgba(25, 135, 84, 0.2); border-radius: 16px;">
                            <h6 class="mb-3 text-success">Login Credentials:</h6>
                            <div class="mb-2">
                                <small class="text-secondary d-block">Business Email</small>
                                <strong class="text-white">${emailVal}</strong>
                            </div>
                            <div class="mt-3">
                                <small class="text-secondary d-block">Temporary Password</small>
                                <strong class="text-white font-monospace">${passwordVal}</strong>
                            </div>
                        </div>
                        
                        <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info smaller mb-4" style="max-width: 400px; margin: 0 auto; font-size: 0.85rem;">
                            <i class="bi bi-info-circle me-2"></i>
                            Please save these credentials. You can change your password after your first login.
                        </div>

                        <a href="${result.redirect || '#'}" class="btn-auth decoration-none d-inline-block" style="text-decoration: none;">Go to Dashboard & Login</a>
                    </div>
                `;
            } else {
                showToast(result.message || 'Payment failed.', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Pay & Subscribe';
            }
        } catch (err) {
            console.error('Subscription Submission Error:', err);
            showToast('Something went wrong during payment processing.', 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Pay & Subscribe';
        }
    });
</script>
@endsection