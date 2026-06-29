<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\QuestionBankItem;
use App\Models\QuestionCategory;
use App\Models\User;

class QuestionGenerationService
{
    public function pickRandomQuestions(array $configRows, int $gradeLevel)
    {
        $picked = collect();

        foreach ($configRows as $row) {
            $quantity = max(0, (int) ($row['quantity'] ?? 0));
            if ($quantity < 1) {
                continue;
            }

            $query = QuestionBankItem::with(['category', 'group', 'options'])
                ->where('is_active', true)
                ->whereHas('category', function ($q) use ($gradeLevel, $row) {
                    $q->where('grade_level', $gradeLevel)
                        ->where('is_active', true);

                    if (!empty($row['category_id'])) {
                        $q->where('id', (int) $row['category_id']);
                    }
                });

            $questionType = (string) ($row['question_type'] ?? '');

            if (in_array($questionType, ['select', 'input'], true)) {
                $query->where('answer_mode', $questionType)
                    ->where('interaction_type', 'normal');
            } elseif (in_array($questionType, ['ordering', 'matching'], true)) {
                $query->where('interaction_type', $questionType);
            } elseif (!empty($row['answer_mode'])) {
                $query->where('answer_mode', $row['answer_mode']);
            }

            if (!empty($row['context_type'])) {
                $query->where('context_type', $row['context_type']);
            }

            if ($questionType === '' && !empty($row['interaction_type'])) {
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
        $rows = $config['question_configs'] ?? [];
        $skillType = $this->resolveSkillTypeFromConfig($rows);

        $questions = $this->pickRandomQuestions($rows, $gradeLevel);

        abort_if($questions->isEmpty(), 422, 'Khong tim thay cau hoi phu hop voi cau hinh da chon.');

        $content = [];
        foreach ($questions as $idx => $item) {
            $line = 'Cau ' . ($idx + 1) . ': ' . $item->question_text;
            $contextType = optional($item->group)->type ?: $item->context_type;
            $passage = optional($item->group)->passage ?: $item->passage;
            $audioUrl = optional($item->group)->audio_url ?: $item->audio_url;

            if ($contextType === 'reading' && $passage) {
                $line = "[DOC HIEU]\nDoan van: " . $passage . "\n" . $line;
            }

            if ($contextType === 'listening' && $audioUrl) {
                $line = "[NGHE]\nAudio: " . $audioUrl . "\n" . $line;
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
                $line .= "\n  Tra loi ngan:";
            }

            $content[] = $line;
        }

        $creator = User::find($creatorId);
        $title = 'Bai tap random - Lop ' . $gradeLevel . ' - ' . ($skillType ? ucfirst($skillType) : 'Tong hop') . ' - ' . now()->format('d/m H:i');

        $exercise = Exercise::create([
            'title' => $title,
            'description' => 'Sinh tu dong tu kho cau hoi theo cau hinh.',
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

    public function categoryOptions()
    {
        return QuestionCategory::where('is_active', true)
            ->orderBy('grade_level')
            ->orderBy('skill_type')
            ->orderBy('name')
            ->get();
    }

    private function resolveSkillTypeFromConfig(array $rows): ?string
    {
        $categoryIds = collect($rows)
            ->pluck('category_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($categoryIds)) {
            return null;
        }

        $skillTypes = QuestionCategory::whereIn('id', $categoryIds)
            ->where('is_active', true)
            ->distinct()
            ->pluck('skill_type');

        return $skillTypes->count() === 1 ? $skillTypes->first() : null;
    }

    private function mapSkillToExerciseType(?string $skillType): string
    {
        $allowed = ['reading', 'writing', 'listening', 'speaking', 'grammar', 'vocabulary'];
        return $skillType && in_array($skillType, $allowed, true) ? $skillType : 'writing';
    }
}
