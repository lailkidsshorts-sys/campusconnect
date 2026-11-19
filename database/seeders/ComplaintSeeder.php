<?php

namespace Database\Seeders;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintFeedback;
use App\Models\ComplaintResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', UserRole::STUDENT)->get();
        $categories = Category::with(['department', 'issueTypes'])->get();
        $deptAdmins = User::where('role', UserRole::DEPT_ADMIN)->get()->keyBy('department_id');

        $sampleSubjects = [
            'Poor food quality or hygiene' => [
                'Stale sandwiches served at morning break',
                'Cold lunch items and unclean dining trays',
                'Insect noticed near drink dispenser area',
            ],
            'High food prices' => [
                'Sudden price hike on everyday snack items',
                'Tea price increased without student notice',
                'Meal deal combo excessively expensive',
            ],
            'Limited food variety' => [
                'No healthy or vegetarian options available',
                'Only fried snacks served after 1:00 PM',
            ],
            'Unprofessional staff behaviour or poor service' => [
                'Cafeteria counter staff unresponsive during rush hour',
                'Rude behaviour when asked for bill receipt',
            ],
            'Insufficient or outdated books and learning resources' => [
                'Database System 8th edition copies not available',
                'Software Engineering reference books missing from shelf',
            ],
            'Librarian availability or staff behaviour' => [
                'Issue counter unattended during 11:00 AM break',
                'Difficulty getting book reservation confirmation',
            ],
            'Library opening and closing hours' => [
                'Need library extended hours during midterm exam week',
                'Library closes early on Fridays',
            ],
            'Insufficient seating or study space' => [
                'Overcrowded discussion area during peak hours',
                'Silent study room AC not working and seats broken',
            ],
            'Non-functional computers' => [
                'Lab 3 PCs 12 to 18 failing to boot to Windows',
                'Keyboard and mouse broken at Lab 1 Station 05',
                'Blue screen errors during programming lab practical',
            ],
            'Internet connectivity issues' => [
                'Wi-Fi signal drops frequently in CS Block 2nd floor',
                'Extremely slow download speed in Lab 4 during labs',
            ],
            'Projector or multimedia equipment problems' => [
                'Projector in Room 204 has distorted color display',
                'HDMI cable missing from lecturer podium in Lab 2',
            ],
            'Missing or unavailable software' => [
                'Android Studio SDK components missing on Lab 5 systems',
                'Visual Studio Code PHP extension pack required for class',
            ],
            'Teachers arriving late to lectures' => [
                'Morning Algorithms lecture consistently delayed by 20 mins',
                'Class starting late due to room scheduling clash',
            ],
            'Unavailability of lecture notes or learning materials' => [
                'Week 4 slides and lab manual not uploaded to portal',
                'Reference slides for midterms still missing on LMS',
            ],
            'Inadequate explanation of course topics' => [
                'Need extra tutorial session for Dynamic Programming concepts',
                'Complex topics rushed in lecture without practical example',
            ],
            'Classroom management or discipline issues' => [
                'Excessive back-row noise disturbing lecture in Room 102',
                'Students entering class 30 minutes late disrupts session',
            ],
        ];

        $statuses = [
            ComplaintStatus::PENDING,
            ComplaintStatus::IN_PROGRESS,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::REJECTED,
        ];

        $priorities = [
            ComplaintPriority::LOW,
            ComplaintPriority::MEDIUM,
            ComplaintPriority::HIGH,
        ];

        $complaintCount = 0;

        foreach ($categories as $category) {
            $dept = $category->department;
            $deptAdmin = $deptAdmins[$dept->id] ?? null;

            foreach ($category->issueTypes as $issueType) {
                $subjects = $sampleSubjects[$issueType->name] ?? [
                    "Report regarding {$issueType->name}",
                ];

                foreach ($subjects as $idx => $subject) {
                    $complaintCount++;
                    $student = $students->random();
                    $status = $statuses[($complaintCount + $idx) % count($statuses)];
                    $priority = $priorities[($complaintCount) % count($priorities)];
                    $daysAgo = rand(1, 28);
                    $createdAt = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 10));

                    $refNo = sprintf('CC-2026-%04d', $complaintCount);

                    $resolvedAt = in_array($status, [ComplaintStatus::RESOLVED, ComplaintStatus::REJECTED])
                        ? (clone $createdAt)->addDays(rand(1, 3))
                        : null;

                    $complaint = Complaint::create([
                        'reference_no' => $refNo,
                        'user_id' => $student->id,
                        'category_id' => $category->id,
                        'issue_type_id' => $issueType->id,
                        'department_id' => $dept->id,
                        'subject' => $subject,
                        'description' => "Detailed complaint statement from student: {$subject}. This issue was observed in recent campus sessions and requires administrative attention.",
                        'priority' => $priority,
                        'status' => $status,
                        'attachment_path' => null,
                        'resolved_at' => $resolvedAt,
                        'created_at' => $createdAt,
                        'updated_at' => $resolvedAt ?? $createdAt,
                    ]);

                    if ($status !== ComplaintStatus::PENDING && $deptAdmin) {
                        if ($status === ComplaintStatus::IN_PROGRESS) {
                            ComplaintResponse::create([
                                'complaint_id' => $complaint->id,
                                'user_id' => $deptAdmin->id,
                                'remark' => 'Complaint acknowledged by department. Maintenance staff/team has been assigned to inspect and rectify.',
                                'status_after' => ComplaintStatus::IN_PROGRESS->value,
                                'created_at' => (clone $createdAt)->addHours(4),
                            ]);
                        } elseif ($status === ComplaintStatus::RESOLVED) {
                            ComplaintResponse::create([
                                'complaint_id' => $complaint->id,
                                'user_id' => $deptAdmin->id,
                                'remark' => 'Initial review completed. Action taken by technical/administrative team.',
                                'status_after' => ComplaintStatus::IN_PROGRESS->value,
                                'created_at' => (clone $createdAt)->addHours(6),
                            ]);
                            ComplaintResponse::create([
                                'complaint_id' => $complaint->id,
                                'user_id' => $deptAdmin->id,
                                'remark' => 'Issue verified and resolved. All concerned systems/items have been restored.',
                                'status_after' => ComplaintStatus::RESOLVED->value,
                                'created_at' => $resolvedAt,
                            ]);

                            ComplaintFeedback::create([
                                'complaint_id' => $complaint->id,
                                'rating' => rand(4, 5),
                                'comment' => 'Prompt action taken by the administration. Thank you for resolving it quickly.',
                                'created_at' => (clone $resolvedAt)->addHours(2),
                            ]);
                        } elseif ($status === ComplaintStatus::REJECTED) {
                            ComplaintResponse::create([
                                'complaint_id' => $complaint->id,
                                'user_id' => $deptAdmin->id,
                                'remark' => 'Unable to take action: The reported item falls outside the department scope or duplicate inquiry submitted.',
                                'status_after' => ComplaintStatus::REJECTED->value,
                                'created_at' => $resolvedAt,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
