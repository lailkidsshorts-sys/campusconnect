@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<!-- Student Profile & Hero Greeting -->
<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 60%, #2563EB 100%); color: #FFFFFF; border-radius: 16px;">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-white bg-opacity-20 text-white px-3 py-1 font-monospace" style="font-size: 0.8rem;">
                        <i class="fas fa-id-card me-1"></i> {{ Auth::user()->roll_no ?? 'Campus Student' }}
                    </span>
                    <span class="badge bg-success bg-opacity-75 text-white px-2 py-1" style="font-size: 0.75rem;">
                        <i class="fas fa-check-circle me-1"></i> Active Enrolled
                    </span>
                </div>
                <h2 class="fw-bold text-white mb-2 fs-3 fs-md-2">Welcome, {{ Auth::user()->name }}!</h2>
                <p class="text-white-50 mb-3 small" style="max-width: 580px;">
                    CampusConnect empowers you to voice concerns and report campus facility problems directly to responsible university administrations.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('student.complaints.create') }}" class="btn btn-light text-primary fw-bold shadow-sm px-3 py-2">
                        <i class="fas fa-magic text-warning me-1"></i> Submit with AI Assistant
                    </a>
                    <a href="{{ route('student.complaints.index') }}" class="btn btn-outline-light px-3 py-2">
                        <i class="fas fa-history me-1"></i> Track My Complaints
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-4 text-center d-none d-md-block">
                <div class="p-3 bg-white bg-opacity-10 rounded-4 d-inline-block backdrop-blur">
                    <i class="fas fa-university text-white-50" style="font-size: 4.5rem;"></i>
                    <div class="text-white small fw-bold mt-2">CampusConnect Portal</div>
                    <small class="text-white-50" style="font-size: 0.72rem;">Centralized Feedback System</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics Row -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-widget p-3 h-100 bg-white">
            <div>
                <span class="text-muted small fw-semibold d-block">Total Submitted</span>
                <h3 class="fw-bold text-dark mt-1 mb-0 fs-3">{{ $stats['total'] }}</h3>
                <small class="text-muted" style="font-size: 0.72rem;">Lifetime Tickets</small>
            </div>
            <div class="stat-icon bg-light text-primary fs-4">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-widget p-3 h-100 bg-white">
            <div>
                <span class="text-muted small fw-semibold d-block">Under Review</span>
                <h3 class="fw-bold text-warning mt-1 mb-0 fs-3">{{ $stats['pending'] }}</h3>
                <small class="text-warning fw-semibold" style="font-size: 0.72rem;">Queued for Dept</small>
            </div>
            <div class="stat-icon bg-warning bg-opacity-10 text-warning fs-4">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-widget p-3 h-100 bg-white">
            <div>
                <span class="text-muted small fw-semibold d-block">In Progress</span>
                <h3 class="fw-bold text-primary mt-1 mb-0 fs-3">{{ $stats['in_progress'] }}</h3>
                <small class="text-primary fw-semibold" style="font-size: 0.72rem;">Personnel Assigned</small>
            </div>
            <div class="stat-icon bg-primary bg-opacity-10 text-primary fs-4">
                <i class="fas fa-tools"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-widget p-3 h-100 bg-white">
            <div>
                <span class="text-muted small fw-semibold d-block">Resolved</span>
                <h3 class="fw-bold text-success mt-1 mb-0 fs-3">{{ $stats['resolved'] }}</h3>
                <small class="text-success fw-semibold" style="font-size: 0.72rem;">Action Completed</small>
            </div>
            <div class="stat-icon bg-success bg-opacity-10 text-success fs-4">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Campus Departments Quick Directory -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-sitemap text-primary me-2"></i>Campus Complaint Channels</h6>
            <small class="text-muted">Tickets route automatically based on chosen facility</small>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 text-center hover-lift" style="transition: transform 0.2s;">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger mx-auto mb-2">
                <i class="fas fa-utensils"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1 small">Cafeteria</h6>
            <p class="text-muted mb-2" style="font-size: 0.72rem;">Food hygiene, menu pricing, cafeteria seating</p>
            <a href="{{ route('student.complaints.create') }}" class="btn btn-xs btn-outline-danger w-100 py-1" style="font-size: 0.72rem;">Report Issue</a>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 text-center hover-lift" style="transition: transform 0.2s;">
            <div class="stat-icon bg-info bg-opacity-10 text-info mx-auto mb-2">
                <i class="fas fa-book-reader"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1 small">Library</h6>
            <p class="text-muted mb-2" style="font-size: 0.72rem;">Book availability, study hours, silent zones</p>
            <a href="{{ route('student.complaints.create') }}" class="btn btn-xs btn-outline-info w-100 py-1" style="font-size: 0.72rem;">Report Issue</a>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 text-center hover-lift" style="transition: transform 0.2s;">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-2">
                <i class="fas fa-laptop-code"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1 small">Labs & IT</h6>
            <p class="text-muted mb-2" style="font-size: 0.72rem;">Network speed, PC hardware, multimedia projectors</p>
            <a href="{{ route('student.complaints.create') }}" class="btn btn-xs btn-outline-primary w-100 py-1" style="font-size: 0.72rem;">Report Issue</a>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 text-center hover-lift" style="transition: transform 0.2s;">
            <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-2">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1 small">Academic Affairs</h6>
            <p class="text-muted mb-2" style="font-size: 0.72rem;">Course materials, class management, lecture delays</p>
            <a href="{{ route('student.complaints.create') }}" class="btn btn-xs btn-outline-success w-100 py-1" style="font-size: 0.72rem;">Report Issue</a>
        </div>
    </div>
