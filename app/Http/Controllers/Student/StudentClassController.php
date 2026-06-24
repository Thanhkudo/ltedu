<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentClassController extends Controller
{
    /**
     * GET /classes/{id}
     * Học viên xem chi tiết lớp học: buổi học + bài tập.
     */
    public function show(Request $request, int $id)
    {
        $studentId = $request->session()->get('student_id');

        if (!$studentId) {
            return redirect('/')->with('error', 'Vui lòng nhập mã vào học trước.');
        }

        $student = Student::findOrFail($studentId);

        $class = SchoolClass::with([
            'teacher',
            'sessions.assignments.exercise',
            'sessions.assignments' => function ($q) use ($studentId) {
                $q->with(['submissions' => fn ($s) => $s->where('student_id', $studentId)]);
            },
        ])->findOrFail($id);

        return view('student.classes.show', compact('class', 'student'));
    }
}
