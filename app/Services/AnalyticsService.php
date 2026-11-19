<?php

namespace App\Services;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\Department;
use App\Models\IssueType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get summary counts (total, pending, in_progress, resolved, rejected, avg rating).
     */
    public function getSummaryStats(?int $departmentId = null): array
    {
        $baseQuery = Complaint::query();
        if ($departmentId) {
            $baseQuery->where('department_id', $departmentId);
        }

        $total = (clone $baseQuery)->count();
        $pending = (clone $baseQuery)->where('status', ComplaintStatus::PENDING->value)->count();
        $inProgress = (clone $baseQuery)->where('status', ComplaintStatus::IN_PROGRESS->value)->count();
        $resolved = (clone $baseQuery)->where('status', ComplaintStatus::RESOLVED->value)->count();
        $rejected = (clone $baseQuery)->where('status', ComplaintStatus::REJECTED->value)->count();

        // Feedback rating
        $ratingQuery = DB::table('complaint_feedback')
            ->join('complaints', 'complaint_feedback.complaint_id', '=', 'complaints.id');

        if ($departmentId) {
            $ratingQuery->where('complaints.department_id', $departmentId);
        }

        $avgRating = round((float) $ratingQuery->avg('complaint_feedback.rating'), 1);

        return [
            'total' => $total,
            'pending' => $pending,
            'in_progress' => $inProgress,
            'resolved' => $resolved,
            'rejected' => $rejected,
            'avg_rating' => $avgRating ?: 0,
        ];
    }

    /**
     * Comparison of complaints received by departments (for Super Admin).
     */
    public function getComplaintsPerDepartment(): array
    {
        $departments = Department::withCount('complaints')->get();

        return [
            'labels' => $departments->pluck('name')->toArray(),
            'data' => $departments->pluck('complaints_count')->toArray(),
        ];
    }

    /**
     * Complaints distribution by status.
     */
    public function getComplaintsByStatus(?int $departmentId = null): array
    {
        $query = Complaint::query();
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $counts = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statuses = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'rejected' => 'Rejected',
        ];

        $labels = [];
        $data = [];
        $colors = ['#F59E0B', '#2563EB', '#0B7A3B', '#DC2626'];

        foreach ($statuses as $key => $label) {
            $labels[] = $label;
            $data[] = $counts[$key] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    /**
     * Complaints trend over the last 30 days.
     */
    public function getTrendsLast30Days(?int $departmentId = null): array
    {
        $startDate = Carbon::now()->subDays(29)->startOfDay();

        $query = Complaint::query()
            ->where('created_at', '>=', $startDate);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $rawResults = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date')
            ->toArray();

        $labels = [];
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->format('Y-m-d');
            $display = Carbon::now()->subDays($i)->format('M d');
            $labels[] = $display;
            $data[] = $rawResults[$day] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Top 5 most reported issue types.
     */
    public function getTopIssueTypes(?int $departmentId = null): array
    {
        $query = DB::table('complaints')
            ->join('issue_types', 'complaints.issue_type_id', '=', 'issue_types.id')
            ->select('issue_types.name', DB::raw('count(complaints.id) as count'))
            ->groupBy('issue_types.id', 'issue_types.name')
            ->orderByDesc('count')
            ->limit(5);

        if ($departmentId) {
            $query->where('complaints.department_id', $departmentId);
        }

        $results = $query->get();

        return [
            'labels' => $results->pluck('name')->toArray(),
            'data' => $results->pluck('count')->toArray(),
        ];
    }
}
