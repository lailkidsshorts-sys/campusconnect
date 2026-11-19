<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') - CampusConnect</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/campus-logo.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CDN & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- CampusConnect Custom Theme -->
    <link href="{{ asset('css/campusconnect.css') }}?v={{ time() }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Student Navbar -->
    <nav class="navbar navbar-expand-lg cc-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark py-1" href="{{ route('student.dashboard') }}">
                <img src="{{ asset('images/campus-logo.svg') }}" alt="CampusConnect Logo" class="cc-brand-logo">
                <span class="fs-6 fs-sm-5 text-dark">Campus<span class="text-primary">Connect</span></span>
            </a>

            <!-- Right-side controls (Bell + Toggle) on mobile -->
            <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-2">
                <!-- Mobile Notification Bell -->
                <div class="dropdown">
                    <button class="notification-bell position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="mobileNotifBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge d-none notif-badge">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0 shadow-lg border-0" aria-labelledby="mobileNotifBtn">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light rounded-top">
                            <span class="fw-bold small text-dark">Notifications</span>
                            <button class="btn btn-link btn-sm text-primary p-0 mark-read-btn" style="font-size: 0.75rem; text-decoration: none;">Mark all read</button>
                        </div>
                        <div class="notif-list" style="max-height: 280px; overflow-y: auto;">
                            <div class="p-3 text-center text-muted small">Loading notifications...</div>
                        </div>
                    </div>
                </div>

                <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

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
                    <!-- Desktop Notification Bell -->
                    <div class="dropdown d-none d-lg-block">
                        <button class="notification-bell position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="desktopNotifBtn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge d-none notif-badge">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0 shadow-lg border-0" aria-labelledby="desktopNotifBtn">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light rounded-top">
                                <span class="fw-bold small text-dark">Notifications</span>
                                <button class="btn btn-link btn-sm text-primary p-0 mark-read-btn" style="font-size: 0.75rem; text-decoration: none;">Mark all read</button>
                            </div>
                            <div class="notif-list" style="max-height: 300px; overflow-y: auto;">
                                <div class="p-3 text-center text-muted small">Loading notifications...</div>
                            </div>
                        </div>
                    </div>

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
            <span>&copy; {{ date('Y') }} <strong>CampusConnect</strong> &bull; Smart University Complaint & Feedback Management System</span>
        </div>
    </footer>

    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Real-Time Notification Poller -->
    <script>
    (function() {
        function fetchNotifications() {
            $.get("{{ route('notifications.unread') }}", function(data) {
                if (data.count > 0) {
                    $('.notif-badge').text(data.count).removeClass('d-none');
                } else {
                    $('.notif-badge').addClass('d-none');
                }

                var html = '';
                if (!data.notifications || data.notifications.length === 0) {
                    html = '<div class="p-3 text-center text-muted small">No recent notifications</div>';
                } else {
                    data.notifications.forEach(function(item) {
                        html += '<a href="' + item.action_url + '" class="notification-item ' + (item.is_read ? '' : 'unread') + '">';
                        html += '<div class="d-flex align-items-start gap-2">';
                        html += '<i class="' + item.icon + ' mt-1"></i>';
                        html += '<div class="flex-grow-1">';
                        html += '<div class="fw-semibold text-dark" style="font-size:0.83rem;">' + item.title + '</div>';
                        html += '<div class="text-muted small" style="font-size:0.75rem;">' + item.message + '</div>';
                        html += '<div class="text-muted" style="font-size:0.68rem; margin-top:2px;">' + item.created_at + '</div>';
                        html += '</div></div></a>';
                    });
                }
                $('.notif-list').html(html);
            });
        }

        $('.mark-read-btn').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('notifications.mark_read') }}",
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function() {
                    $('.notif-badge').addClass('d-none');
                    $('.notification-item').removeClass('unread');
                }
            });
        });

        fetchNotifications();
        // Live polling every 12 seconds
        setInterval(fetchNotifications, 12000);
    })();
    </script>

    @stack('scripts')
</body>
</html>
