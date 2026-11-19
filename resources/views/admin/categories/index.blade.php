@extends('layouts.admin')

@section('title', 'Manage Categories & Issues')
@section('page_title', 'Complaint Categories & Issue Types')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">Categories & Specific Issue Types</h4>
        <p class="text-muted small mb-0">Configure complaint categories, map them to departments, and define selectable problem types.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCatModal">
        <i class="fas fa-plus-circle me-1"></i> Add New Category
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

<div class="row g-3 g-md-4">
    @foreach ($categories as $cat)
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $cat->name }}</h6>
                        <small class="text-primary fw-medium"><i class="fas fa-building me-1"></i> {{ $cat->department->name ?? 'Unassigned' }}</small>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-xs btn-outline-primary py-1 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#editCatModal{{ $cat->id }}">
                            <i class="fas fa-edit me-1"></i> Edit
                        </button>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category and all its issue types?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2" style="font-size: 0.75rem;" {{ $cat->complaints_count > 0 ? 'disabled title="Cannot delete: Complaints exist"' : '' }}>
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.72rem;">Configured Issue Types ({{ $cat->issue_types_count }})</span>
                        <button class="btn btn-xs btn-light text-primary border py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#addIssueModal{{ $cat->id }}">
                            <i class="fas fa-plus me-1"></i> Add Issue Type
                        </button>
                    </div>

                    <ul class="list-group list-group-flush small">
                        @forelse ($cat->issueTypes as $issue)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i> {{ $issue->name }}</span>
                                <form action="{{ route('admin.issue_types.destroy', $issue) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this issue type?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 ms-2" style="font-size: 0.75rem;" title="Delete Issue Type">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-muted px-0 py-2 fst-italic">No specific issue types added yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div class="modal fade" id="editCatModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.categories.update', $cat) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $cat->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Responsible Department <span class="text-danger">*</span></label>
                                <select name="department_id" class="form-select" required>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $cat->department_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm">Update Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Issue Type Modal -->
        <div class="modal fade" id="addIssueModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Issue Type to {{ $cat->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.categories.issue_types.store', $cat) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Specific Issue Title <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Broken AC in study room" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm">Add Issue Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Complaint Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Sports & Gymnasium Facilities" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Responsible Department <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-select" required>
                            <option value="">-- Choose Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
