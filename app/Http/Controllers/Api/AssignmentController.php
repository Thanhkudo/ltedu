<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\SubmitAssignmentRequest;
use App\Services\AssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    private AssignmentService $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * POST /api/assignments
     * Giao vien giao bai tap cho mot buoi hoc.
     */
    public function store(StoreAssignmentRequest $request): JsonResponse
    {
        $assignment = $this->assignmentService->assignExercise($request->validated());
        return response()->json(['data' => $assignment->load(['exercise', 'session']), 'message' => 'Giao bai tap thanh cong.'], 201);
    }

    /**
     * GET /api/assignments/{id}
     */
    public function show(int $id): JsonResponse
    {
        $assignment = $this->assignmentService->getAssignment($id);
        return response()->json(['data' => $assignment]);
    }

    /**
     * PUT /api/assignments/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'instructions' => 'nullable|string',
            'due_date'     => 'sometimes|required|date|after:now',
            'max_score'    => 'nullable|integer|min:1',
        ]);

        $assignment = $this->assignmentService->updateAssignment($id, $data);
        return response()->json(['data' => $assignment, 'message' => 'Cap nhat bai tap thanh cong.']);
    }

    /**
     * DELETE /api/assignments/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->assignmentService->deleteAssignment($id);
        return response()->json(['message' => 'Xoa bai tap thanh cong.']);
    }

    /**
     * POST /api/assignments/{id}/submit
     * Hoc vien nop bai tap.
     * Body: { "student_id": 5, "content": "Bai lam cua em...", "file_path": "optional" }
     */
    public function submit(SubmitAssignmentRequest $request, int $id): JsonResponse
    {
        // Trong thuc te: $studentId = Auth::user()->student->id;
        $studentId = $request->input('student_id');

        $submission = $this->assignmentService->submitAssignment(
            $id,
            $studentId,
            $request->validated()
        );

        return response()->json(['data' => $submission, 'message' => 'Nop bai thanh cong.'], 201);
    }

    /**
     * PATCH /api/assignments/{id}/submissions/{submissionId}/grade
     * Giao vien cham diem bai nop.
     */
    public function grade(Request $request, int $id, int $submissionId): JsonResponse
    {
        $request->validate([
            'score'    => 'required|numeric|min:0',
            'feedback' => 'nullable|string',
        ]);

        $submission = $this->assignmentService->gradeSubmission(
            $submissionId,
            $request->score,
            $request->feedback
        );

        return response()->json(['data' => $submission, 'message' => 'Cham diem thanh cong.']);
    }

    /**
     * GET /api/assignments/{id}/submissions
     * Xem tat ca bai nop cua mot assignment.
     */
    public function submissions(int $id): JsonResponse
    {
        $submissions = $this->assignmentService->getSubmissionsForAssignment($id);
        return response()->json(['data' => $submissions]);
    }
}
