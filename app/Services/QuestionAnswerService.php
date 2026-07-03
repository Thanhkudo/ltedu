<?php

namespace App\Services;

use App\Models\QuestionBankItem;
use Illuminate\Support\Collection;

class QuestionAnswerService
{
    public function evaluateQuestionFlowDetails(Collection $questions, array $answers, float $maxScore = 0): array
    {
        $total = $questions->count();
        $attempted = 0;
        $correct = 0;
        $items = [];
        $pointPerQuestion = $total > 0 ? $maxScore / $total : 0;

        foreach ($questions as $question) {
            $answerValue = trim((string) ($answers[(string) $question->id] ?? ''));
            $isAnswered = $answerValue !== '';
            $isCorrect = false;
            $expected = $this->expectedAnswerForQuestion($question);

            if ($isAnswered) {
                $attempted++;
                $isCorrect = $this->isQuestionAnswerCorrect($question, $answerValue, $expected);

                if ($isCorrect) {
                    $correct++;
                }
            }

            $questionScore = $isCorrect ? round($pointPerQuestion, 2) : 0;
            $items[(string) $question->id] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'group_id' => $question->group_id,
                'group_title' => optional($question->group)->title,
                'answer' => $answerValue,
                'expected' => $expected,
                'answer_text' => $this->formatQuestionAnswer($question, $answerValue),
                'expected_text' => $this->formatQuestionAnswer($question, $expected),
                'is_correct' => $isCorrect,
                'score' => $questionScore,
                'max_score' => round($pointPerQuestion, 2),
                'answer_mode' => $question->answer_mode,
                'interaction_type' => $question->interaction_type ?? 'normal',
                'type_label' => $this->questionTypeLabel($question),
            ];
        }

        return [
            'correct' => $correct,
            'attempted' => $attempted,
            'total' => $total,
            'score' => $total > 0 ? round(($correct / $total) * $maxScore, 2) : 0,
            'max_score' => $maxScore,
            'checked_at' => now()->toDateTimeString(),
            'items' => $items,
        ];
    }

    public function evaluateSingleQuestion(QuestionBankItem $question, $answer): array
    {
        $answerValue = is_array($answer)
            ? json_encode($answer, JSON_UNESCAPED_UNICODE)
            : trim((string) $answer);
        $expected = $this->expectedAnswerForQuestion($question);
        $isCorrect = $answerValue !== '' && $this->isQuestionAnswerCorrect($question, $answerValue, $expected);

        return [
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
            'answer' => $answerValue,
            'expected' => $expected,
            'answer_text' => $this->formatQuestionAnswer($question, $answerValue),
            'expected_text' => $this->formatQuestionAnswer($question, $expected),
            'type_label' => $this->questionTypeLabel($question),
        ];
    }

    public function expectedAnswerForQuestion(QuestionBankItem $question)
    {
        $interactionType = $question->interaction_type ?? 'normal';

        if ($interactionType === 'ordering') {
            return collect(data_get($question->interaction_data, 'items', []))
                ->keys()
                ->map(fn ($idx) => (string) $idx)
                ->implode(',');
        }

        if ($interactionType === 'matching') {
            $expected = [];
            foreach (data_get($question->interaction_data, 'pairs', []) as $idx => $pair) {
                $expected[(string) $idx] = (string) $idx;
            }

            return $expected;
        }

        if ($question->answer_mode === 'select') {
            $correctOptionId = optional($question->options->firstWhere('is_correct', true))->id;
            return $correctOptionId !== null ? (string) $correctOptionId : '';
        }

        return trim((string) ($question->correct_answer ?? ''));
    }

    public function isQuestionAnswerCorrect(QuestionBankItem $question, string $answerValue, $expected): bool
    {
        $interactionType = $question->interaction_type ?? 'normal';

        if ($interactionType === 'ordering') {
            return $answerValue !== '' && $answerValue === (string) $expected;
        }

        if ($interactionType === 'matching') {
            $decodedAnswer = json_decode($answerValue, true);

            if (!is_array($decodedAnswer) || !is_array($expected)) {
                return false;
            }

            return $this->normalizeMatchingAnswer($decodedAnswer) === $this->normalizeMatchingAnswer($expected);
        }

        if ($question->answer_mode === 'select') {
            return $expected !== '' && (string) $answerValue === (string) $expected;
        }

        return (string) $expected !== ''
            && mb_strtolower(trim($answerValue)) === mb_strtolower(trim((string) $expected));
    }

    public function questionTypeLabel(QuestionBankItem $question): string
    {
        $interactionType = $question->interaction_type ?? 'normal';

        if ($interactionType === 'ordering') {
            return 'Sắp xếp đáp án';
        }

        if ($interactionType === 'matching') {
            return 'Nối đáp án';
        }

        return $question->answer_mode === 'select' ? 'Chọn đáp án' : 'Nhập đáp án';
    }

    public function formatQuestionAnswer(QuestionBankItem $question, $answer): string
    {
        $interactionType = $question->interaction_type ?? 'normal';

        if ($answer === null || $answer === '') {
            return 'Chưa trả lời';
        }

        if ($interactionType === 'ordering') {
            $items = collect(data_get($question->interaction_data, 'items', []))->values();
            $indexes = is_array($answer) ? $answer : explode(',', (string) $answer);

            return collect($indexes)
                ->map(fn ($idx) => $items[(int) $idx] ?? null)
                ->filter()
                ->implode(' -> ') ?: 'Chưa trả lời';
        }

        if ($interactionType === 'matching') {
            $pairs = collect(data_get($question->interaction_data, 'pairs', []))->values();
            $decoded = is_array($answer) ? $answer : json_decode((string) $answer, true);

            if (!is_array($decoded)) {
                return 'Chưa trả lời';
            }

            return collect($decoded)->map(function ($rightIndex, $leftIndex) use ($pairs) {
                $left = data_get($pairs, $leftIndex . '.left', 'Vế trái ' . ((int) $leftIndex + 1));
                $right = data_get($pairs, $rightIndex . '.right');

                if (data_get($pairs, $rightIndex . '.right_type') === 'image') {
                    $right = 'Ảnh ' . ((int) $rightIndex + 1);
                }

                return $left . ' -> ' . ($right ?: 'Chưa chọn');
            })->implode("\n") ?: 'Chưa trả lời';
        }

        if ($question->answer_mode === 'select') {
            $option = $question->options->firstWhere('id', (int) $answer);

            return $option ? $option->option_text : 'Chưa trả lời';
        }

        return trim((string) $answer) ?: 'Chưa trả lời';
    }

    private function normalizeMatchingAnswer(array $answer): array
    {
        $normalized = [];

        foreach ($answer as $key => $value) {
            $normalized[(string) $key] = (string) $value;
        }

        ksort($normalized);

        return $normalized;
    }
}
