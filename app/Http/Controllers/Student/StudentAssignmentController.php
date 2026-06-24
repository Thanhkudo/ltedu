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
     * Học viên xem chi tiết bài tập + trạng thái đã nộp chưa.
     */
    public function show(Request $request, int $id)
    {
        $studentId = $request->session()->get('student_id');

        if (!$studentId) {
            return redirect('/')->with('error', 'Vui lòng nhập mã vào học trước.');
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
            $generatedQuestions = QuestionBankItem::with(['options'])
                ->whereIn('id', $questionIds)
                ->get()
                ->sortBy(function ($item) use ($questionIds) {
                    return array_search($item->id, $questionIds, true);
                })
                ->values();
        }

        $submittedAnswers = [];
        if ($submission && is_string($submission->content) && strpos($submission->content, '{') === 0) {
            $decoded = json_decode($submission->content, true);
            if (is_array($decoded) && isset($decoded['answers']) && is_array($decoded['answers'])) {
                $submittedAnswers = $decoded['answers'];
            }
        }

        $submissionResult = null;
        if ($submission && $generatedQuestions->isNotEmpty() && !empty($submittedAnswers)) {
            $submissionResult = $this->evaluateQuestionFlowResult($generatedQuestions, $submittedAnswers);
        }

        return view('student.assignments.show', compact('assignment', 'submission', 'student', 'generatedQuestions', 'submittedAnswers', 'submissionResult'));
    }

    /**
     * POST /assignments/{id}/submit
     * Học viên nộp bài tập.
     */
    public function submit(Request $request, int $id)
    {
        $studentId = $request->session()->get('student_id');

        if (!$studentId) {
            return redirect('/')->with('error', 'Vui lòng nhập mã vào học trước.');
        }

        $assignment = Assignment::findOrFail($id);
        $questionIds = data_get($assignment->generation_config, 'question_ids', []);

        if (is_array($questionIds) && !empty($questionIds)) {
            $request->validate([
                'answers' => 'nullable|array',
            ]);

            $answers = [];
            foreach ($questionIds as $questionId) {
                $rawAnswer = $request->input('answers.' . $questionId, '');
                $answerValue = is_array($rawAnswer)
                    ? json_encode($rawAnswer, JSON_UNESCAPED_UNICODE)
                    : trim((string) $rawAnswer);
                $answers[(string) $questionId] = $answerValue;
            }

            $payload = [
                'mode' => 'question_flow',
                'question_ids' => $questionIds,
                'answers' => $answers,
            ];

            $this->assignmentService->submitAssignment($id, $studentId, [
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
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
            ->with('success', 'Nộp bài thành công!');
    }

    private function evaluateQuestionFlowResult(Collection $questions, array $answers): array
    {
        $total = $questions->count();
        $attempted = 0;
        $correct = 0;

        foreach ($questions as $question) {
            $answerValue = trim((string) ($answers[(string) $question->id] ?? ''));
            if ($answerValue === '') {
                continue;
            }

            $attempted++;

            $interactionType = $question->interaction_type ?? 'normal';

            if ($interactionType === 'ordering') {
                $expected = collect(data_get($question->interaction_data, 'items', []))
                    ->keys()
                    ->map(fn ($idx) => (string) $idx)
                    ->implode(',');

                if ($answerValue === $expected) {
                    $correct++;
                }
                continue;
            }

            if ($interactionType === 'matching') {
                $decodedAnswer = json_decode($answerValue, true);
                $pairs = data_get($question->interaction_data, 'pairs', []);
                $expected = [];
                foreach ($pairs as $idx => $pair) {
                    $expected[(string) $idx] = (string) $idx;
                }

                if (is_array($decodedAnswer) && $decodedAnswer == $expected) {
                    $correct++;
                }
                continue;
            }

            if ($question->answer_mode === 'select') {
                $correctOptionId = optional($question->options->firstWhere('is_correct', true))->id;
                if ($correctOptionId !== null && (string) $answerValue === (string) $correctOptionId) {
                    $correct++;
                }
                continue;
            }

            $expected = trim((string) ($question->correct_answer ?? ''));
            if ($expected !== '' && mb_strtolower($answerValue) === mb_strtolower($expected)) {
                $correct++;
            }
        }

        return [
            'correct' => $correct,
            'attempted' => $attempted,
            'total' => $total,
        ];
    }
}
