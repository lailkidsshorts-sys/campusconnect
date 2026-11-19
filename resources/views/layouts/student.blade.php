<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>@yield('title', 'Student Portal') - BIIT CampusConnect</title>
    <link rel="icon" type="image/png" href="{{ asset('images/biit-logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CDN & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- CampusConnect Custom BIIT Theme -->
    <link href="{{ asset('css/campusconnect.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Student Navbar -->
    <nav class="navbar navbar-expand-lg cc-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark py-1" href="{{ route('student.dashboard') }}">
                <img src="{{ asset('logo.png') }}" alt="BIIT Logo" class="cc-brand-logo">
                <span class="fs-6 fs-sm-5">Campus<span class="text-primary">Connect</span></span>
            </a>

            <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav" aria-controls="studentNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="studentNav">
                <ul class="navbar-nav me-auto ms-lg-3 my-2 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link py-2 {{ request()->routeIs('student.dashboard') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('student.dashboard') }}">
                            <i class="fas fa-th-large me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2 {{ request()->routeIs('student.complaints.index') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('student.complaints.index') }}">
                            <i class="fas fa-list-check me-1"></i> My Complaints
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2 {{ request()->routeIs('student.complaints.create') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('student.complaints.create') }}">
                            <i class="fas fa-plus-circle me-1"></i> New Complaint
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center justify-content-between justify-content-lg-end gap-3 pt-2 pt-lg-0 border-top border-lg-0">
                    <div class="text-start text-lg-end">
                        <div class="fw-semibold small text-dark lh-sm">{{ Auth::user()->name }}</div>
                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.72rem;">{{ Auth::user()->roll_no ?? 'Student' }}</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container py-3 py-md-4 flex-grow-1">
        <!-- Flash Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
        <div class="container">
            <span>&copy; {{ date('Y') }} <strong>Barani Institute of Information Technology</strong> &bull; CampusConnect System</span>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
