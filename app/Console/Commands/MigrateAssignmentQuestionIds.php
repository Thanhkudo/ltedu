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
            $this->info('Khong co assignment nao can migrate.');
            return 0;
        }

        $this->info("Tim thay {$assignments->count()} assignment can migrate." . ($isDry ? ' (dry-run)' : ''));

        $ok = 0;
        $fail = 0;

        foreach ($assignments as $assignment) {
            $config = $assignment->generation_config;
            $gradeLevel = $config['grade_level'] ?? null;
            $rows       = $config['question_configs'] ?? [];

            if (!$gradeLevel || empty($rows)) {
                $this->warn("  [SKIP] Assignment #{$assignment->id} - thieu grade_level/question_configs");
                $fail++;
                continue;
            }

            try {
                $questions = $this->qgs->pickRandomQuestions($rows, (int) $gradeLevel);

                if ($questions->isEmpty()) {
                    $this->warn("  [SKIP] Assignment #{$assignment->id} - khong tim thay cau hoi phu hop");
                    $fail++;
                    continue;
                }

                $ids = $questions->pluck('id')->values()->all();

                $this->line("  [OK]   Assignment #{$assignment->id} - " . count($ids) . " cau hoi: [" . implode(', ', $ids) . "]");

                if (!$isDry) {
                    $newConfig = array_merge($config, ['question_ids' => $ids]);
                    $assignment->update([
                        'generation_config'         => $newConfig,
                        'generated_question_count'  => count($ids),
                    ]);
                }

                $ok++;
            } catch (\Throwable $e) {
                $this->error("  [FAIL] Assignment #{$assignment->id} - " . $e->getMessage());
                $fail++;
            }
        }

        $this->newLine();
        $this->info("Hoan thanh: {$ok} thanh cong, {$fail} bo qua/loi.");

        return 0;
    }
}
