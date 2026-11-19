@extends('layouts.student')

@section('title', 'My Complaints')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1 fs-4 fs-md-3">My Complaints Tracking</h3>
        <p class="text-muted small mb-0">Track live progress and official remarks from department administrators.</p>
    </div>
    <a href="{{ route('student.complaints.create') }}" class="btn btn-primary w-100 w-sm-auto">
        <i class="fas fa-plus-circle me-1"></i> Submit Complaint
    </a>
</div>

<!-- Filter Bar -->
<div class="card mb-3 mb-md-4 border-0 shadow-sm">
    <div class="card-body p-2 p-md-3">
        <form action="{{ route('student.complaints.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by Ref No or Subject..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-6 col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('student.complaints.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Complaints Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($complaints->isEmpty())
            <div class="text-center py-5 px-3">
                <i class="fas fa-search text-muted" style="font-size: 2.8rem;"></i>
                <h5 class="fw-bold mt-3 text-dark">No complaints found</h5>
                <p class="text-muted small">No complaints matched your current filter criteria.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Reference No</th>
                            <th class="d-none d-md-table-cell">Department</th>
                            <th>Subject & Issue</th>
                            <th class="d-none d-sm-table-cell">Priority</th>
                            <th>Status</th>
                            <th class="d-none d-lg-table-cell">Submitted</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complaints as $complaint)
                            <tr>
                                <td>
                                    <a href="{{ route('student.complaints.show', $complaint) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                        {{ $complaint->reference_no }}
                                    </a>
                                </td>
                                <td class="d-none d-md-table-cell small">{{ $complaint->department->name }}</td>
                                <td>
                                    <div class="fw-medium text-dark text-truncate" style="max-width: 220px;">
                                        {{ $complaint->subject }}
                                    </div>
                                    <span class="text-muted small">{{ $complaint->issueType->name }}</span>
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
                                    <a href="{{ route('student.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-primary py-1 px-2 px-md-3">
                                        <i class="fas fa-eye me-md-1"></i> <span class="d-none d-md-inline">Details</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top d-flex justify-content-end">
                {{ $complaints->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
