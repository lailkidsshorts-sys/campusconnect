@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Institutional Overview & Dashboard')

@section('content')
<!-- Metric Cards -->
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">Total</small>
                <h3 class="fw-bold text-dark mt-1 mb-0 fs-4 fs-md-3">{{ $stats['total'] }}</h3>
            </div>
            <div class="stat-icon bg-light text-primary">
                <i class="fas fa-inbox"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">Pending</small>
                <h3 class="fw-bold text-warning mt-1 mb-0 fs-4 fs-md-3">{{ $stats['pending'] }}</h3>
            </div>
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">In Progress</small>
                <h3 class="fw-bold text-primary mt-1 mb-0 fs-4 fs-md-3">{{ $stats['in_progress'] }}</h3>
            </div>
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">Resolved</small>
                <h3 class="fw-bold text-success mt-1 mb-0 fs-4 fs-md-3">{{ $stats['resolved'] }}</h3>
            </div>
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">Rejected</small>
                <h3 class="fw-bold text-danger mt-1 mb-0 fs-4 fs-md-3">{{ $stats['rejected'] }}</h3>
            </div>
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">Satisfaction</small>
                <h3 class="fw-bold text-dark mt-1 mb-0 fs-4 fs-md-3">
                    {{ $stats['avg_rating'] }} <i class="fas fa-star text-warning" style="font-size: 0.9rem;"></i>
                </h3>
            </div>
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-smile"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 g-md-4 mb-4">
    @if ($isSuperAdmin && $departmentChart)
        <!-- Complaints per Department (Bar Chart) -->
        <div class="col-12 col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small fs-md-6"><i class="fas fa-building text-primary me-1"></i> Complaints per Department</span>
                    <span class="badge bg-light text-muted border">Super Admin</span>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div style="position: relative; height: 260px;">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Status Distribution (Doughnut Chart) -->
    <div class="col-12 col-lg-{{ $isSuperAdmin ? '6' : '4' }}">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark small fs-md-6"><i class="fas fa-chart-pie text-primary me-1"></i> Status Breakdown</span>
            </div>
            <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-center">
                <div style="position: relative; height: 240px; width: 100%;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 30-Day Trends (Line Chart) -->
    <div class="col-12 col-lg-{{ $isSuperAdmin ? '8' : '8' }}">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark small fs-md-6"><i class="fas fa-chart-line text-primary me-1"></i> Influx Trend (Last 30 Days)</span>
            </div>
            <div class="card-body p-2 p-md-3">
                <div style="position: relative; height: 250px;">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Issue Types (Horizontal Bar Chart) -->
    <div class="col-12 col-lg-{{ $isSuperAdmin ? '4' : '12' }}">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark small fs-md-6"><i class="fas fa-exclamation-triangle text-warning me-1"></i> Top 5 Recurring Issues</span>
            </div>
            <div class="card-body p-2 p-md-3">
                <div style="position: relative; height: 250px;">
                    <canvas id="topIssuesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Complaints Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-bold text-dark"><i class="fas fa-inbox me-1 text-primary"></i> Recent Incoming Complaints</span>
        <a href="{{ route('admin.complaints.index') }}" class="btn btn-sm btn-outline-primary">View All Complaints</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Ref No</th>
                        <th>Student</th>
                        @if ($isSuperAdmin)
                            <th class="d-none d-md-table-cell">Department</th>
                        @endif
                        <th class="d-none d-lg-table-cell">Issue Type</th>
                        <th>Subject</th>
                        <th class="d-none d-sm-table-cell">Priority</th>
                        <th>Status</th>
                        <th class="d-none d-xl-table-cell">Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentComplaints as $c)
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
                            <td class="d-none d-lg-table-cell"><span class="small text-muted">{{ $c->issueType->name }}</span></td>
                            <td class="fw-medium text-dark">
                                <div class="text-truncate" style="max-width: 190px;">{{ $c->subject }}</div>
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
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isSuperAdmin ? '9' : '8' }}" class="text-center py-4 text-muted">
                                No complaints registered.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if ($isSuperAdmin && $departmentChart)
    new Chart(document.getElementById('deptChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($departmentChart['labels']) !!},
            datasets: [{
                label: 'Total Complaints',
                data: {!! json_encode($departmentChart['data']) !!},
                backgroundColor: '#0c7347',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
    @endif

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusChart['labels']) !!},
            datasets: [{
                data: {!! json_encode($statusChart['data']) !!},
                backgroundColor: {!! json_encode($statusChart['colors']) !!},
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } }
            }
        }
    });

    new Chart(document.getElementById('trendsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($trendsChart['labels']) !!},
            datasets: [{
                label: 'Daily Complaints',
                data: {!! json_encode($trendsChart['data']) !!},
                borderColor: '#0c7347',
                backgroundColor: 'rgba(12, 115, 71, 0.08)',
                fill: true,
                tension: 0.35,
                borderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    new Chart(document.getElementById('topIssuesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topIssuesChart['labels']) !!},
            datasets: [{
                label: 'Occurrences',
                data: {!! json_encode($topIssuesChart['data']) !!},
                backgroundColor: '#2563EB',
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
});
</script>
@endpush
