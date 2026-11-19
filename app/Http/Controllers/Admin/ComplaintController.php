<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ComplaintStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateComplaintStatusRequest;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintResponse;
use App\Models\Department;
use App\Services\EmailNotificationService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplaintController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected EmailNotificationService $emailService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Complaint::with(['user', 'department', 'category', 'issueType']);

        if (! $isSuperAdmin) {
            $query->where('department_id', $user->department_id);
        } else {
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->input('department_id'));
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('roll_no', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $complaints = $query->latest()->paginate(12)->withQueryString();

        $departments = $isSuperAdmin ? Department::all() : collect([$user->department]);
        $categories = Category::all();

        return view('admin.complaints.index', compact('complaints', 'departments', 'categories', 'isSuperAdmin'));
    }

    public function show(Complaint $complaint): View
    {
        $this->authorize('view', $complaint);

        $complaint->load([
            'user',
            'department',
            'category',
            'issueType',
            'responses.user',
            'feedback',
        ]);

        return view('admin.complaints.show', compact('complaint'));
    }

    public function updateStatus(UpdateComplaintStatusRequest $request, Complaint $complaint): RedirectResponse
    {
        $this->authorize('update', $complaint);

        $newStatus = $request->validated('status');
        $remark = $request->validated('remark');

        $resolvedAt = null;
        if (in_array($newStatus, [ComplaintStatus::RESOLVED->value, ComplaintStatus::REJECTED->value])) {
            $resolvedAt = Carbon::now();
        }

        $complaint->update([
            'status' => $newStatus,
            'resolved_at' => $resolvedAt,
        ]);

        ComplaintResponse::create([
            'complaint_id' => $complaint->id,
            'user_id' => $request->user()->id,
            'remark' => $remark,
            'status_after' => $newStatus,
        ]);

        // Send notifications and email to student
        $this->notificationService->notifyComplaintUpdated($complaint, $newStatus, $remark);
        $this->emailService->sendStatusUpdatedEmail($complaint, $newStatus, $remark);

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', "Complaint status updated to '".ucfirst(str_replace('_', ' ', $newStatus))."' and notification sent.");
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Complaint::with(['user', 'department', 'category', 'issueType']);

        if (! $isSuperAdmin) {
            $query->where('department_id', $user->department_id);
        } elseif ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $filename = 'CampusConnect_Complaints_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Reference No',
                'Date Submitted',
                'Student Name',
                'Roll No',
                'Department',
                'Category',
                'Issue Type',
                'Subject',
                'Priority',
                'Status',
                'Resolved At',
            ]);

            $query->chunk(100, function ($complaints) use ($handle) {
                foreach ($complaints as $c) {
                    fputcsv($handle, [
                        $c->reference_no,
                        $c->created_at->format('Y-m-d H:i'),
                        $c->user->name ?? 'N/A',
                        $c->user->roll_no ?? 'N/A',
                        $c->department->name ?? 'N/A',
                        $c->category->name ?? 'N/A',
                        $c->issueType->name ?? 'N/A',
                        $c->subject,
                        strtoupper($c->priority->value ?? $c->priority),
                        strtoupper(str_replace('_', ' ', $c->status->value ?? $c->status)),
                        $c->resolved_at ? $c->resolved_at->format('Y-m-d H:i') : 'Pending',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
