<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - CampusConnect</title>
    <link rel="icon" type="image/png" href="{{ asset('images/biit-logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/campusconnect.css') }}" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="card p-4 p-md-5 border-0 shadow-sm">
                    <div class="mb-4">
                        <img src="{{ asset('images/biit-logo.png') }}" alt="BIIT Logo" style="width: 72px;">
                    </div>
                    <div class="text-danger mb-3">
                        <i class="bi bi-shield-lock-fill" style="font-size: 3.5rem;"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-2">403 - Access Denied</h2>
                    <p class="text-muted mb-4">
                        {{ $exception->getMessage() ?: 'You do not have permission to access or view this department complaint. Role-based server-side security has intercepted this request.' }}
                    </p>
                    <div>
                        @if (Auth::check())
                            <a href="{{ Auth::user()->isStudent() ? route('student.dashboard') : route('admin.dashboard') }}" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-arrow-left me-1"></i> Return to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Go to Login
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
