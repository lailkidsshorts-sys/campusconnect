@extends('layouts.admin')

@section('title', 'Manage Complaint ' . $complaint->reference_no)
@section('page_title', 'Complaint Investigation & Action')

@section('content')
<div class="row g-3 g-md-4">
    <!-- Complaint Overview & Action Column -->
    <div class="col-12 col-lg-8">
        <!-- Main Details Card -->
        <div class="card border-0 shadow-sm mb-3 mb-md-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border font-monospace fs-6">{{ $complaint->reference_no }}</span>
                    <span class="badge badge-priority-{{ $complaint->priority->value ?? $complaint->priority }}">
                        {{ ucfirst($complaint->priority->value ?? $complaint->priority) }} Priority
                    </span>
                </div>
                <span class="badge badge-status-{{ $complaint->status->value ?? $complaint->status }} px-3 py-2 fs-6">
                    {{ ucfirst(str_replace('_', ' ', $complaint->status->value ?? $complaint->status)) }}
                </span>
            </div>

            <div class="card-body p-3 p-md-4">
                <h4 class="fw-bold text-dark mb-3 fs-5 fs-md-4">{{ $complaint->subject }}</h4>

                <div class="row g-2 g-md-3 mb-4 p-3 bg-light rounded-3">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block" style="font-size: 0.72rem;">Department</small>
                        <strong class="text-dark small">{{ $complaint->department->name }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block" style="font-size: 0.72rem;">Category</small>
                        <strong class="text-dark small">{{ $complaint->category->name }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block" style="font-size: 0.72rem;">Issue Type</small>
                        <span class="text-dark small">{{ $complaint->issueType->name }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block" style="font-size: 0.72rem;">Submitted Date</small>
                        <span class="text-dark small">{{ $complaint->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">Student Complaint Description</h6>
                    <div class="p-3 bg-white border rounded text-secondary" style="white-space: pre-line; font-size: 0.95rem;">
                        {{ $complaint->description }}
                    </div>
                </div>

                @if ($complaint->attachment_path)
                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2">Attached Image Evidence</h6>
                        <div class="p-2 border rounded-3 d-inline-block bg-light">
                            <a href="{{ asset('storage/' . $complaint->attachment_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $complaint->attachment_path) }}" alt="Evidence" class="img-fluid rounded" style="max-height: 220px; object-fit: contain;">
                            </a>
                            <div class="mt-1 small text-muted text-center" style="font-size: 0.72rem;">
                                <i class="fas fa-external-link-alt me-1"></i> Tap to View Full Image
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Department Action & Status Update Form -->
        <div class="card border-0 shadow-sm mb-3 mb-md-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold text-dark mb-0 fs-6"><i class="fas fa-edit text-primary me-1"></i> Update Status & Respond to Student</h5>
            </div>
            <div class="card-body p-3 p-md-4">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 px-3 small border-0 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.complaints.update_status', $complaint) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="status" class="form-label fw-semibold small">New Complaint Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', $complaint->status->value ?? $complaint->status) === 'pending' ? 'selected' : '' }}>Pending (Under Review)</option>
                                <option value="in_progress" {{ old('status', $complaint->status->value ?? $complaint->status) === 'in_progress' ? 'selected' : '' }}>In Progress (Assigned / Action Underway)</option>
                                <option value="resolved" {{ old('status', $complaint->status->value ?? $complaint->status) === 'resolved' ? 'selected' : '' }}>Resolved (Problem Fixed)</option>
                                <option value="rejected" {{ old('status', $complaint->status->value ?? $complaint->status) === 'rejected' ? 'selected' : '' }}>Rejected (Invalid / Non-actionable)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="remark" class="form-label fw-semibold small mb-0">Administrative Remark / Official Response <span class="text-danger">*</span></label>
                                <button type="button" id="btnAiDraftReply" class="btn btn-xs btn-outline-primary" style="font-size: 0.75rem; padding: 2px 8px;">
                                    <i class="fas fa-magic me-1"></i> ✨ Generate AI Reply
                                </button>
                            </div>
                            <textarea name="remark" id="remark" rows="4" class="form-control @error('remark') is-invalid @enderror" placeholder="Write a clear institutional remark explaining the action taken or reason for status update (visible to student)..." required>{{ old('remark') }}</textarea>
                            <div class="form-text small text-muted d-flex justify-content-between flex-wrap gap-1" style="font-size: 0.75rem;">
                                <span><i class="fas fa-info-circle me-1"></i> If rejecting, a clear institutional justification is mandatory.</span>
                                <span id="aiReplyStatus" class="text-primary d-none"><i class="fas fa-spinner fa-spin me-1"></i> Drafting AI response...</span>
                            </div>
                        </div>

                        <div class="col-12 mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 w-100 w-sm-auto">
                                <i class="fas fa-check-circle me-1"></i> Save Status & Record Remark
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Student Feedback if Available -->
        @if ($complaint->feedback)
            <div class="card border-0 shadow-sm mb-3 mb-md-4">
                <div class="card-header bg-white py-3 d-flex align-items-center gap-2">
                    <i class="fas fa-star text-warning"></i>
                    <h5 class="fw-bold text-dark mb-0 fs-6">Student Resolution Feedback</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <div class="text-warning">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $complaint->feedback->rating ? '' : 'text-muted text-opacity-25' }}"></i>
                            @endfor
                        </div>
                        <span class="badge bg-success">{{ $complaint->feedback->rating }} / 5 Stars</span>
                        <small class="text-muted ms-auto" style="font-size: 0.72rem;">{{ $complaint->feedback->created_at->format('M d, Y h:i A') }}</small>
                    </div>
                    @if ($complaint->feedback->comment)
                        <div class="p-3 bg-light rounded text-dark small mt-2">
                            "{{ $complaint->feedback->comment }}"
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Student Info & Timeline Column -->
    <div class="col-12 col-lg-4">
        <!-- Student Info Card -->
        <div class="card border-0 shadow-sm mb-3 mb-md-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-id-badge text-primary me-1"></i> Complainant Information</h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon bg-light-primary text-primary fs-4">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $complaint->user->name ?? 'Anonymous' }}</h6>
                        <span class="badge bg-light text-dark border font-monospace">{{ $complaint->user->roll_no ?? 'No Roll No' }}</span>
                    </div>
                </div>
                <div class="small">
                    <div class="text-muted mb-1"><i class="fas fa-envelope me-1"></i> {{ $complaint->user->email ?? 'N/A' }}</div>
                    <div class="text-muted"><i class="fas fa-user-check me-1"></i> Registered Student</div>
                </div>
            </div>
        </div>

        <!-- Audit Trail / Timeline -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-history text-primary me-1"></i> Audit Trail & History</h6>
            </div>
            <div class="card-body p-3">
                @if ($complaint->responses->isEmpty())
                    <div class="text-center py-3 text-muted small">
                        No responses recorded yet. Use the form to record the first administrative update.
                    </div>
                @else
                    <div class="timeline">
                        @foreach ($complaint->responses as $response)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge badge-status-{{ $response->status_after }} small" style="font-size: 0.72rem;">
                                            {{ ucfirst(str_replace('_', ' ', $response->status_after)) }}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            {{ $response->created_at->format('M d, H:i') }}
                                        </small>
                                    </div>
                                    <div class="fw-semibold text-dark small">{{ $response->user->name ?? 'Admin' }}</div>
                                    <p class="text-muted small mb-0 mt-1 bg-light p-2 rounded" style="font-size: 0.82rem;">
                                        {{ $response->remark }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div>
            <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Complaints List
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAiDraft = document.getElementById('btnAiDraftReply');
    const remarkArea = document.getElementById('remark');
    const statusSelect = document.getElementById('status');
    const aiStatus = document.getElementById('aiReplyStatus');

    if (btnAiDraft) {
        btnAiDraft.addEventListener('click', function () {
            btnAiDraft.disabled = true;
            aiStatus.classList.remove('d-none');

            fetch("{{ route('ai.reply') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    subject: @json($complaint->subject),
                    description: @json($complaint->description),
                    status: statusSelect.value,
                    department_name: @json($complaint->department->name ?? 'Campus Administration')
                })
            })
            .then(res => res.json())
            .then(data => {
                btnAiDraft.disabled = false;
                aiStatus.classList.add('d-none');
                if (data.success && data.reply) {
                    remarkArea.value = data.reply;
                    remarkArea.focus();
                }
            })
            .catch(err => {
                console.error(err);
                btnAiDraft.disabled = false;
                aiStatus.classList.add('d-none');
            });
        });
    }
});
</script>
@endpush
