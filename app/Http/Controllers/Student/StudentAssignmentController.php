<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\QuestionBankItem;
use App\Models\Student;
use App\Services\AssignmentService;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class StudentAssignmentController extends Controller
{
    private AssignmentService $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * GET /assignments/{id}
     * Hoc vien xem chi tiet bai tap + trang thai da nop chua.
     */
    public function show(Request $request, int $id)
    {
        return $this->renderAssignment($request, $id, false);
    }

    public function practice(Request $request, int $id)
    {
        return $this->renderAssignment($request, $id, true);
    }

    private function renderAssignment(Request $request, int $id, bool $practiceMode)
    {
        $studentId = $request->session()->get('student_id');

        if (!$studentId) {
            return redirect('/')->with('error', 'Vui long nhap ma vao hoc truoc.');
        }

        $student    = Student::findOrFail($studentId);
        $assignment = Assignment::with(['exercise', 'session.schoolClass'])->findOrFail($id);
        $submission = AssignmentSubmission::where('assignment_id', $id)
            ->where('student_id', $studentId)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->first();

        $generatedQuestions = collect();
        $questionIds = data_get($assignment->generation_config, 'question_ids', []);

        if (is_array($questionIds) && !empty($questionIds)) {
            $generatedQuestions = QuestionBankItem::with(['group', 'options'])
                ->whereIn('id', $questionIds)
                ->get()
                ->sortBy(function ($item) use ($questionIds) {
                    return array_search($item->id, $questionIds, true);
                })
                ->values();
        }

        $submittedAnswers = data_get($submission ? $submission->json_params : null, 'answers', []);
        if (!is_array($submittedAnswers)) {
            $submittedAnswers = [];
        }

        if (empty($submittedAnswers) && $submission && is_string($submission->content) && strpos($submission->content, '{') === 0) {
            $decoded = json_decode($submission->content, true);
            if (is_array($decoded) && isset($decoded['answers']) && is_array($decoded['answers'])) {
                $submittedAnswers = $decoded['answers'];
            }
        }

        if ($practiceMode) {
            $submittedAnswers = [];
        }

        $submissionResult = data_get($submission ? $submission->json_params : null, 'result');
        if (!$submissionResult && $submission && $generatedQuestions->isNotEmpty() && !empty($submittedAnswers)) {
            $submissionResult = $this->evaluateQuestionFlowResult($generatedQuestions, $submittedAnswers, (float) $assignment->max_score);
        }

        return view('student.assignments.show', compact('assignment', 'submission', 'student', 'generatedQuestions', 'submittedAnswers', 'submissionResult', 'practiceMode'));
    }

    /**
     * POST /assignments/{id}/submit
     * Hoc vien nop bai tap.
     */
    public function submit(Request $request, int $id)
    {
        $studentId = $request->session()->get('student_id');

        if (!$studentId) {
            return redirect('/')->with('error', 'Vui long nhap ma vao hoc truoc.');
        }

        $assignment = Assignment::findOrFail($id);
        $questionIds = data_get($assignment->generation_config, 'question_ids', []);

        if (is_array($questionIds) && !empty($questionIds)) {
            $request->validate([
                'answers' => 'nullable|array',
            ]);

            $generatedQuestions = QuestionBankItem::with(['group', 'options'])
                ->whereIn('id', $questionIds)
                ->get()
                ->sortBy(function ($item) use ($questionIds) {
                    return array_search($item->id, $questionIds, true);
                })
                ->values();

            $answers = [];
            foreach ($questionIds as $questionId) {
                $rawAnswer = $request->input('answers.' . $questionId, '');
                $answerValue = is_array($rawAnswer)
                    ? json_encode($rawAnswer, JSON_UNESCAPED_UNICODE)
                    : trim((string) $rawAnswer);
                $answers[(string) $questionId] = $answerValue;
            }

            $result = $this->evaluateQuestionFlowDetails($generatedQuestions, $answers, (float) $assignment->max_score);
            $payload = [
                'mode' => 'question_flow',
                'question_ids' => $questionIds,
                'answers' => $answers,
                'result' => $result,
            ];

            $this->assignmentService->submitAssignment($id, $studentId, [
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'json_params' => $payload,
                'score' => $result['score'],
            ]);
        } else {
            $request->validate([
                'content' => 'nullable|string',
            ]);

            $this->assignmentService->submitAssignment($id, $studentId, [
                'content' => (string) $request->input('content', ''),
            ]);
        }

        return redirect()->route('student.assignments.show', $id)
            ->with('success', 'Nop bai thanh cong!');
    }

    private function evaluateQuestionFlowResult(Collection $questions, array $answers, float $maxScore = 0): array
    {
        $details = $this->evaluateQuestionFlowDetails($questions, $answers, $maxScore);

        return [
            'correct' => $details['correct'],
            'attempted' => $details['attempted'],
            'total' => $details['total'],
            'score' => $details['score'] ?? 0,
            'max_score' => $details['max_score'] ?? 0,
            'items' => $details['items'] ?? [],
        ];
    }

    private function evaluateQuestionFlowDetails(Collection $questions, array $answers, float $maxScore = 0): array
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

        $score = $total > 0 ? round(($correct / $total) * $maxScore, 2) : 0;

        return [
            'correct' => $correct,
            'attempted' => $attempted,
            'total' => $total,
            'score' => $score,
            'max_score' => $maxScore,
            'checked_at' => now()->toDateTimeString(),
            'items' => $items,
        ];
    }

    private function expectedAnswerForQuestion(QuestionBankItem $question)
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

    private function isQuestionAnswerCorrect(QuestionBankItem $question, string $answerValue, $expected): bool
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

    private function normalizeMatchingAnswer(array $answer): array
    {
        $normalized = [];

        foreach ($answer as $key => $value) {
            $normalized[(string) $key] = (string) $value;
        }

        ksort($normalized);

        return $normalized;
    }

    private function questionTypeLabel(QuestionBankItem $question): string
    {
        $interactionType = $question->interaction_type ?? 'normal';

        if ($interactionType === 'ordering') {
            return 'Sap xep dap an';
        }

        if ($interactionType === 'matching') {
            return 'Noi dap an';
        }

        return $question->answer_mode === 'select' ? 'Chon dap an' : 'Nhap dap an';
    }

    private function formatQuestionAnswer(QuestionBankItem $question, $answer): string
    {
        $interactionType = $question->interaction_type ?? 'normal';

        if ($answer === null || $answer === '') {
            return 'Chua tra loi';
        }

        if ($interactionType === 'ordering') {
            $items = collect(data_get($question->interaction_data, 'items', []))->values();
            $indexes = is_array($answer) ? $answer : explode(',', (string) $answer);

            return collect($indexes)
                ->map(fn ($idx) => $items[(int) $idx] ?? null)
                ->filter()
                ->implode(' -> ') ?: 'Chua tra loi';
        }

        if ($interactionType === 'matching') {
            $pairs = collect(data_get($question->interaction_data, 'pairs', []))->values();
            $decoded = is_array($answer) ? $answer : json_decode((string) $answer, true);

            if (!is_array($decoded)) {
                return 'Chua tra loi';
            }

            return collect($decoded)->map(function ($rightIndex, $leftIndex) use ($pairs) {
                $left = data_get($pairs, $leftIndex . '.left', 'Ve trai ' . ((int) $leftIndex + 1));
                $right = data_get($pairs, $rightIndex . '.right');

                if (data_get($pairs, $rightIndex . '.right_type') === 'image') {
                    $right = 'Anh ' . ((int) $rightIndex + 1);
                }

                return $left . ' -> ' . ($right ?: 'Chua chon');
            })->implode("\n") ?: 'Chua tra loi';
        }

        if ($question->answer_mode === 'select') {
            $option = $question->options->firstWhere('id', (int) $answer);

            return $option ? $option->option_text : 'Chua tra loi';
        }

        return trim((string) $answer) ?: 'Chua tra loi';
    }
}