</div>

<!-- Recent Complaints Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <div>
            <span class="fw-bold text-dark fs-6"><i class="fas fa-history me-2 text-primary"></i>My Recent Complaints Activity</span>
        </div>
        <a href="{{ route('student.complaints.index') }}" class="btn btn-sm btn-outline-primary py-1 px-3">
            View All Complaints <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        @if ($recentComplaints->isEmpty())
            <div class="text-center py-5 px-3">
                <div class="stat-icon bg-light text-primary mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                    <i class="fas fa-inbox"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No complaints submitted yet</h5>
                <p class="text-muted small mb-3" style="max-width: 420px; margin: 0 auto;">
                    Have an issue with campus facilities, internet, books, or cafeteria? Click below to launch the Smart Complaint Assistant.
                </p>
                <a href="{{ route('student.complaints.create') }}" class="btn btn-primary px-4 py-2 shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> Submit First Complaint
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Reference No</th>
                            <th class="d-none d-md-table-cell">Department</th>
                            <th>Subject</th>
                            <th class="d-none d-sm-table-cell">Priority</th>
                            <th>Status</th>
                            <th class="d-none d-lg-table-cell">Date</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentComplaints as $complaint)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-light text-dark border font-monospace fw-bold">{{ $complaint->reference_no }}</span>
                                </td>
                                <td class="d-none d-md-table-cell small">
                                    <span class="fw-medium text-dark"><i class="fas fa-building text-primary me-1"></i> {{ $complaint->department->name }}</span>
                                </td>
                                <td class="fw-medium text-dark">
                                    <div class="text-truncate" style="max-width: 250px;">
                                        {{ $complaint->subject }}
                                    </div>
                                    <small class="text-muted d-md-none">{{ $complaint->department->name }}</small>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge badge-priority-{{ $complaint->priority->value ?? $complaint->priority }}">
                                        {{ ucfirst($complaint->priority->value ?? $complaint->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-status-{{ $complaint->status->value ?? $complaint->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $complaint->status->value ?? $complaint->status)) }}
                                    </span>
                                </td>
                                <td class="d-none d-lg-table-cell text-muted small">{{ $complaint->created_at->format('M d, Y') }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('student.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-primary py-1 px-3">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- 3-Step Lifecycle Guide -->
<div class="card border-0 shadow-sm p-4 bg-light">
    <div class="row g-3 text-center">
        <div class="col-12 col-md-4">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="badge bg-primary rounded-circle" style="width: 28px; height: 28px; line-height: 18px;">1</span>
                <strong class="text-dark small">Submit Ticket</strong>
            </div>
            <p class="text-muted small mb-0">Select facility, let Gemini AI polish your description, and submit with supporting photos.</p>
        </div>
        <div class="col-12 col-md-4">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="badge bg-primary rounded-circle" style="width: 28px; height: 28px; line-height: 18px;">2</span>
                <strong class="text-dark small">Automatic Dept Routing</strong>
            </div>
            <p class="text-muted small mb-0">The system instantly routes tickets to the authorized department administrator for inspection.</p>
        </div>
        <div class="col-12 col-md-4">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="badge bg-primary rounded-circle" style="width: 28px; height: 28px; line-height: 18px;">3</span>
                <strong class="text-dark small">Resolution & Rating</strong>
            </div>
            <p class="text-muted small mb-0">Track official remarks in real-time, get notified on resolution, and submit star satisfaction feedback.</p>
        </div>
    </div>
</div>
@endsection
