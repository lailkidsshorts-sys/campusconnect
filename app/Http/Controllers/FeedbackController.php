<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Complaint;
use App\Models\ComplaintFeedback;
use Illuminate\Http\RedirectResponse;

class FeedbackController extends Controller
{
    public function store(StoreFeedbackRequest $request, Complaint $complaint): RedirectResponse
    {
        $this->authorize('feedback', $complaint);

        ComplaintFeedback::create([
            'complaint_id' => $complaint->id,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
        ]);

        return redirect()->back()->with('success', 'Thank you for your valuable feedback! It helps improve campus services.');
    }
}
