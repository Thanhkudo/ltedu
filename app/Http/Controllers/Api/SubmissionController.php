<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    private SubmissionService $submissionService;

    public function __construct(SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    /**
     * POST /api/tests/{testId}/start
     * Học viên bắt đầu làm bài kiểm tra.
     * Body: { "student_id": 3 }
     */
    public function startTest(Request $request, int $testId): JsonResponse
    {
        $request->validate(['student_id' => 'required|exists:students,id']);

        // Thực tế: $studentId = Auth::user()->student->id
        $studentId  = $request->student_id;
        $submission = $this->submissionService->startTest($testId, $studentId);

        return response()->json([
            'data'    => $submission->load('test'),
            'message' => 'Bắt đầu làm bài.',
        ]);
    }

    /**
     * PATCH /api/submissions/{submissionId}/save
     * Lưu câu trả lời tạm (không nộp).
     * Body: { "answers": [{ "question_id": 1, "selected_option_id": 2 }, ...] }
     */
    public function saveAnswers(Request $request, int $submissionId): JsonResponse
    {
        $request->validate([
            'answers'                      => 'required|array|min:1',
            'answers.*.question_id'        => 'required|exists:test_questions,id',
            'answers.*.selected_option_id' => 'nullable|exists:question_options,id',
            'answers.*.answer_text'        => 'nullable|string',
        ]);

        $this->submissionService->saveAnswers($submissionId, $request->answers);
        return response()->json(['message' => 'Đã lưu câu trả lời.']);
    }

    /**
     * POST /api/submissions/{submissionId}/submit
     * Học viên nộp bài – hệ thống tự chấm trắc nghiệm.
     * Body: { "student_id": 3 }
     */
    public function submitTest(Request $request, int $submissionId): JsonResponse
    {
        $request->validate(['student_id' => 'required|exists:students,id']);

        $submission = $this->submissionService->submitTest($submissionId, $request->student_id);

        return response()->json([
            'data'    => $submission->load(['answers.question', 'answers.selectedOption']),
            'message' => 'Nộp bài thành công.',
        ]);
    }

    /**
     * PATCH /api/answers/{answerId}/grade
     * Giáo viên chấm điểm câu tự luận.
     * Body: { "score": 4.5, "is_correct": true }
     */
    public function gradeAnswer(Request $request, int $answerId): JsonResponse
    {
        $request->validate([
            'score'      => 'required|numeric|min:0',
            'is_correct' => 'required|boolean',
        ]);

        $answer = $this->submissionService->gradeAnswer(
            $answerId,
            $request->score,
            $request->is_correct
        );

        return response()->json(['data' => $answer, 'message' => 'Chấm điểm thành công.']);
    }

    /**
     * GET /api/tests/{testId}/submissions
     * Giáo viên xem tất cả bài nộp của một bài kiểm tra.
     */
    public function testSubmissions(int $testId): JsonResponse
    {
        $submissions = $this->submissionService->getSubmissionsForTest($testId);
        return response()->json(['data' => $submissions]);
    }
}
