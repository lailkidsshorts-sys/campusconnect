<?php

namespace App\Services;

use App\Models\Category;
use App\Models\IssueType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiService
{
    /**
     * Analyze rough complaint notes and suggest category, issue type, priority, and polished text.
     */
    public function analyzeAndPolish(string $subject, string $description): array
    {
        $apiKey = env('GEMINI_API_KEY');

        if (! empty($apiKey)) {
            try {
                $prompt = "You are an AI Campus Complaint Assistant for CampusConnect. Analyze this student complaint:
Subject: {$subject}
Description: {$description}

Categories:
1. Cafeteria Services (Food quality, high prices, limited variety, staff behaviour)
2. Library Facilities & Services (Outdated books, librarian availability, hours, seating space)
3. Laboratories & IT Facilities (Non-functional computers, internet connectivity, projector problems, missing software)
4. Teaching & Academic Affairs (Teachers arriving late, unavailability of notes, inadequate explanation, discipline)

Return JSON with keys:
category_slug (one of: cafeteria-services, library-services, lab-it-facilities, teaching-academic-affairs)
priority (one of: low, medium, high)
polished_subject (clear professional 5-10 word title)
polished_description (constructive, formal 2-4 sentences explaining location and impact)";

                $response = Http::timeout(5)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $jsonText = $response->json('candidates.0.content.parts.0.text');
                    $parsed = json_decode($jsonText, true);
                    if ($parsed && isset($parsed['category_slug'])) {
                        return $this->formatResult($parsed);
                    }
                }
            } catch (\Throwable $e) {
                Log::info("Gemini API call skipped or timed out, using smart heuristic fallback: " . $e->getMessage());
            }
        }

        // Smart Heuristic NLP Fallback (Works 100% offline & during competitions)
        return $this->heuristicAnalysis($subject, $description);
    }

    protected function heuristicAnalysis(string $subject, string $text): array
    {
        $content = strtolower($subject . ' ' . $text);

        // Department & Category Detection
        $catSlug = 'lab-it-facilities'; // default
        if (preg_match('/(food|eat|meal|tea|snack|cafeteria|canteen|price|tray|hygiene|lunch|drink)/', $content)) {
            $catSlug = 'cafeteria-services';
        } elseif (preg_match('/(book|library|librarian|study|reading|shelf|hours|shelf)/', $content)) {
            $catSlug = 'library-services';
        } elseif (preg_match('/(teacher|professor|lecture|slide|notes|class|grade|exam|discipline|attendance|course)/', $content)) {
            $catSlug = 'teaching-academic-affairs';
        }

        // Priority Detection
        $priority = 'medium';
        if (preg_match('/(urgent|danger|exam|immediately|severe|broken|blackout|emergency|clash)/', $content)) {
            $priority = 'high';
        } elseif (preg_match('/(suggestion|request|minor|improvement|feedback)/', $content)) {
            $priority = 'low';
        }

        // Polished Text Generation
        $trimmedSubject = ucfirst(trim($subject ?: 'Campus Facility Issue Report'));
        $polishedDesc = "I would like to bring an institutional matter to the administration's attention regarding {$trimmedSubject}. ";
        $polishedDesc .= ucfirst(trim($text)) . ". ";
        $polishedDesc .= "This situation causes considerable disruption to ongoing student academic and campus activities. I kindly request the responsible administration to inspect and resolve this promptly.";

        return $this->formatResult([
            'category_slug' => $catSlug,
            'priority' => $priority,
            'polished_subject' => $trimmedSubject,
            'polished_description' => $polishedDesc,
        ]);
    }

    protected function formatResult(array $data): array
    {
        $category = Category::where('slug', $data['category_slug'] ?? 'lab-it-facilities')->first();
        if (! $category) {
            $category = Category::first();
        }

        $issueType = $category->issueTypes()->first();

        return [
            'success' => true,
            'category_id' => $category->id,
            'category_name' => $category->name,
            'department_name' => $category->department->name ?? 'Campus Administration',
            'issue_type_id' => $issueType ? $issueType->id : null,
            'priority' => $data['priority'] ?? 'medium',
            'polished_subject' => $data['polished_subject'] ?? 'Campus Facility Inquiry',
            'polished_description' => $data['polished_description'] ?? 'Formal complaint report submitted for departmental review.',
        ];
    }
}
