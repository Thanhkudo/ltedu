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
     * Hoc vien xem chi tiet lop hoc: buoi hoc + bai tap.
     */
    public function show(Request $request, int $id)
    {
        $studentId = $request->session()->get('student_id');

        if (!$studentId) {
            return redirect('/')->with('error', 'Vui long nhap ma vao hoc truoc.');
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
