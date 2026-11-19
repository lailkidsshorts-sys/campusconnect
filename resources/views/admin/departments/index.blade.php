@extends('layouts.admin')

@section('title', 'Manage Departments')
@section('page_title', 'University Departments Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">Campus Departments</h4>
        <p class="text-muted small mb-0">Define, edit, and organize institutional departments handling student complaints.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeptModal">
        <i class="fas fa-plus-circle me-1"></i> Add New Department
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger border-0 py-2 px-3 small mb-3">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Department Name</th>
                        <th>Admin Title / Head</th>
                        <th>Categories</th>
                        <th>Complaints Handled</th>
                        <th>Assigned Admins</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $dept)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $dept->name }}</div>
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.72rem;">slug: {{ $dept->slug }}</span>
                            </td>
                            <td>
                                <span class="fw-medium text-dark small"><i class="fas fa-user-shield text-primary me-1"></i> {{ $dept->admin_title }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $dept->categories_count }} Categories</span>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $dept->complaints_count }} Tickets</span>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info">{{ $dept->admins_count }} Accounts</span>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#editDeptModal{{ $dept->id }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 ms-1" {{ $dept->complaints_count > 0 || $dept->categories_count > 0 ? 'disabled title="Cannot delete department with active data"' : '' }}>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Department Modal -->
                        <div class="modal fade" id="editDeptModal{{ $dept->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Department</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.departments.update', $dept) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Department Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $dept->name) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Administrator Title <span class="text-danger">*</span></label>
                                                <input type="text" name="admin_title" class="form-control" value="{{ old('admin_title', $dept->admin_title) }}" required>
                                                <div class="form-text small">e.g. Cafeteria Administrator, Lab Coordinator</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No departments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Department Modal -->
<div class="modal fade" id="createDeptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.departments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Department Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Transportation & Parking Services" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Administrator Title <span class="text-danger">*</span></label>
                        <input type="text" name="admin_title" class="form-control" placeholder="e.g. Transport Manager" value="{{ old('admin_title') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
