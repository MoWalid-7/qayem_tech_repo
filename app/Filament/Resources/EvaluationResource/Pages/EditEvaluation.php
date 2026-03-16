<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluation extends EditRecord

{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_ai')
                ->label('Generate by AI')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $record = $this->record;
                    $entity = $record->employee ?? $record->manager;

                    if (!$entity) {
                        \Filament\Notifications\Notification::make()
                            ->title('No professional associated with this evaluation.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $geminiService = new \App\Services\GeminiService();
                    $result = $geminiService->generateEvaluation($entity);

                    if ($result && $result['score'] !== null) {
                        $record->update([
                            'evaluation_text' => $result['text'],
                            'score' => $result['score'],
                            'strengths' => $result['strengths'] ?? null,
                            'weaknesses' => $result['weaknesses'] ?? null,
                            'recommendations' => $result['recommendations'] ?? null,
                        ]);

                        $this->refreshFormData(['evaluation_text', 'score', 'strengths', 'weaknesses', 'recommendations']);

                        \Filament\Notifications\Notification::make()
                            ->title('AI Evaluation generated and fields updated!')
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Failed to generate AI data. Check if metrics are entered.')
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
