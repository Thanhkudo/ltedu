<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\QuestionBankItem;
use App\Models\QuestionCategory;
use App\Models\SchoolTest;
use App\Models\User;

class QuestionGenerationService
{
    public function pickRandomQuestions(array $configRows, int $gradeLevel, string $skillType)
    {
        $picked = collect();

        foreach ($configRows as $row) {
            $quantity = max(0, (int) ($row['quantity'] ?? 0));
            if ($quantity < 1) {
                continue;
            }

            $query = QuestionBankItem::with(['category', 'options'])
                ->where('is_active', true)
                ->whereHas('category', function ($q) use ($gradeLevel, $skillType, $row) {
                    $q->where('grade_level', $gradeLevel)
                        ->where('skill_type', $skillType)
                        ->where('is_active', true);

                    if (!empty($row['category_id'])) {
                        $q->where('id', (int) $row['category_id']);
                    }
                });

            if (!empty($row['answer_mode'])) {
                $query->where('answer_mode', $row['answer_mode']);
            }

            if (!empty($row['context_type'])) {
                $query->where('context_type', $row['context_type']);
            }

            if (!empty($row['interaction_type'])) {
                $query->where('interaction_type', $row['interaction_type']);
            }

            $chunk = $query->inRandomOrder()->limit($quantity)->get();
            $picked = $picked->merge($chunk);
        }

        return $picked->values();
    }

    public function createExerciseFromConfig(array $config, int $creatorId): array
    {
        $gradeLevel = (int) $config['grade_level'];
        $skillType = (string) $config['skill_type'];
        $rows = $config['question_configs'] ?? [];

        $questions = $this->pickRandomQuestions($rows, $gradeLevel, $skillType);

        abort_if($questions->isEmpty(), 422, 'Không tìm thấy câu hỏi phù hợp với cấu hình đã chọn.');

        $content = [];
        foreach ($questions as $idx => $item) {
            $line = 'Câu ' . ($idx + 1) . ': ' . $item->question_text;

            if ($item->context_type === 'reading' && $item->passage) {
                $line = "[ĐỌC HIỂU]\nĐoạn văn: " . $item->passage . "\n" . $line;
            }

            if ($item->context_type === 'listening' && $item->audio_url) {
                $line = "[NGHE]\nAudio: " . $item->audio_url . "\n" . $line;
            }

            if (($item->interaction_type ?? 'normal') === 'ordering') {
                $line .= "\n  Sap xep theo dung thu tu.";
            } elseif (($item->interaction_type ?? 'normal') === 'matching') {
                $line .= "\n  Noi cac cap dap an tuong ung.";
            } elseif ($item->answer_mode === 'select') {
                foreach ($item->options as $optIdx => $opt) {
                    $line .= "\n  " . chr(65 + $optIdx) . '. ' . $opt->option_text;
                }
            } else {
                $line .= "\n  Trả lời ngắn:";
            }

            $content[] = $line;
        }

        $creator = User::find($creatorId);
        $title = 'Bài tập random - Lớp ' . $gradeLevel . ' - ' . ucfirst($skillType) . ' - ' . now()->format('d/m H:i');

        $exercise = Exercise::create([
            'title' => $title,
            'description' => 'Sinh tự động từ kho câu hỏi theo cấu hình.',
            'content' => implode("\n\n", $content),
            'type' => $this->mapSkillToExerciseType($skillType),
            'difficulty' => 'medium',
            'created_by' => $creator ? $creator->id : User::query()->value('id'),
        ]);

        return [
            'exercise' => $exercise,
            'question_count' => $questions->count(),
            'question_ids' => $questions->pluck('id')->values()->all(),
        ];
    }

    public function addQuestionsToTestFromConfig(int $testId, array $config): int
    {
        $test = SchoolTest::with('questions')->findOrFail($testId);

        $gradeLevel = (int) $config['grade_level'];
        $skillType = (string) $config['skill_type'];
        $rows = $config['question_configs'] ?? [];

        $questions = $this->pickRandomQuestions($rows, $gradeLevel, $skillType);
        abort_if($questions->isEmpty(), 422, 'Không tìm thấy câu hỏi phù hợp để thêm vào bài kiểm tra.');

        $order = ((int) $test->questions()->max('order_index')) + 1;

        foreach ($questions as $item) {
            $questionText = $item->question_text;
            if ($item->context_type === 'reading' && $item->passage) {
                $questionText = "[READING]\n" . $item->passage . "\n\n" . $questionText;
            }
            if ($item->context_type === 'listening' && $item->audio_url) {
                $questionText = "[LISTENING]\nAudio: " . $item->audio_url . "\n\n" . $questionText;
            }

            $testQuestion = $test->questions()->create([
                'question_text' => $questionText,
                'question_type' => $item->answer_mode === 'select' ? 'multiple_choice' : 'short_answer',
                'score' => 1,
                'order_index' => $order++,
            ]);

            if ($item->answer_mode === 'select' && $item->options->isNotEmpty()) {
                foreach ($item->options as $optIdx => $option) {
                    $testQuestion->options()->create([
                        'option_text' => $option->option_text,
                        'is_correct' => (bool) $option->is_correct,
                        'order_index' => $optIdx + 1,
                    ]);
                }
            }
        }

        return $questions->count();
    }

    public function categoryOptions()
    {
        return QuestionCategory::where('is_active', true)
            ->orderBy('grade_level')
            ->orderBy('skill_type')
            ->orderBy('name')
            ->get();
    }

    private function mapSkillToExerciseType(string $skillType): string
    {
        $allowed = ['reading', 'writing', 'listening', 'speaking', 'grammar', 'vocabulary'];
        return in_array($skillType, $allowed) ? $skillType : 'writing';
    }
}
