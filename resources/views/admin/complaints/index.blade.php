@extends('layouts.admin')

@section('title', 'Complaints Management')
@section('page_title', 'Complaints Management & Audit')

@section('content')
<!-- Filter & Search Toolbar -->
<div class="card mb-3 mb-md-4 border-0 shadow-sm">
    <div class="card-body p-2 p-md-3">
        <form action="{{ route('admin.complaints.index') }}" method="GET" class="row g-2 align-items-center">
            <!-- Search -->
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Ref No, Subject, Student..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Department (Super Admin only) -->
            @if ($isSuperAdmin)
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Category -->
            <div class="col-6 col-md-{{ $isSuperAdmin ? '2' : '3' }}">
                <label class="form-label small fw-semibold text-muted mb-1">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Date Range From -->
            <div class="col-6 col-md-{{ $isSuperAdmin ? '3' : '2' }}">
                <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-12 d-flex justify-content-between align-items-center mt-3 pt-2 border-top flex-wrap gap-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    @if (request()->hasAny(['search', 'department_id', 'category_id', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    @endif
                </div>

                <a href="{{ route('admin.complaints.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Filtered CSV
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Complaints Data Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($complaints->isEmpty())
            <div class="text-center py-5 px-3">
                <i class="fas fa-inbox text-muted" style="font-size: 2.8rem;"></i>
                <h5 class="fw-bold mt-3 text-dark">No complaints found</h5>
                <p class="text-muted small">No complaints matched your search or filter settings.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ref No</th>
                            <th>Student</th>
                            @if ($isSuperAdmin)
                                <th class="d-none d-md-table-cell">Department</th>
                            @endif
                            <th class="d-none d-lg-table-cell">Category & Issue</th>
                            <th>Subject</th>
                            <th class="d-none d-sm-table-cell">Priority</th>
                            <th>Status</th>
                            <th class="d-none d-xl-table-cell">Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complaints as $c)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.complaints.show', $c) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                        {{ $c->reference_no }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $c->user->name ?? 'Student' }}</div>
                                    <div class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $c->user->roll_no ?? 'N/A' }}</div>
                                </td>
                                @if ($isSuperAdmin)
                                    <td class="d-none d-md-table-cell"><span class="badge bg-light text-dark border">{{ $c->department->name }}</span></td>
                                @endif
                                <td class="d-none d-lg-table-cell">
                                    <div class="small fw-semibold text-dark">{{ $c->category->name }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $c->issueType->name }}</div>
                                </td>
                                <td class="fw-medium text-dark">
                                    <div class="text-truncate" style="max-width: 180px;">{{ $c->subject }}</div>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge badge-priority-{{ $c->priority->value ?? $c->priority }}">
                                        {{ ucfirst($c->priority->value ?? $c->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-status-{{ $c->status->value ?? $c->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $c->status->value ?? $c->status)) }}
                                    </span>
                                </td>
                                <td class="d-none d-xl-table-cell text-muted small">{{ $c->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.complaints.show', $c) }}" class="btn btn-sm btn-primary py-1 px-2 px-md-3">
                                        <i class="fas fa-edit me-md-1"></i> <span class="d-none d-md-inline">Review</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted" style="font-size: 0.8rem;">
                    Showing {{ $complaints->firstItem() ?? 0 }} to {{ $complaints->lastItem() ?? 0 }} of {{ $complaints->total() }} complaints
                </small>
                <div>
                    {{ $complaints->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
