<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRequest;
use App\Services\ClassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    private ClassService $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    /**
     * GET /api/classes
     */
    public function index(Request $request): JsonResponse
    {
        $classes = $this->classService->listClasses($request->only('teacher_id', 'active_only'));
        return response()->json(['data' => $classes]);
    }

    /**
     * POST /api/classes
     */
    public function store(StoreClassRequest $request): JsonResponse
    {
        $class = $this->classService->createClass($request->validated());
        return response()->json(['data' => $class, 'message' => 'Tạo lớp học thành công.'], 201);
    }

    /**
     * GET /api/classes/{id}
     */
    public function show(int $id): JsonResponse
    {
        $class = $this->classService->getClass($id);
        return response()->json(['data' => $class]);
    }

    /**
     * PUT /api/classes/{id}
     */
    public function update(StoreClassRequest $request, int $id): JsonResponse
    {
        $class = $this->classService->updateClass($id, $request->validated());
        return response()->json(['data' => $class, 'message' => 'Cập nhật lớp học thành công.']);
    }

    /**
     * DELETE /api/classes/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->classService->deleteClass($id);
        return response()->json(['message' => 'Xoá lớp học thành công.']);
    }

    /**
     * POST /api/classes/{id}/enroll
     * Thêm học viên vào lớp.
     * Body: { "student_ids": [1, 2, 3] }
     */
    public function enroll(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $this->classService->enrollStudents($id, $request->student_ids);
        return response()->json(['message' => 'Thêm học viên vào lớp thành công.']);
    }

    /**
     * DELETE /api/classes/{id}/students/{studentId}
     * Cho học viên rời lớp.
     */
    public function dropStudent(int $id, int $studentId): JsonResponse
    {
        $this->classService->dropStudent($id, $studentId);
        return response()->json(['message' => 'Học viên đã rời lớp.']);
    }

    /**
     * GET /api/classes/{id}/students
     * Danh sách học viên trong lớp.
     */
    public function students(int $id): JsonResponse
    {
        // Dùng StudentService để lấy học viên theo lớp
        $students = app(\App\Services\StudentService::class)->getStudentsByClass($id);
        return response()->json(['data' => $students]);
    }
}
