<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Create an in-app notification for a user.
     */
    public function notify(User $user, string $type, string $title, string $message, ?string $actionUrl = null): void
    {
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'icon' => $this->getIconForType($type),
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Notify student and department administrators of a new complaint.
     */
    public function notifyComplaintCreated(Complaint $complaint): void
    {
        // 1. Notify student
        $this->notify(
            $complaint->user,
            'complaint_submitted',
            'Complaint Submitted Successfully',
            "Your complaint #{$complaint->reference_no} has been registered and routed to {$complaint->department->name}.",
            route('student.complaints.show', $complaint)
        );

        // 2. Notify Department Admins
        $admins = User::where('department_id', $complaint->department_id)->get();
        foreach ($admins as $admin) {
            $this->notify(
                $admin,
                'complaint_assigned',
                'New Complaint Assigned',
                "New complaint #{$complaint->reference_no} ({$complaint->subject}) assigned to your department.",
                route('admin.complaints.show', $complaint)
            );
        }
    }

    /**
     * Notify student when complaint status or remarks are updated.
     */
    public function notifyComplaintUpdated(Complaint $complaint, string $status, string $remark): void
    {
        $statusLabel = ucfirst(str_replace('_', ' ', $status));
        $this->notify(
            $complaint->user,
            'status_updated',
            "Complaint {$statusLabel}",
            "Department update on #{$complaint->reference_no}: {$remark}",
            route('student.complaints.show', $complaint)
        );
    }

    /**
     * Notify admin when student leaves resolution feedback.
     */
    public function notifyFeedbackReceived(Complaint $complaint, int $rating): void
    {
        $admins = User::where('department_id', $complaint->department_id)->get();
        foreach ($admins as $admin) {
            $this->notify(
                $admin,
                'feedback_received',
                'Resolution Feedback Submitted',
                "Student gave {$rating}/5 stars for complaint #{$complaint->reference_no}.",
                route('admin.complaints.show', $complaint)
            );
        }
    }

    /**
     * Get unread count for user.
     */
    public function getUnreadCount(int $userId): int
    {
        return DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get recent notifications.
     */
    public function getRecentNotifications(int $userId, int $limit = 6): array
    {
        $records = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $records->map(function ($row) {
            $data = json_decode($row->data, true) ?: [];
            return [
                'id' => $row->id,
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? '',
                'action_url' => $data['action_url'] ?? '#',
                'icon' => $data['icon'] ?? 'fas fa-bell',
                'is_read' => ! is_null($row->read_at),
                'created_at' => \Carbon\Carbon::parse($row->created_at)->diffForHumans(),
            ];
        })->toArray();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(int $userId): void
    {
        DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    protected function getIconForType(string $type): string
    {
        return match ($type) {
            'complaint_submitted' => 'fas fa-paper-plane text-primary',
            'complaint_assigned' => 'fas fa-inbox text-info',
            'status_updated' => 'fas fa-sync-alt text-warning',
            'feedback_received' => 'fas fa-star text-warning',
            default => 'fas fa-bell text-secondary',
        };
    }
}
