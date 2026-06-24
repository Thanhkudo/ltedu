<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * GET /api/students
     * Danh sách học viên (có tìm kiếm).
     */
    public function index(Request $request): JsonResponse
    {
        $students = $this->studentService->listStudents($request->only('search'));
        return response()->json(['data' => $students]);
    }

    /**
     * POST /api/students
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = $this->studentService->createStudent($request->validated());
        return response()->json(['data' => $student, 'message' => 'Tạo học viên thành công.'], 201);
    }

    /**
     * GET /api/students/{student}
     */
    public function show(int $id): JsonResponse
    {
        $student = $this->studentService->getStudent($id);
        return response()->json(['data' => $student]);
    }

    /**
     * PUT /api/students/{student}
     */
    public function update(UpdateStudentRequest $request, int $id): JsonResponse
    {
        $student = $this->studentService->updateStudent($id, $request->validated());
        return response()->json(['data' => $student, 'message' => 'Cập nhật thành công.']);
    }

    /**
     * DELETE /api/students/{student}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->studentService->deleteStudent($id);
        return response()->json(['message' => 'Xoá học viên thành công.']);
    }
}
