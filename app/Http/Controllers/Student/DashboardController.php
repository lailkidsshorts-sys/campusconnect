<?php

namespace App\Http\Controllers\Student;

use App\Enums\ComplaintStatus;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $stats = [
            'total' => Complaint::forUser($userId)->count(),
            'pending' => Complaint::forUser($userId)->status(ComplaintStatus::PENDING)->count(),
            'in_progress' => Complaint::forUser($userId)->status(ComplaintStatus::IN_PROGRESS)->count(),
            'resolved' => Complaint::forUser($userId)->status(ComplaintStatus::RESOLVED)->count(),
            'rejected' => Complaint::forUser($userId)->status(ComplaintStatus::REJECTED)->count(),
        ];

        $recentComplaints = Complaint::with(['department', 'category', 'issueType'])
            ->forUser($userId)
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact('stats', 'recentComplaints'));
    }
}
