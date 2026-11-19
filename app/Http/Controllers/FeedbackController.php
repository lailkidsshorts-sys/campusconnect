<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Complaint;
use App\Models\ComplaintFeedback;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;

class FeedbackController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function store(StoreFeedbackRequest $request, Complaint $complaint): RedirectResponse
    {
        $this->authorize('feedback', $complaint);

        $feedback = ComplaintFeedback::create([
            'complaint_id' => $complaint->id,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
        ]);

        $this->notificationService->notifyFeedbackReceived($complaint, $feedback->rating);

        return redirect()->back()->with('success', 'Thank you for your valuable feedback! It helps improve campus services.');
    }
}
