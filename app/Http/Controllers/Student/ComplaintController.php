<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\IssueType;
use App\Services\ComplaintRoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function __construct(
        protected ComplaintRoutingService $routingService
    ) {}

    public function index(Request $request): View
    {
        $query = Complaint::with(['department', 'category', 'issueType'])
            ->forUser($request->user()->id);

        if ($request->filled('status')) {
            $query->status($request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $complaints = $query->latest()->paginate(10)->withQueryString();

        return view('student.complaints.index', compact('complaints'));
    }

    public function create(): View
    {
        $categories = Category::with('issueTypes')->get();
        return view('student.complaints.create', compact('categories'));
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $complaint = $this->routingService->createComplaint(
            $request->user(),
            $request->validated(),
            $request->file('attachment')
        );

        return redirect()->route('student.complaints.show', $complaint)
            ->with('success', "Complaint submitted successfully! Reference Number: {$complaint->reference_no}");
    }

    public function show(Complaint $complaint): View
    {
        $this->authorize('view', $complaint);

        $complaint->load([
            'department',
            'category',
            'issueType',
            'responses.user',
            'feedback',
        ]);

        return view('student.complaints.show', compact('complaint'));
    }

    /**
     * AJAX endpoint to get issue types for selected category
     */
    public function getIssueTypes(Category $category): JsonResponse
    {
        $issueTypes = $category->issueTypes()->select('id', 'name')->get();
        return response()->json($issueTypes);
    }
}
