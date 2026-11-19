<?php

namespace App\Http\Controllers;

use App\Services\GeminiAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(
        protected GeminiAiService $aiService
    ) {}

    public function analyze(Request $request): JsonResponse
    {
        $subject = (string) $request->input('subject', '');
        $description = (string) $request->input('description', '');

        if (empty(trim($subject)) && empty(trim($description))) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter at least a brief subject or rough note to analyze.',
            ], 422);
        }

        $result = $this->aiService->analyzeAndPolish($subject, $description);

        return response()->json($result);
    }

    public function generateReply(Request $request): JsonResponse
    {
        $subject = (string) $request->input('subject', '');
        $description = (string) $request->input('description', '');
        $status = (string) $request->input('status', 'in_progress');
        $departmentName = (string) $request->input('department_name', 'Campus Administration');

        $result = $this->aiService->generateAdminReply($subject, $description, $status, $departmentName);

        return response()->json($result);
    }
}
