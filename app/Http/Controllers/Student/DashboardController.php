<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\TestSession;
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
     * Trang chá»§: cho phÃ©p nháº­p mÃ£ vÃ o há»c, rá»“i hiá»ƒn thá»‹ dashboard.
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

        // BÃ i táº­p Ä‘Ã£ giao theo cÃ¡c lá»›p Ä‘ang há»c (khÃ´ng giá»›i háº¡n háº¡n ná»™p)
        $classIds = $student->classes->pluck('id');

        $assignmentCount = Assignment::query()
            ->whereHas('session', fn ($q) => $q->whereIn('class_id', $classIds))
            ->count();

        $upcomingAssignments = Assignment::with(['session.schoolClass', 'exercise'])
            ->whereHas('session', fn ($q) => $q->whereIn('class_id', $classIds))
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Phiên kiểm tra đang mở
        $activeTests = TestSession::with(['test.questions', 'schoolClass'])
            ->whereIn('class_id', $classIds)
            ->where('status', 'open')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->get();

        return view('student.dashboard', compact('student', 'upcomingAssignments', 'assignmentCount', 'activeTests'));
    }

    /**
     * POST /pick-student
     * Há»c viÃªn nháº­p mÃ£ vÃ o há»c Ä‘á»ƒ lÆ°u session.
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
                'entry_code' => 'MÃ£ vÃ o há»c khÃ´ng Ä‘Ãºng. Vui lÃ²ng kiá»ƒm tra láº¡i.',
            ])->withInput();
        }

        $request->session()->put('student_id', $student->id);

        return redirect()->route('student.home');
    }

    /**
     * POST /logout-student â€” Äáº·t láº¡i há»c viÃªn Ä‘ang xem (demo).
     */
    public function logout(Request $request)
    {
        $request->session()->forget('student_id');
        return redirect('/');
    }
}


