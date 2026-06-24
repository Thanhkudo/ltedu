<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\TestSession;
use App\Models\TestSubmission;
use App\Services\SubmissionService;
use Illuminate\Http\Request;

class StudentTestController extends Controller
{
    private SubmissionService $submissionService;

    public function __construct(SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    public function show(Request $request, int $id)
    {
        $studentId = $request->session()->get('student_id');
        if (!$studentId) {
            return redirect('/')->with('error', 'Vui lòng chọn học viên trước.');
        }

        $student = Student::findOrFail($studentId);
        $testSession = TestSession::with(['test.questions.options', 'schoolClass'])->findOrFail($id);
        $test = $testSession->test;

        $submission = TestSubmission::where('test_session_id', $id)
            ->where('student_id', $studentId)
            ->first();

        if ($submission && $submission->isSubmitted()) {
            return redirect()->route('student.tests.result', $submission->id);
        }

        if (!$submission) {
            abort_unless($testSession->isAvailable(), 422, 'Phiên kiểm tra chưa mở hoặc đã kết thúc.');
            $submission = $this->submissionService->startTestSession($id, $studentId);
        }

        return view('student.tests.show', compact('test', 'testSession', 'student', 'submission'));
    }

    public function submit(Request $request, int $id)
    {
        $studentId = $request->session()->get('student_id');
        if (!$studentId) {
            return redirect('/')->with('error', 'Vui lòng chọn học viên trước.');
        }

        $submission = TestSubmission::where('test_session_id', $id)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $answers = [];
        foreach ($request->except(['_token']) as $key => $value) {
            if (str_starts_with($key, 'answer_')) {
                $questionId = (int) str_replace('answer_', '', $key);
                $answers[] = [
                    'question_id' => $questionId,
                    'selected_option_id' => is_numeric($value) ? (int) $value : null,
                    'answer_text' => is_numeric($value) ? null : $value,
                ];
            }
        }

        if (!empty($answers)) {
            $this->submissionService->saveAnswers($submission->id, $answers);
        }

        $submission = $this->submissionService->submitTest($submission->id, $studentId);

        return redirect()->route('student.tests.result', $submission->id)
            ->with('success', 'Nộp bài thành công!');
    }

    public function result(Request $request, int $submissionId)
    {
        $submission = TestSubmission::with([
            'test.questions.options',
            'testSession.schoolClass',
            'answers.question',
            'answers.selectedOption',
            'student',
        ])->findOrFail($submissionId);

        return view('student.tests.result', compact('submission'));
    }
}
