<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\StoreQuestionRequest;
use App\Services\TestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    private TestService $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }

    /**
     * GET /api/classes/{classId}/tests
     */
    public function index(int $classId): JsonResponse
    {
        $tests = $this->testService->getTestsByClass($classId);
        return response()->json(['data' => $tests]);
    }

    /**
     * POST /api/tests
     * Tạo bài kiểm tra mới.
     */
    public function store(StoreTestRequest $request): JsonResponse
    {
        $data = array_merge($request->validated(), [
            'created_by' => optional($request->user())->id ?? 1,
        ]);

        $test = $this->testService->createTest($data);
        return response()->json(['data' => $test, 'message' => 'Tạo bài kiểm tra thành công.'], 201);
    }

    /**
     * GET /api/tests/{id}
     * Chi tiết bài kiểm tra kèm câu hỏi.
     */
    public function show(int $id): JsonResponse
    {
        $test = $this->testService->getTest($id);
        return response()->json(['data' => $test]);
    }

    /**
     * PUT /api/tests/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'duration'    => 'nullable|integer|min:1',
            'starts_at'   => 'sometimes|date|after:now',
            'ends_at'     => 'sometimes|date|after:starts_at',
        ]);

        $test = $this->testService->updateTest($id, $data);
        return response()->json(['data' => $test, 'message' => 'Cập nhật thành công.']);
    }

    /**
     * DELETE /api/tests/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->testService->deleteTest($id);
        return response()->json(['message' => 'Xoá bài kiểm tra thành công.']);
    }

    /**
     * PATCH /api/tests/{id}/publish
     * Phát hành bài kiểm tra để học viên có thể làm.
     */
    public function publish(int $id): JsonResponse
    {
        $test = $this->testService->publishTest($id);
        return response()->json(['data' => $test, 'message' => 'Phát hành bài kiểm tra thành công.']);
    }

    /**
     * POST /api/tests/{id}/questions
     * Thêm câu hỏi + đáp án vào bài kiểm tra.
     */
    public function addQuestion(StoreQuestionRequest $request, int $id): JsonResponse
    {
        $question = $this->testService->addQuestion($id, $request->validated());
        return response()->json(['data' => $question, 'message' => 'Thêm câu hỏi thành công.'], 201);
    }

    /**
     * DELETE /api/questions/{questionId}
     */
    public function deleteQuestion(int $questionId): JsonResponse
    {
        $this->testService->deleteQuestion($questionId);
        return response()->json(['message' => 'Xoá câu hỏi thành công.']);
    }
}
