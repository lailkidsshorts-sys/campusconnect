<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $departmentId = $isSuperAdmin ? null : $user->department_id;

        // Statistics
        $stats = $this->analyticsService->getSummaryStats($departmentId);

        // Chart Data
        $statusChart = $this->analyticsService->getComplaintsByStatus($departmentId);
        $trendsChart = $this->analyticsService->getTrendsLast30Days($departmentId);
        $topIssuesChart = $this->analyticsService->getTopIssueTypes($departmentId);

        $departmentChart = null;
        if ($isSuperAdmin) {
            $departmentChart = $this->analyticsService->getComplaintsPerDepartment();
        }

        // Recent complaints for dashboard preview
        $complaintsQuery = Complaint::with(['user', 'department', 'category', 'issueType']);
        if (! $isSuperAdmin) {
            $complaintsQuery->where('department_id', $departmentId);
        }

        $recentComplaints = $complaintsQuery->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'stats',
            'statusChart',
            'trendsChart',
            'topIssuesChart',
            'departmentChart',
            'recentComplaints',
            'isSuperAdmin'
        ));
    }
}
