<?php

namespace App\Services;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class ComplaintRoutingService
{
    /**
     * Create and route a new complaint automatically based on category.
     */
    public function createComplaint(User $student, array $data, ?UploadedFile $attachment = null): Complaint
    {
        // 1. Resolve category and enforce server-side department routing
        $category = Category::with('department')->findOrFail($data['category_id']);
        $departmentId = $category->department_id;

        // 2. Handle file attachment safely
        $attachmentPath = null;
        if ($attachment && $attachment->isValid()) {
            $attachmentPath = $attachment->store('attachments', 'public');
        }

        // 3. Generate sequential reference number
        $referenceNo = Complaint::generateReferenceNumber();

        // 4. Create and return complaint
        return Complaint::create([
            'reference_no' => $referenceNo,
            'user_id' => $student->id,
            'category_id' => $category->id,
            'issue_type_id' => $data['issue_type_id'],
            'department_id' => $departmentId,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'priority' => $data['priority'] ?? ComplaintPriority::MEDIUM->value,
            'status' => ComplaintStatus::PENDING->value,
            'attachment_path' => $attachmentPath,
            'resolved_at' => null,
        ]);
    }
}
