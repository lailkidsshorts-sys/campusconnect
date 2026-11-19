<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $departmentId = $isSuperAdmin ? null : $user->department_id;

        $stats = $this->analyticsService->getSummaryStats($departmentId);
        $statusChart = $this->analyticsService->getComplaintsByStatus($departmentId);
        $trendsChart = $this->analyticsService->getTrendsLast30Days($departmentId);
        $topIssuesChart = $this->analyticsService->getTopIssueTypes($departmentId);

        $departmentChart = null;
        if ($isSuperAdmin) {
            $departmentChart = $this->analyticsService->getComplaintsPerDepartment();
        }

        return view('admin.analytics.index', compact(
            'stats',
            'statusChart',
            'trendsChart',
            'topIssuesChart',
            'departmentChart',
            'isSuperAdmin'
        ));
    }
}
