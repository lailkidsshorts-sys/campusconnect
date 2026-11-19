<?php

namespace App\Policies;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    /**
     * Determine whether the user can view the complaint.
     */
    public function view(User $user, Complaint $complaint): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isDeptAdmin()) {
            return (int) $complaint->department_id === (int) $user->department_id;
        }

        if ($user->isStudent()) {
            return (int) $complaint->user_id === (int) $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update / respond to the complaint.
     */
    public function update(User $user, Complaint $complaint): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isDeptAdmin()) {
            return (int) $complaint->department_id === (int) $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the student can leave feedback on the complaint.
     */
    public function feedback(User $user, Complaint $complaint): bool
    {
        return $user->isStudent()
            && (int) $complaint->user_id === (int) $user->id
            && $complaint->status === ComplaintStatus::RESOLVED
            && ! $complaint->feedback()->exists();
    }
}
