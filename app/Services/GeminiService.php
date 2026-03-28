<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }

    public function generateEvaluation($entity)
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API key is not configured.');
            return ['text' => 'AI Configuration Error.', 'score' => null];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $departmentName = ($entity instanceof \App\Models\Employee && $entity->department) ? $entity->department->name : 'N/A';
        $role = ($entity instanceof \App\Models\Manager) ? $entity->role : ($entity->job_title ?? 'Employee');
        $hireDate = $entity->hire_date ? $entity->hire_date : 'Not set';

        $prompt = "Evaluate this professional based on the following data:
Name: {$entity->name}
Role: {$role}
Department: {$departmentName}
Hire Date: {$hireDate}
Attendance rate: " . ($entity->attendance_rate ?? 'N/A') . "%
Tasks completed: {$entity->tasks_completed}
Tasks requested: {$entity->tasks_requested}

Provide a detailed professional evaluation in Markdown structure.
Your response MUST be in raw JSON format with exactly these keys:
1. \"text\": A string containing a detailed professional evaluation (Markdown).
2. \"score\": An integer between 1 and 10 representing the Overall Score.
3. \"strengths\": A string summarizing top strengths (MUST NOT be empty).
4. \"weaknesses\": A string summarizing areas for improvement (MUST NOT be empty).
5. \"recommendations\": A string summarizing growth steps (MUST NOT be empty).

IMPORTANT: The current user interface language is " . (app()->getLocale() == 'ar' ? 'Arabic' : 'English') . ". 
YOU MUST write all the values in the JSON object, INCLUDING all Markdown headers and content in the \"text\" field, in " . (app()->getLocale() == 'ar' ? 'Arabic' : 'English') . ", while maintaining the strict JSON structure with English keys.
The Markdown headers should be localized (e.g., use Arabic headers if the language is Arabic).

This is a strict requirement for a system integration. Do NOT wrap the JSON in markdown code blocks. Just return the raw JSON object.";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::post($url, $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiText = $responseData['candidates'][0]['content']['parts'][0]['text'];

                    // Parse the JSON. 
                    $aiText = trim($aiText);

                    // Use regex to extract JSON block if AI wraps it in markdown
                    if (preg_match('/```json\s*(.*?)\s*```/s', $aiText, $matches)) {
                        $jsonContent = $matches[1];
                    } elseif (preg_match('/\{(?:[^{}]|(?R))*\}/s', $aiText, $matches)) {
                        // Attempt to find the first JSON-like object
                        $jsonContent = $matches[0];
                    } else {
                        $jsonContent = $aiText;
                    }

                    $parsed = json_decode($jsonContent, true);

                    if (json_last_error() === JSON_ERROR_NONE && isset($parsed['text'], $parsed['score'])) {
                        return [
                            'text' => $parsed['text'],
                            'score' => (int) $parsed['score'],
                            'strengths' => $parsed['strengths'] ?? '',
                            'weaknesses' => $parsed['weaknesses'] ?? '',
                            'recommendations' => $parsed['recommendations'] ?? ''
                        ];
                    }

                    // Fallback parsing if AI returns simple text instead of JSON
                    Log::warning('Gemini API did not return valid JSON or missing keys.', [
                        'content' => $aiText,
                        'extracted' => $jsonContent,
                        'json_error' => json_last_error_msg()
                    ]);
                    return [
                        'text' => $aiText,
                        'score' => null
                    ];
                }
            } else {
                Log::error('Gemini API Error', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Gemini API Exception', ['message' => $e->getMessage()]);
        }

        return null;
    }

    public function chat(string $prompt, string $context = '')
    {
        if (empty($this->apiKey)) {
            return 'AI Configuration Error.';
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $fullPrompt = "System Context: You are a professional HR Assistant for Evalo. 
The user is currently using the interface in " . (app()->getLocale() == 'ar' ? 'Arabic' : 'English') . ". 
YOU MUST respond to the user message in " . (app()->getLocale() == 'ar' ? 'Arabic' : 'English') . ".
{$context}

User Message: {$prompt}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $fullPrompt]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::post($url, $payload);
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'No response from AI.';
            }
            Log::error('Gemini Chat Error', ['status' => $response->status(), 'response' => $response->body()]);
            if ($response->status() === 429) {
                return 'AI Assistant is temporarily busy (Quota limit reached). Please try again in a minute.';
            }
        } catch (\Exception $e) {
            Log::error('Gemini Chat Exception', ['message' => $e->getMessage()]);
        }

        return 'Failed to reach AI assistant. Please check your connection or try again later.';
    }
}
