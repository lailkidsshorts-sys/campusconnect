@extends('layouts.student')

@section('title', 'Submit Complaint')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm p-3 p-md-4">
            <div class="d-flex align-items-center gap-3 mb-3 mb-md-4 border-bottom pb-3">
                <div class="stat-icon bg-light-primary text-primary">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-0 fs-5 fs-md-4">Submit Campus Complaint</h4>
                    <small class="text-muted">Complaints are automatically routed to the responsible department.</small>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 small border-0 mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('student.complaints.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- AI Smart Complaint Assistant Box -->
                <div class="card bg-light border-0 shadow-none mb-3 mb-md-4 p-3 rounded-3" style="border-left: 4px solid var(--cc-blue) !important; background-color: #F0F4FA !important;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white"><i class="fas fa-brain me-1"></i> Gemini AI Assistant</span>
                            <span class="small text-dark fw-semibold">Smart Complaint Categorization & Polish</span>
                        </div>
                        <button type="button" id="btnAiAssist" class="btn btn-sm btn-outline-primary fw-semibold">
                            <i class="fas fa-magic me-1"></i> ✨ AI Polish & Suggest
                        </button>
                    </div>
                    <p class="small text-muted mb-0 mt-2" style="font-size: 0.8rem;">
                        Type your rough notes below (supports English or Roman Urdu), then click <strong>AI Polish & Suggest</strong> to automatically classify the category, detect department, set priority, and formulate a formal institutional report.
                    </p>
                    <div id="aiLoadingIndicator" class="small text-primary d-none py-1 mt-2">
                        <i class="fas fa-spinner fa-spin me-1"></i> Analyzing details and generating professional draft...
                    </div>
                    <div id="aiSuggestionNotice" class="alert alert-success d-none py-2 px-3 small mb-0 mt-2 border-0">
                        <i class="fas fa-check-circle me-1"></i> <strong>AI Analysis Applied:</strong> Category, department routing, priority, and polished wording have been filled in below. You can edit any field before submitting.
                    </div>
                </div>

                <!-- Automatic Routing Alert -->
                <div class="alert bg-light-primary border-0 mb-3 mb-md-4 d-flex align-items-start gap-2 p-3">
                    <i class="fas fa-shield-alt text-primary fs-5 mt-1"></i>
                    <div>
                        <strong class="text-primary d-block small fw-bold">Automatic Department Routing Active</strong>
                        <span class="small text-muted" id="routingInfo">Select a category below to see which institutional department will handle your complaint.</span>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Category Select -->
                    <div class="col-12 col-md-6">
                        <label for="category_id" class="form-label fw-semibold small">1. Complaint Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Choose Category --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }} data-dept="{{ $cat->department->name ?? 'Responsible Department' }}">
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dependent Issue Type Select -->
                    <div class="col-12 col-md-6">
                        <label for="issue_type_id" class="form-label fw-semibold small">2. Specific Issue Type <span class="text-danger">*</span></label>
                        <select name="issue_type_id" id="issue_type_id" class="form-select @error('issue_type_id') is-invalid @enderror" required disabled>
                            <option value="">-- Select Category First --</option>
                        </select>
                    </div>

                    <!-- Priority Select -->
                    <div class="col-12 col-md-6">
                        <label for="priority" class="form-label fw-semibold small">Priority Level <span class="text-danger">*</span></label>
                        <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low (General Feedback / Non-blocking)</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium (Standard Campus Problem)</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High (Urgent / Classroom Disruption)</option>
                        </select>
                    </div>

                    <!-- Supporting Attachment -->
                    <div class="col-12 col-md-6">
                        <label for="attachment" class="form-label fw-semibold small">Supporting Image (Optional)</label>
                        <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                        <div class="form-text small" style="font-size: 0.75rem;">JPG, JPEG, PNG (Max 2MB).</div>
                    </div>

                    <!-- Subject -->
                    <div class="col-12">
                        <label for="subject" class="form-label fw-semibold small">Subject / Brief Summary <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" placeholder="e.g. Broken keyboard at Lab 1 Station 5" value="{{ old('subject') }}" required>
                    </div>

                    <!-- Detailed Description -->
                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold small">Detailed Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Describe the location, problem details, and how it impacts you..." required>{{ old('description') }}</textarea>
                    </div>

                    <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('student.complaints.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 w-100 w-sm-auto">
                            <i class="fas fa-paper-plane me-1"></i> Submit Complaint
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category_id');
    const issueTypeSelect = document.getElementById('issue_type_id');
    const routingInfo = document.getElementById('routingInfo');

    const previousIssueTypeId = "{{ old('issue_type_id') }}";

    function loadIssueTypes(categoryId, preselectId = null) {
        if (!categoryId) {
            issueTypeSelect.innerHTML = '<option value="">-- Select Category First --</option>';
            issueTypeSelect.disabled = true;
            routingInfo.innerHTML = 'Select a category below to see which institutional department will handle your complaint.';
            return;
        }

        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const deptName = selectedOption.getAttribute('data-dept');
        if (deptName) {
            routingInfo.innerHTML = 'This complaint will be automatically routed to: <strong class="text-primary">' + deptName + '</strong>.';
        }

        issueTypeSelect.disabled = true;
        issueTypeSelect.innerHTML = '<option value="">Loading issues...</option>';

        fetch("{{ url('student/categories') }}/" + categoryId + "/issue-types")
            .then(response => response.json())
            .then(data => {
                issueTypeSelect.innerHTML = '<option value="">-- Choose Issue Type --</option>';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    if (preselectId && preselectId == item.id) {
                        option.selected = true;
                    }
                    issueTypeSelect.appendChild(option);
                });
                issueTypeSelect.disabled = false;
            })
            .catch(err => {
                console.error(err);
                issueTypeSelect.innerHTML = '<option value="">Failed to load issues</option>';
            });
    }

    categorySelect.addEventListener('change', function () {
        loadIssueTypes(this.value);
    });

    if (categorySelect.value) {
        loadIssueTypes(categorySelect.value, previousIssueTypeId);
    }

    // AI Smart Assistant Handler
    const btnAiAssist = document.getElementById('btnAiAssist');
    const aiLoadingIndicator = document.getElementById('aiLoadingIndicator');
    const aiSuggestionNotice = document.getElementById('aiSuggestionNotice');
    const subjectInput = document.getElementById('subject');
    const descriptionInput = document.getElementById('description');
    const prioritySelect = document.getElementById('priority');

    if (btnAiAssist) {
        btnAiAssist.addEventListener('click', function () {
            const currentSubject = subjectInput.value.trim();
            const currentDesc = descriptionInput.value.trim();

            if (!currentSubject && !currentDesc) {
                Swal.fire({
                    icon: 'info',
                    title: 'Rough Notes Needed',
                    text: 'Please type a few words or sentences in the Subject or Description field (in English or Roman Urdu) for the AI to analyze.',
                    confirmButtonColor: '#1E3A8A'
                });
                return;
            }

            btnAiAssist.disabled = true;
            btnAiAssist.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Analyzing...';
            aiLoadingIndicator.classList.remove('d-none');
            aiSuggestionNotice.classList.add('d-none');

            fetch("{{ route('ai.analyze') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    subject: currentSubject,
                    description: currentDesc
                })
            })
            .then(res => res.json())
            .then(data => {
                btnAiAssist.disabled = false;
                btnAiAssist.innerHTML = '<i class="fas fa-magic me-1"></i> ✨ AI Polish & Suggest';
                aiLoadingIndicator.classList.add('d-none');

                if (data.success) {
                    if (data.category_id) {
                        categorySelect.value = data.category_id;
                        loadIssueTypes(data.category_id, data.issue_type_id);
                    }
                    if (data.priority) {
                        prioritySelect.value = data.priority;
                    }
                    if (data.polished_subject) {
                        subjectInput.value = data.polished_subject;
                    }
                    if (data.polished_description) {
                        descriptionInput.value = data.polished_description;
                    }

                    aiSuggestionNotice.classList.remove('d-none');
                    aiSuggestionNotice.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Notice',
                        text: data.message || 'Unable to analyze text.',
                        confirmButtonColor: '#1E3A8A'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                btnAiAssist.disabled = false;
                btnAiAssist.innerHTML = '<i class="fas fa-magic me-1"></i> ✨ AI Polish & Suggest';
                aiLoadingIndicator.classList.add('d-none');
                Swal.fire({
                    icon: 'error',
                    title: 'Assistant Error',
                    text: 'Could not connect to the AI service. You can still fill out the form manually.',
                    confirmButtonColor: '#1E3A8A'
                });
            });
        });
    }
});
</script>
@endpush
