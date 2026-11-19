@extends('layouts.admin')

@section('title', 'Manage User Accounts')
@section('page_title', 'Institutional User Accounts Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">User Accounts Management</h4>
        <p class="text-muted small mb-0">Manage student accounts, department administrators, and central administration access.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="fas fa-user-plus me-1"></i> Add New User
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

<!-- Filters Bar -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-2 p-md-3">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by Name, Email, or Roll No..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <select name="role" class="form-select">
                    <option value="">All User Roles</option>
                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Students</option>
                    <option value="dept_admin" {{ request('role') === 'dept_admin' ? 'selected' : '' }}>Department Admins</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admins</option>
                </select>
            </div>

            <div class="col-6 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                @if (request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">User Details</th>
                        <th>Role</th>
                        <th>Assigned Department / Roll No</th>
                        <th>Complaints</th>
                        <th>Registered Date</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $u->name }}</div>
                                <small class="text-muted">{{ $u->email }}</small>
                            </td>
                            <td>
                                @if ($u->role->value === 'super_admin' || $u->role === 'super_admin')
                                    <span class="badge bg-danger">Super Admin</span>
                                @elseif ($u->role->value === 'dept_admin' || $u->role === 'dept_admin')
                                    <span class="badge bg-primary">Dept Admin</span>
                                @else
                                    <span class="badge bg-success">Student</span>
                                @endif
                            </td>
                            <td>
                                @if ($u->department)
                                    <span class="fw-medium text-dark small"><i class="fas fa-building text-primary me-1"></i> {{ $u->department->name }}</span>
                                @elseif ($u->roll_no)
                                    <span class="badge bg-light text-dark border font-monospace">{{ $u->roll_no }}</span>
                                @else
                                    <span class="text-muted small">Central Administration</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $u->complaints_count }}</span>
                            </td>
                            <td class="text-muted small">{{ $u->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 ms-1" {{ $u->id === Auth::id() ? 'disabled title="Cannot delete self"' : '' }}>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit User Account</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.users.update', $u) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $u->name) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email', $u->email) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Role <span class="text-danger">*</span></label>
                                                <select name="role" class="form-select role-selector" required>
                                                    <option value="student" {{ old('role', $u->role->value ?? $u->role) === 'student' ? 'selected' : '' }}>Student</option>
                                                    <option value="dept_admin" {{ old('role', $u->role->value ?? $u->role) === 'dept_admin' ? 'selected' : '' }}>Department Administrator</option>
                                                    <option value="super_admin" {{ old('role', $u->role->value ?? $u->role) === 'super_admin' ? 'selected' : '' }}>Super Administrator</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 dept-select-wrap">
                                                <label class="form-label fw-semibold small">Department Assignment (for Dept Admins)</label>
                                                <select name="department_id" class="form-select">
                                                    <option value="">-- Select Department --</option>
                                                    @foreach ($departments as $dept)
                                                        <option value="{{ $dept->id }}" {{ old('department_id', $u->department_id) == $dept->id ? 'selected' : '' }}>
                                                            {{ $dept->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Roll Number (for Students)</label>
                                                <input type="text" name="roll_no" class="form-control" value="{{ old('roll_no', $u->roll_no) }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Reset Password (leave empty to keep unchanged)</label>
                                                <input type="password" name="password" class="form-control" placeholder="New password (optional)">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary btn-sm">Update User</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No users matched your filter criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="p-3 border-top">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create New User Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Asad Mehmood" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="user@campusconnect.edu" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Account Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" id="new_user_role" required>
                            <option value="student">Student</option>
                            <option value="dept_admin">Department Administrator</option>
                            <option value="super_admin">Super Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3" id="new_user_dept_wrap">
                        <label class="form-label fw-semibold small">Department Assignment (for Dept Admin)</label>
                        <select name="department_id" class="form-select">
                            <option value="">-- Select Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="new_user_roll_wrap">
                        <label class="form-label fw-semibold small">Roll Number (for Student)</label>
                        <input type="text" name="roll_no" class="form-control" placeholder="e.g. FA23-BCS-099">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
