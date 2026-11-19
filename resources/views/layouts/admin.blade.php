<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>@yield('title', 'Administration') - BIIT CampusConnect</title>
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

    <!-- Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
</head>
<body>
    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="cc-sidebar" id="adminSidebar">
        <div class="d-flex align-items-center justify-content-between px-3 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="cc-sidebar-brand p-0 border-0">
                <img src="{{ asset('logo.png') }}" alt="BIIT Logo" class="cc-brand-logo bg-white rounded-circle p-1">
                <div>
                    <div class="fw-bold fs-6 lh-1">CampusConnect</div>
                    <small class="text-white-50" style="font-size: 0.72rem;">BIIT Administration</small>
                </div>
            </a>
            <button class="btn btn-link text-white-50 d-lg-none p-1" onclick="toggleSidebar()">
                <i class="fas fa-times fs-5"></i>
            </button>
        </div>

        <div class="px-3 my-3">
            <div class="bg-black bg-opacity-25 p-2 rounded-3 text-center">
                <span class="badge {{ Auth::user()->isSuperAdmin() ? 'bg-danger' : 'bg-warning text-dark' }} text-uppercase mb-1" style="font-size: 0.68rem;">
                    {{ Auth::user()->isSuperAdmin() ? 'Central Super Admin' : 'Department Admin' }}
                </span>
                <div class="small fw-semibold text-white text-truncate">
                    {{ Auth::user()->department ? Auth::user()->department->name : 'All Campus Departments' }}
                </div>
            </div>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-chart-pie me-2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}" href="{{ route('admin.complaints.index') }}">
                <i class="fas fa-inbox me-2"></i> Complaints Management
            </a>
            <a class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}">
                <i class="fas fa-chart-line me-2"></i> Analytics & Reports
            </a>
            <a class="nav-link" href="{{ route('admin.complaints.export') }}">
                <i class="fas fa-file-csv me-2"></i> Export CSV
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="cc-main-content">
        <!-- Topbar -->
        <header class="cc-topbar d-flex align-items-center justify-content-between sticky-top">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark fs-6 fs-md-5">@yield('page_title', 'Admin Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-semibold small text-dark lh-sm">{{ Auth::user()->name }}</div>
                    <div class="text-muted" style="font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1">
                        <i class="fas fa-sign-out-alt me-1"></i> <span class="d-none d-sm-inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Body -->
        <main class="p-3 p-md-4 flex-grow-1">
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

        <footer class="bg-white border-top py-3 px-3 px-md-4 text-muted small text-center text-sm-start d-sm-flex justify-content-between">
            <span>&copy; {{ date('Y') }} <strong>Barani Institute of Information Technology</strong></span>
            <span class="d-none d-sm-inline">CampusConnect Administration</span>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById('adminSidebar');
            var backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
