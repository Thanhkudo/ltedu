<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRequest;
use App\Models\Student;
use App\Models\User;
use App\Services\ClassService;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    private ClassService $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    public function index()
    {
        $classes = $this->classService->listClasses();
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    public function store(StoreClassRequest $request)
    {
        $this->classService->createClass($request->validated());
        return redirect()->route('admin.classes.index')
            ->with('success', 'Tạo lớp học thành công!');
    }

    public function show(int $id)
    {
        $class    = $this->classService->getClass($id);
        $students = Student::orderBy('full_name')->get();
        return view('admin.classes.show', compact('class', 'students'));
    }

    public function edit(int $id)
    {
        $class    = $this->classService->getClass($id);
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    public function update(StoreClassRequest $request, int $id)
    {
        $this->classService->updateClass($id, $request->validated());
        return redirect()->route('admin.classes.index')
            ->with('success', 'Cập nhật lớp học thành công!');
    }

    public function destroy(int $id)
    {
        $this->classService->deleteClass($id);
        return redirect()->route('admin.classes.index')
            ->with('success', 'Xoá lớp học thành công!');
    }

    /**
     * POST /admin/classes/{id}/enroll — Thêm học viên vào lớp.
     */
    public function enroll(Request $request, int $id)
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $this->classService->enrollStudents($id, $request->student_ids);
        return redirect()->route('admin.classes.show', $id)
            ->with('success', 'Thêm học viên vào lớp thành công!');
    }

    /**
     * POST /admin/classes/{id}/drop/{studentId}
     */
    public function dropStudent(int $id, int $studentId)
    {
        $this->classService->dropStudent($id, $studentId);
        return redirect()->route('admin.classes.show', $id)
            ->with('success', 'Học viên đã rời lớp.');
    }
}
