<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $students = $this->studentService->listStudents($request->only('search'));
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(StoreStudentRequest $request)
    {
        $this->studentService->createStudent($request->validated());
        return redirect()->route('admin.students.index')
            ->with('success', 'Tao hoc vien thanh cong!');
    }

    public function edit(int $id)
    {
        $student = $this->studentService->getStudent($id);
        return view('admin.students.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, int $id)
    {
        $this->studentService->updateStudent($id, $request->validated());
        return redirect()->route('admin.students.index')
            ->with('success', 'Cap nhat hoc vien thanh cong!');
    }

    public function destroy(int $id)
    {
        $this->studentService->deleteStudent($id);
        return redirect()->route('admin.students.index')
            ->with('success', 'Xoa hoc vien thanh cong!');
    }
}
