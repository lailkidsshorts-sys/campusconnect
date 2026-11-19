<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Database Viewer - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; font-family: system-ui, -apple-system, sans-serif; }
        .table-wrap { background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; }
        .badge-table { font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; }
    </style>
</head>
<body class="py-4">
    <div class="container-fluid px-3 px-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="fas fa-database text-primary me-2"></i>Live Database Viewer</h3>
                <p class="text-muted small mb-0">Inspecting Live Cloud MySQL Database (<strong>ethiodeploy</strong>)</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url('/seed-dummy-data') }}" target="_blank" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-sync-alt me-1"></i> Seed/Reload Dummy Data
                </a>
                <a href="{{ url('/login') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt me-1"></i> Go to App Login
                </a>
            </div>
        </div>

        <!-- Table Selector Tabs -->
        <div class="d-flex gap-2 mb-3 flex-wrap">
            @foreach ($tables as $t)
                <a href="{{ url('/live-data?table=' . $t) }}" class="btn btn-sm {{ $currentTable === $t ? 'btn-primary' : 'btn-white bg-white text-dark border' }}">
                    <i class="fas fa-table me-1 text-muted"></i>
                    <strong>{{ ucfirst(str_replace('_', ' ', $t)) }}</strong>
                    <span class="badge {{ $currentTable === $t ? 'bg-light text-primary' : 'bg-secondary' }} ms-1">
                        {{ $counts[$t] ?? 0 }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Data Table Card -->
        <div class="table-wrap p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark">
                    Showing Table: <span class="text-primary font-monospace">{{ $currentTable }}</span> 
                    <small class="text-muted fw-normal">({{ count($rows) }} records)</small>
                </h5>
            </div>

            @if (count($rows) === 0)
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                    <h6>This table is currently empty.</h6>
                    <a href="{{ url('/seed-dummy-data') }}" class="btn btn-success btn-sm mt-2">
                        <i class="fas fa-plus-circle me-1"></i> Click to Seed Demo Data
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered align-middle small mb-0">
                        <thead class="table-dark">
                            <tr>
                                @foreach (array_keys((array) $rows[0]) as $column)
                                    <th>{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr>
                                    @foreach ((array) $row as $colName => $val)
                                        <td>
                                            @if (is_null($val))
                                                <span class="text-muted fst-italic">NULL</span>
                                            @elseif ($colName === 'password')
                                                <span class="text-muted font-monospace">[Hashed]</span>
                                            @elseif (is_string($val) && strlen($val) > 70)
                                                <span title="{{ $val }}">{{ Str::limit($val, 70) }}</span>
                                            @else
                                                {{ $val }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
