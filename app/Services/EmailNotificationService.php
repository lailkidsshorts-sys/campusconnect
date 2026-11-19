<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * Send welcome email to newly registered student.
     */
    public function sendWelcomeEmail(User $student): void
    {
        try {
            $data = [
                'name' => $student->name,
                'email' => $student->email,
                'roll_no' => $student->roll_no,
                'login_url' => route('login'),
            ];

            Mail::send('emails.welcome', $data, function ($message) use ($student) {
                $message->to($student->email, $student->name)
                        ->subject('CampusConnect | Welcome to Smart Campus Complaint Management');
            });
        } catch (\Throwable $e) {
            Log::warning("Failed to send welcome email to {$student->email}: " . $e->getMessage());
        }
    }

    /**
     * Send complaint submission confirmation to student.
     */
    public function sendComplaintSubmittedEmail(Complaint $complaint): void
    {
        try {
            $student = $complaint->user;
            $data = [
                'student_name' => $student->name,
                'reference_no' => $complaint->reference_no,
                'subject' => $complaint->subject,
                'category' => $complaint->category->name,
                'department' => $complaint->department->name,
                'priority' => ucfirst($complaint->priority->value ?? $complaint->priority),
                'status' => ucfirst(str_replace('_', ' ', $complaint->status->value ?? $complaint->status)),
                'submitted_at' => $complaint->created_at->format('M d, Y h:i A'),
                'view_url' => route('student.complaints.show', $complaint),
            ];

            Mail::send('emails.complaint_submitted', $data, function ($message) use ($student, $complaint) {
                $message->to($student->email, $student->name)
                        ->subject("CampusConnect | Complaint #{$complaint->reference_no} Submitted Successfully");
            });
        } catch (\Throwable $e) {
            Log::warning("Failed to send submission email for complaint #{$complaint->reference_no}: " . $e->getMessage());
        }
    }

    /**
     * Send new complaint alert to Department Administrator.
     */
    public function sendAdminAssignmentEmail(Complaint $complaint): void
    {
        try {
            $admins = User::where('department_id', $complaint->department_id)->get();
            $data = [
                'reference_no' => $complaint->reference_no,
                'subject' => $complaint->subject,
                'category' => $complaint->category->name,
                'department' => $complaint->department->name,
                'priority' => ucfirst($complaint->priority->value ?? $complaint->priority),
                'student_name' => $complaint->user->name ?? 'Student',
                'submitted_at' => $complaint->created_at->format('M d, Y h:i A'),
                'view_url' => route('admin.complaints.show', $complaint),
            ];

            foreach ($admins as $admin) {
                Mail::send('emails.admin_assigned', $data, function ($message) use ($admin, $complaint) {
                    $message->to($admin->email, $admin->name)
                            ->subject("CampusConnect | New Complaint Assigned #{$complaint->reference_no}");
                });
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to send admin assignment email for complaint #{$complaint->reference_no}: " . $e->getMessage());
        }
    }

    /**
     * Send status update & admin remarks notification to student.
     */
    public function sendStatusUpdatedEmail(Complaint $complaint, string $newStatus, string $remark): void
    {
        try {
            $student = $complaint->user;
            $statusLabel = ucfirst(str_replace('_', ' ', $newStatus));
            $data = [
                'student_name' => $student->name,
                'reference_no' => $complaint->reference_no,
                'subject' => $complaint->subject,
                'department' => $complaint->department->name,
                'status' => $statusLabel,
                'remark' => $remark,
                'updated_at' => now()->format('M d, Y h:i A'),
                'view_url' => route('student.complaints.show', $complaint),
            ];

            Mail::send('emails.complaint_status_updated', $data, function ($message) use ($student, $complaint, $statusLabel) {
                $message->to($student->email, $student->name)
                        ->subject("CampusConnect | Complaint #{$complaint->reference_no} Status: {$statusLabel}");
            });
        } catch (\Throwable $e) {
            Log::warning("Failed to send status update email for complaint #{$complaint->reference_no}: " . $e->getMessage());
        }
    }
}
