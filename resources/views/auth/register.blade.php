@extends('layouts.auth')

@section('title', 'Create Student Account - CampusConnect')

@section('content')
<div class="container signup-wrap">
    <form method="POST" action="{{ route('signup.submit') }}" id="signupForm" novalidate>
        @csrf
        <img src="{{ asset('images/campus-logo.svg') }}" alt="CampusConnect Logo" class="center" />
        <h4 class="signup-title">
            Create Student Account <br />
            CampusConnect Portal
        </h4>
        <p class="signup-subtitle">Register to submit complaints and track departmental actions</p>

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small border-0 mb-3" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="input_field">
            <div class="row g-2">
                <div class="col-6">
                    <div class="field">
                        <input type="text" name="fname" placeholder="First Name" value="{{ old('fname') }}" maxlength="50" autocomplete="given-name" class="@error('fname') is-invalid @enderror" required />
                        <small class="field-error" id="err-fname" role="alert">@error('fname'){{ $message }}@enderror</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field">
                        <input type="text" name="lname" placeholder="Last Name" value="{{ old('lname') }}" maxlength="50" autocomplete="family-name" class="@error('lname') is-invalid @enderror" required />
                        <small class="field-error" id="err-lname" role="alert">@error('lname'){{ $message }}@enderror</small>
                    </div>
                </div>
            </div>

            <div class="field">
                <input type="text" name="roll_no" placeholder="Roll Number (e.g. STU-2024-042)" value="{{ old('roll_no') }}" maxlength="25" autocomplete="off" class="text-uppercase @error('roll_no') is-invalid @enderror" required />
                <small class="field-error" id="err-roll_no" role="alert">@error('roll_no'){{ $message }}@enderror</small>
            </div>

            <div class="field">
                <input type="email" name="email" placeholder="Institutional Email (e.g. student@campus.edu)" value="{{ old('email') }}" maxlength="100" autocomplete="email" class="@error('email') is-invalid @enderror" required />
                <small class="field-error" id="err-email" role="alert">@error('email'){{ $message }}@enderror</small>
            </div>

            <div class="field">
                <input type="password" name="password" placeholder="Password (Min. 6 characters)" maxlength="64" autocomplete="new-password" class="@error('password') is-invalid @enderror" required />
                <small class="field-error" id="err-password" role="alert">@error('password'){{ $message }}@enderror</small>
            </div>

            <div class="field">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" maxlength="64" autocomplete="new-password" required />
                <small class="field-error" id="err-password_confirmation" role="alert"></small>
            </div>
        </div>

        <button type="submit" id="submitBtn" class="mt-4">
            <i class="fas fa-user-plus me-2"></i>CREATE ACCOUNT
        </button>

        <p class="mt-3 mb-0 small text-center text-muted">
            Already have an account? <a href="{{ route('login') }}" class="register">Login here</a>
        </p>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        'use strict';

        var form = document.getElementById('signupForm');
        var submitBtn = document.getElementById('submitBtn');
        if (!form) { return; }

        var originalBtnHtml = submitBtn.innerHTML;

        var NAME_PATTERN = /^[A-Za-z][A-Za-z .'-]*$/;
        var EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

        var validators = {
            fname: function (value) {
                value = value.trim();
                if (!value) { return 'First name is required.'; }
                if (value.length < 2) { return 'First name must be at least 2 characters.'; }
                if (!NAME_PATTERN.test(value)) { return 'Only letters, spaces and hyphens allowed.'; }
                return '';
            },
            lname: function (value) {
                value = value.trim();
                if (!value) { return 'Last name is required.'; }
                if (!NAME_PATTERN.test(value)) { return 'Only letters, spaces and hyphens allowed.'; }
                return '';
            },
            roll_no: function (value) {
                value = value.trim();
                if (!value) { return 'Student Roll Number is required.'; }
                if (value.length < 3) { return 'Please enter a valid roll number.'; }
                return '';
            },
            email: function (value) {
                value = value.trim();
                if (!value) { return 'Email address is required.'; }
                if (!EMAIL_PATTERN.test(value)) { return 'Enter a valid institutional email address.'; }
                return '';
            },
            password: function (value) {
                if (!value) { return 'Password is required.'; }
                if (value.length < 6) { return 'Password must be at least 6 characters.'; }
                return '';
            },
            password_confirmation: function (value) {
                if (!value) { return 'Please confirm your password.'; }
                if (value !== form.elements['password'].value) { return 'Passwords do not match.'; }
                return '';
            }
        };

        var names = Object.keys(validators);
        var touched = {};

        function showError(input, message) {
            var errorEl = document.getElementById('err-' + input.name);
            if (errorEl) { errorEl.textContent = message; }
            input.classList.toggle('is-invalid', message !== '');
            input.setAttribute('aria-invalid', message !== '' ? 'true' : 'false');
        }

        function validateField(name) {
            var input = form.elements[name];
            var message = validators[name](input.value);
            showError(input, message);
            return message === '';
        }

        names.forEach(function (name) {
            var input = form.elements[name];

            input.addEventListener('input', function () {
                if (name === 'roll_no') {
                    input.value = input.value.toUpperCase();
                }
                if (touched[name] || input.classList.contains('is-invalid')) {
                    validateField(name);
                }
                if (name === 'password' && touched['password_confirmation']) {
                    validateField('password_confirmation');
                }
            });

            input.addEventListener('blur', function () {
                if (name === 'email') { input.value = input.value.trim().toLowerCase(); }
                if (name === 'fname' || name === 'lname') { input.value = input.value.trim(); }
                if (name === 'roll_no') { input.value = input.value.trim().toUpperCase(); }
                touched[name] = true;
                validateField(name);
            });
        });

        form.addEventListener('submit', function (event) {
            var firstInvalid = null;

            names.forEach(function (name) {
                touched[name] = true;
                if (!validateField(name) && !firstInvalid) { firstInvalid = form.elements[name]; }
            });

            if (firstInvalid) {
                event.preventDefault();
                firstInvalid.focus();
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> CREATING ACCOUNT...';
        });

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        });
    })();
</script>
@endpush
