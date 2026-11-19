@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<div class="row align-items-center mb-3 mb-md-4 g-2">
    <div class="col-12 col-md-8">
        <h3 class="fw-bold text-dark mb-1 fs-4 fs-md-3">Welcome, {{ Auth::user()->name }}!</h3>
        <p class="text-muted mb-0 small">Track and manage your submitted campus complaints and service requests.</p>
    </div>
    <div class="col-12 col-md-4 text-md-end mt-2 mt-md-0">
        <a href="{{ route('student.complaints.create') }}" class="btn btn-primary w-100 w-md-auto shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> Submit New Complaint
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <span class="text-muted small fw-semibold d-block">Total</span>
                <h3 class="fw-bold text-dark mt-1 mb-0 fs-4 fs-md-3">{{ $stats['total'] }}</h3>
            </div>
            <div class="stat-icon bg-light text-primary">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <span class="text-muted small fw-semibold d-block">Pending</span>
                <h3 class="fw-bold text-warning mt-1 mb-0 fs-4 fs-md-3">{{ $stats['pending'] }}</h3>
            </div>
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <span class="text-muted small fw-semibold d-block">In Progress</span>
                <h3 class="fw-bold text-primary mt-1 mb-0 fs-4 fs-md-3">{{ $stats['in_progress'] }}</h3>
            </div>
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-tools"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <span class="text-muted small fw-semibold d-block">Resolved</span>
                <h3 class="fw-bold text-success mt-1 mb-0 fs-4 fs-md-3">{{ $stats['resolved'] }}</h3>
            </div>
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Complaints Section -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold text-dark"><i class="fas fa-history me-1 text-primary"></i> My Recent Complaints</span>
        <a href="{{ route('student.complaints.index') }}" class="btn btn-sm btn-outline-primary py-1 px-2">View All</a>
    </div>
    <div class="card-body p-0">
        @if ($recentComplaints->isEmpty())
            <div class="text-center py-5 px-3">
                <i class="fas fa-inbox text-muted" style="font-size: 2.8rem;"></i>
                <h6 class="fw-semibold mt-3 text-dark">No complaints submitted yet</h6>
                <p class="text-muted small">Need assistance with cafeteria, library, labs, or academics? Click below.</p>
                <a href="{{ route('student.complaints.create') }}" class="btn btn-primary btn-sm px-3">Submit First Complaint</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Reference No</th>
                            <th class="d-none d-md-table-cell">Department</th>
                            <th>Subject</th>
                            <th class="d-none d-sm-table-cell">Priority</th>
                            <th>Status</th>
                            <th class="d-none d-lg-table-cell">Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentComplaints as $complaint)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $complaint->reference_no }}</span>
                                </td>
                                <td class="d-none d-md-table-cell small">{{ $complaint->department->name }}</td>
                                <td class="fw-medium text-dark">
                                    <div class="text-truncate" style="max-width: 220px;">
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
                                <td class="text-end">
                                    <a href="{{ route('student.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                        <i class="fas fa-eye"></i>
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
@endsection
