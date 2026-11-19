@extends('layouts.admin')

@section('title', 'Analytics & Reports')
@section('page_title', 'Institutional Analytics & Performance Reporting')

@section('content')
<!-- Metric Cards -->
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-3">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">Total Complaints</small>
                <h3 class="fw-bold text-dark mt-1 mb-0 fs-4 fs-md-3">{{ $stats['total'] }}</h3>
            </div>
            <div class="stat-icon bg-light text-primary"><i class="fas fa-inbox"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">Active Pending/Progress</small>
                <h3 class="fw-bold text-warning mt-1 mb-0 fs-4 fs-md-3">{{ $stats['pending'] + $stats['in_progress'] }}</h3>
            </div>
            <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-hourglass-half"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">Resolved Successfully</small>
                <h3 class="fw-bold text-success mt-1 mb-0 fs-4 fs-md-3">{{ $stats['resolved'] }}</h3>
            </div>
            <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-widget p-2 p-md-3">
            <div>
                <small class="text-muted fw-semibold d-block">Student Satisfaction</small>
                <h3 class="fw-bold text-dark mt-1 mb-0 fs-4 fs-md-3">{{ $stats['avg_rating'] }} / 5.0</h3>
            </div>
            <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-star"></i></div>
        </div>
    </div>
</div>

<!-- Detailed Analytics Charts -->
<div class="row g-3 g-md-4">
    @if ($isSuperAdmin && $departmentChart)
        <!-- Department Bar Chart -->
        <div class="col-12 col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <span class="fw-bold text-dark small fs-md-6"><i class="fas fa-building text-primary me-1"></i> Complaints Across University Departments</span>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div style="position: relative; height: 260px;">
                        <canvas id="analyticsDeptChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Status Breakdown -->
    <div class="col-12 col-lg-{{ $isSuperAdmin ? '6' : '6' }}">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark small fs-md-6"><i class="fas fa-chart-pie text-primary me-1"></i> Complaint Status Distribution</span>
            </div>
            <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-center">
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="analyticsStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 30 Days Trend -->
    <div class="col-12 col-lg-8">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark small fs-md-6"><i class="fas fa-chart-line text-primary me-1"></i> Influx Timeline (Last 30 Days)</span>
            </div>
            <div class="card-body p-2 p-md-3">
                <div style="position: relative; height: 260px;">
                    <canvas id="analyticsTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Recurring Issues -->
    <div class="col-12 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark small fs-md-6"><i class="fas fa-exclamation-triangle text-warning me-1"></i> Top 5 Recurring Issues</span>
            </div>
            <div class="card-body p-2 p-md-3">
                <div style="position: relative; height: 260px;">
                    <canvas id="analyticsTopIssuesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if ($isSuperAdmin && $departmentChart)
    new Chart(document.getElementById('analyticsDeptChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($departmentChart['labels']) !!},
            datasets: [{
                label: 'Complaints',
                data: {!! json_encode($departmentChart['data']) !!},
                backgroundColor: '#1E3A8A',
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

    new Chart(document.getElementById('analyticsStatusChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusChart['labels']) !!},
            datasets: [{
                data: {!! json_encode($statusChart['data']) !!},
                backgroundColor: {!! json_encode($statusChart['colors']) !!},
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

    new Chart(document.getElementById('analyticsTrendsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($trendsChart['labels']) !!},
            datasets: [{
                label: 'Daily Complaints',
                data: {!! json_encode($trendsChart['data']) !!},
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
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

    new Chart(document.getElementById('analyticsTopIssuesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topIssuesChart['labels']) !!},
            datasets: [{
                label: 'Reports',
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
