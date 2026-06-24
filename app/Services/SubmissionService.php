<?php

namespace App\Services;

use App\Models\SchoolTest;
use App\Models\TestAnswer;
use App\Models\TestSession;
use App\Models\TestSubmission;
use Illuminate\Support\Facades\DB;

class SubmissionService
{
    public function startTest(int $testId, int $studentId): TestSubmission
    {
        $test = SchoolTest::findOrFail($testId);

        abort_unless($test->isAvailable(), 422, 'Bài kiểm tra chưa mở hoặc đã kết thúc.');

        $existing = TestSubmission::where('test_id', $testId)
            ->whereNull('test_session_id')
            ->where('student_id', $studentId)
            ->first();

        if ($existing) {
            abort_if($existing->isSubmitted(), 422, 'Bạn đã nộp bài kiểm tra này rồi.');
            return $existing;
        }

        return TestSubmission::create([
            'test_id' => $testId,
            'student_id' => $studentId,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function startTestSession(int $sessionId, int $studentId): TestSubmission
    {
        $session = TestSession::with('test')->findOrFail($sessionId);

        abort_unless($session->isAvailable(), 422, 'Phiên kiểm tra chưa mở hoặc đã kết thúc.');

        $existing = TestSubmission::where('test_session_id', $sessionId)
            ->where('student_id', $studentId)
            ->first();

        if ($existing) {
            abort_if($existing->isSubmitted(), 422, 'Bạn đã nộp bài kiểm tra này rồi.');
            return $existing;
        }

        return TestSubmission::create([
            'test_id' => $session->test_id,
            'test_session_id' => $session->id,
            'student_id' => $studentId,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function saveAnswers(int $submissionId, array $answers): void
    {
        $submission = TestSubmission::findOrFail($submissionId);
        abort_if($submission->isSubmitted(), 422, 'Bài đã nộp, không thể chỉnh sửa.');

        DB::transaction(function () use ($submission, $answers) {
            foreach ($answers as $answerData) {
                TestAnswer::updateOrCreate(
                    [
                        'test_submission_id' => $submission->id,
                        'question_id' => $answerData['question_id'],
                    ],
                    [
                        'selected_option_id' => $answerData['selected_option_id'] ?? null,
                        'answer_text' => $answerData['answer_text'] ?? null,
                    ]
                );
            }
        });
    }

    public function submitTest(int $submissionId, int $studentId): TestSubmission
    {
        $submission = TestSubmission::with(['test.questions.options', 'answers'])
            ->findOrFail($submissionId);

        abort_if($submission->student_id !== $studentId, 403, 'Không có quyền nộp bài này.');
        abort_if($submission->isSubmitted(), 422, 'Bài đã được nộp rồi.');

        DB::transaction(function () use ($submission) {
            $totalScore = 0;

            foreach ($submission->answers as $answer) {
                $question = $submission->test->questions->find($answer->question_id);

                if (!$question) {
                    continue;
                }

                if (in_array($question->question_type, ['multiple_choice', 'true_false'], true)) {
                    $option = $question->options->find($answer->selected_option_id);
                    $isCorrect = $answer->selected_option_id && $option && $option->is_correct;

                    $score = $isCorrect ? $question->score : 0;
                    $answer->update(['is_correct' => $isCorrect, 'score' => $score]);
                    $totalScore += $score;
                }
            }

            $submission->update([
                'total_score' => $totalScore,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        });

        return $submission->fresh();
    }

    public function gradeAnswer(int $answerId, float $score, bool $isCorrect): TestAnswer
    {
        $answer = TestAnswer::with('submission')->findOrFail($answerId);
        $answer->update(['score' => $score, 'is_correct' => $isCorrect]);

        $submission = $answer->submission;
        $totalScore = $submission->answers()->sum('score');
        $submission->update(['total_score' => $totalScore, 'status' => 'graded']);

        return $answer;
    }

    public function getSubmissionsForTest(int $testId)
    {
        return TestSubmission::where('test_id', $testId)
            ->with(['student', 'answers', 'testSession.schoolClass'])
            ->latest('submitted_at')
            ->get();
    }

    public function getSubmissionsForSession(int $sessionId)
    {
        return TestSubmission::where('test_session_id', $sessionId)
            ->with(['student', 'answers'])
            ->latest('submitted_at')
            ->get();
    }
}
