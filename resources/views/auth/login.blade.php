@extends('layouts.auth')

@section('title', 'Sign In - BIIT CampusConnect')

@section('content')
<div class="container auth-wrap">
    <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
        @csrf
        <img src="{{ asset('logo.png') }}" alt="BIIT Logo" class="center" />
        <h4 class="auth-title">
            Welcome to <br />
            Barani Institute <br />
            CampusConnect
        </h4>
        <p class="auth-subtitle">Smart Campus Complaint & Feedback Management System</p>

        @if (session('success'))
            <div class="alert alert-success py-2 px-3 small border-0 mb-3" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info py-2 px-3 small border-0 mb-3" role="alert">
                <i class="fas fa-info-circle me-1"></i> {{ session('info') }}
            </div>
        @endif

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
            <div class="field">
                <input type="email" name="email" id="email" placeholder="Institutional Email" value="{{ old('email') }}" required autofocus autocomplete="email" />
            </div>
            <div class="field">
                <input type="password" name="password" id="password" placeholder="Password" required autocomplete="current-password" />
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
            <div class="form-check m-0">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small text-muted" for="remember">
                    Remember me
                </label>
            </div>
            <p class="psw m-0">
                <a href="{{ route('password.forgot') }}" class="small text-muted text-decoration-none">Forgot Password?</a>
            </p>
        </div>

        <button type="submit" id="submitBtn">
            <i class="fas fa-sign-in-alt me-2"></i>SIGN IN
        </button>

        <p class="mt-3 mb-0 small text-center text-muted">
            Don't have a student account? <a href="{{ route('signup') }}" class="register">Sign Up here</a>
        </p>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $('#loginForm').on('submit', function(e) {
        const email = $('#email').val().trim();
        const password = $('#password').val();

        if (!email || !password) {
            e.preventDefault();
            Swal.fire({
                title: 'Validation Error',
                text: 'Please enter both your institutional email and password.',
                icon: 'error',
                confirmButtonColor: '#0c7347'
            });
            return false;
        }

        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Signing in...');
        return true;
    });

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            $('#submitBtn').prop('disabled', false).html('<i class="fas fa-sign-in-alt me-2"></i>SIGN IN');
        }
    });
</script>
@endpush
