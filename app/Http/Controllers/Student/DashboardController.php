<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Assignment;
use App\Services\ClassService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private ClassService $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    /**
     * GET /
     * Trang chu: cho phep nhap ma vao hoc, roi hien thi dashboard.
     */
    public function index(Request $request)
    {
        $studentId = $request->session()->get('student_id');

        if (!$studentId) {
            return view('student.pick');
        }

        $student = Student::with([
            'classes.teacher',
            'classes.sessions',
        ])->findOrFail($studentId);

        // Bai tap da giao theo cac lop dang hoc (khong gioi han han nop)
        $classIds = $student->classes->pluck('id');

        $assignmentCount = Assignment::query()
            ->whereHas('session', fn ($q) => $q->whereIn('class_id', $classIds))
            ->count();

        $completedAssignmentCount = Assignment::query()
            ->whereHas('session', fn ($q) => $q->whereIn('class_id', $classIds))
            ->whereHas('submissions', fn ($q) => $q->where('student_id', $studentId))
            ->count();

        $upcomingAssignments = Assignment::with([
                'session.schoolClass',
                'exercise',
                'submissions' => fn ($q) => $q->where('student_id', $studentId)->latest('submitted_at'),
            ])
            ->whereHas('session', fn ($q) => $q->whereIn('class_id', $classIds))
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('student.dashboard', compact('student', 'upcomingAssignments', 'assignmentCount', 'completedAssignmentCount'));
    }

    /**
     * POST /pick-student
     * Hoc vien nhap ma vao hoc de luu session.
     */
    public function pickStudent(Request $request)
    {
        $data = $request->validate([
            'entry_code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim((string) $data['entry_code']));

        $student = Student::whereRaw('UPPER(student_code) = ?', [$code])->first();

        if (!$student) {
            return back()->withErrors([
                'entry_code' => 'Ma vao hoc khong dung. Vui long kiem tra lai.',
            ])->withInput();
        }

        $request->session()->put('student_id', $student->id);

        return redirect()->route('student.home');
    }

    /**
     * POST /logout-student a" Dat lai hoc vien dang xem (demo).
     */
    public function logout(Request $request)
    {
        $request->session()->forget('student_id');
        return redirect('/');
    }
}


