@extends('layouts.student')

@section('title', 'Complaint ' . $complaint->reference_no)

@section('content')
<div class="row g-3 g-md-4">
    <!-- Complaint Details Column -->
    <div class="col-12 col-lg-8">
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
                    <div class="col-6 col-sm-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Department</small>
                        <strong class="text-dark small">{{ $complaint->department->name }}</strong>
                    </div>
                    <div class="col-6 col-sm-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Category</small>
                        <strong class="text-dark small">{{ $complaint->category->name }}</strong>
                    </div>
                    <div class="col-6 col-sm-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Issue Type</small>
                        <span class="text-dark small">{{ $complaint->issueType->name }}</span>
                    </div>
                    <div class="col-6 col-sm-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Submitted On</small>
                        <span class="text-dark small">{{ $complaint->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">Student Description</h6>
                    <div class="p-3 bg-white border rounded text-secondary" style="white-space: pre-line; font-size: 0.95rem;">
                        {{ $complaint->description }}
                    </div>
                </div>

                @if ($complaint->attachment_path)
                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2">Attached Image Evidence</h6>
                        <div class="p-2 border rounded-3 d-inline-block bg-light">
                            <a href="{{ asset('storage/' . $complaint->attachment_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $complaint->attachment_path) }}" alt="Attachment" class="img-fluid rounded" style="max-height: 200px; object-fit: contain;">
                            </a>
                            <div class="mt-1 small text-muted text-center" style="font-size: 0.75rem;">
                                <i class="fas fa-external-link-alt me-1"></i> Tap to view full image
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Student Resolution Feedback Section -->
        @if ($complaint->status->value === 'resolved' || $complaint->status === 'resolved')
            <div class="card border-0 shadow-sm mb-3 mb-md-4">
                <div class="card-header bg-white d-flex align-items-center gap-2 py-3">
                    <i class="fas fa-star text-warning"></i>
                    <h5 class="fw-bold text-dark mb-0 fs-6">Resolution Feedback & Quality Rating</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    @if ($complaint->feedback)
                        <div class="alert bg-light-primary border-0 mb-0">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="fw-bold text-dark small">Your Rating:</span>
                                <div>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $complaint->feedback->rating ? 'text-warning' : 'text-muted text-opacity-25' }}"></i>
                                    @endfor
                                </div>
                                <span class="badge bg-success ms-auto">Submitted</span>
                            </div>
                            @if ($complaint->feedback->comment)
                                <p class="mb-0 text-dark small">"{{ $complaint->feedback->comment }}"</p>
                            @endif
                            <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">Submitted on {{ $complaint->feedback->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                    @else
                        <form action="{{ route('student.complaints.feedback', $complaint) }}" method="POST">
                            @csrf
                            <p class="text-muted small mb-3">This complaint has been marked as resolved by the department. Please rate your satisfaction with the resolution quality.</p>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small d-block">Satisfaction Rating <span class="text-danger">*</span></label>
                                <div class="btn-group w-100 w-sm-auto" role="group">
                                    @for ($r = 1; $r <= 5; $r++)
                                        <input type="radio" class="btn-check" name="rating" id="rating_{{ $r }}" value="{{ $r }}" {{ old('rating', 5) == $r ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-warning py-2 px-3" for="rating_{{ $r }}">
                                            {{ $r }} <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label fw-semibold small">Feedback Remarks (Optional)</label>
                                <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Any suggestions or comments on how the issue was handled?">{{ old('comment') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm px-3 w-100 w-sm-auto">
                                <i class="fas fa-check-circle me-1"></i> Submit Resolution Feedback
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Timeline Column -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-history me-1 text-primary"></i> Status Audit Trail</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                @if ($complaint->responses->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-hourglass-start fs-3 text-warning mb-2 d-block"></i>
                        <span class="small">Complaint submitted and awaiting initial review from {{ $complaint->department->name }}.</span>
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
                                    <div class="fw-semibold text-dark small">{{ $response->user->name ?? 'Department Admin' }}</div>
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
            <a href="{{ route('student.complaints.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2">
                <i class="fas fa-arrow-left me-1"></i> Back to My Complaints
            </a>
        </div>
    </div>
</div>
@endsection
