<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Services\QuestionGenerationService;
use Illuminate\Console\Command;

class MigrateAssignmentQuestionIds extends Command
{
    protected $signature = 'assignments:migrate-question-ids
                            {--dry-run : Show what would be updated without saving}';

    protected $description = 'Populate question_ids into generation_config for old random assignments that are missing it';

    private QuestionGenerationService $qgs;

    public function __construct(QuestionGenerationService $qgs)
    {
        parent::__construct();
        $this->qgs = $qgs;
    }

    public function handle(): int
    {
        $isDry = $this->option('dry-run');

        // Find all random assignments whose generation_config lacks question_ids
        $assignments = Assignment::where('generation_mode', 'random')
            ->whereNotNull('generation_config')
            ->get()
            ->filter(fn ($a) => empty($a->generation_config['question_ids']));

        if ($assignments->isEmpty()) {
            $this->info('Không có assignment nào cần migrate.');
            return 0;
        }

        $this->info("Tìm thấy {$assignments->count()} assignment cần migrate." . ($isDry ? ' (dry-run)' : ''));

        $ok = 0;
        $fail = 0;

        foreach ($assignments as $assignment) {
            $config = $assignment->generation_config;
            $gradeLevel = $config['grade_level'] ?? null;
            $skillType  = $config['skill_type']  ?? null;
            $rows       = $config['question_configs'] ?? [];

            if (!$gradeLevel || !$skillType || empty($rows)) {
                $this->warn("  [SKIP] Assignment #{$assignment->id} — thiếu grade_level/skill_type/question_configs");
                $fail++;
                continue;
            }

            try {
                $questions = $this->qgs->pickRandomQuestions($rows, (int) $gradeLevel, (string) $skillType);

                if ($questions->isEmpty()) {
                    $this->warn("  [SKIP] Assignment #{$assignment->id} — không tìm thấy câu hỏi phù hợp");
                    $fail++;
                    continue;
                }

                $ids = $questions->pluck('id')->values()->all();

                $this->line("  [OK]   Assignment #{$assignment->id} — " . count($ids) . " câu hỏi: [" . implode(', ', $ids) . "]");

                if (!$isDry) {
                    $newConfig = array_merge($config, ['question_ids' => $ids]);
                    $assignment->update([
                        'generation_config'         => $newConfig,
                        'generated_question_count'  => count($ids),
                    ]);
                }

                $ok++;
            } catch (\Throwable $e) {
                $this->error("  [FAIL] Assignment #{$assignment->id} — " . $e->getMessage());
                $fail++;
            }
        }

        $this->newLine();
        $this->info("Hoàn thành: {$ok} thành công, {$fail} bỏ qua/lỗi.");

        return 0;
    }
}
