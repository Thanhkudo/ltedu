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
        return response()->json(['data' => $class, 'message' => 'Tao lop hoc thanh cong.'], 201);
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
        return response()->json(['data' => $class, 'message' => 'Cap nhat lop hoc thanh cong.']);
    }

    /**
     * DELETE /api/classes/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->classService->deleteClass($id);
        return response()->json(['message' => 'Xoa lop hoc thanh cong.']);
    }

    /**
     * POST /api/classes/{id}/enroll
     * Them hoc vien vao lop.
     * Body: { "student_ids": [1, 2, 3] }
     */
    public function enroll(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $this->classService->enrollStudents($id, $request->student_ids);
        return response()->json(['message' => 'Them hoc vien vao lop thanh cong.']);
    }

    /**
     * DELETE /api/classes/{id}/students/{studentId}
     * Cho hoc vien roi lop.
     */
    public function dropStudent(int $id, int $studentId): JsonResponse
    {
        $this->classService->dropStudent($id, $studentId);
        return response()->json(['message' => 'Hoc vien da roi lop.']);
    }

    /**
     * GET /api/classes/{id}/students
     * Danh sach hoc vien trong lop.
     */
    public function students(int $id): JsonResponse
    {
        // Dung StudentService de lay hoc vien theo lop
        $students = app(\App\Services\StudentService::class)->getStudentsByClass($id);
        return response()->json(['data' => $students]);
    }
}
