<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\Evaluation;
use App\Models\Manager;
use App\Services\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateEvaluationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Max seconds before the job is killed */
    public int $timeout = 120;

    /** Number of retry attempts on failure */
    public int $tries = 2;

    public function __construct(
        private readonly Employee|Manager $entity
    ) {}

    public function handle(GeminiService $gemini): void
    {
        $result = $gemini->generateEvaluation($this->entity);

        if ($result && isset($result['score'])) {
            $data = [
                'evaluation_text' => $result['text'],
                'score'           => $result['score'],
                'strengths'       => $result['strengths'] ?? null,
                'weaknesses'      => $result['weaknesses'] ?? null,
                'recommendations' => $result['recommendations'] ?? null,
            ];

            if ($this->entity instanceof Employee) {
                $data['employee_id'] = $this->entity->id;
            } else {
                $data['manager_id'] = $this->entity->id;
            }

            Evaluation::create($data);

            Log::info('GenerateEvaluationJob: Evaluation created', [
                'entity_type' => get_class($this->entity),
                'entity_id'   => $this->entity->id,
            ]);
        } else {
            Log::error('GenerateEvaluationJob: AI evaluation failed or returned no score', [
                'entity_type' => get_class($this->entity),
                'entity_id'   => $this->entity->id,
            ]);
        }
    }
}
