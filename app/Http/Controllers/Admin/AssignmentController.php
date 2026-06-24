<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Models\ClassSession;
use App\Services\AssignmentService;
use App\Services\QuestionGenerationService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    private AssignmentService $assignmentService;
    private QuestionGenerationService $questionGenerationService;

    public function __construct(AssignmentService $assignmentService, QuestionGenerationService $questionGenerationService)
    {
        $this->assignmentService = $assignmentService;
        $this->questionGenerationService = $questionGenerationService;
    }

    /**
     * GET /admin/assignments/create?session_id=X
     * Form giao bài tập cho một buổi học.
     */
    public function create(Request $request)
    {
        $sessionId = $request->query('session_id');
        $session   = $sessionId ? ClassSession::with('schoolClass')->findOrFail($sessionId) : null;
        $sessions  = ClassSession::with('schoolClass')->orderBy('session_date', 'desc')->get();
        $categories = $this->questionGenerationService->categoryOptions();

        return view('admin.assignments.create', compact('session', 'sessions', 'categories'));
    }

    public function store(StoreAssignmentRequest $request)
    {
        $data = $request->validated();

        $generated = $this->questionGenerationService->createExerciseFromConfig([
            'grade_level' => $data['grade_level'],
            'skill_type' => $data['skill_type'],
            'question_configs' => $data['question_configs'] ?? [],
        ], auth()->id());

        $data['exercise_id'] = $generated['exercise']->id;
        $data['generation_mode'] = 'random';
        $data['generation_config'] = [
            'grade_level' => $data['grade_level'],
            'skill_type' => $data['skill_type'],
            'question_configs' => $data['question_configs'] ?? [],
            'question_ids' => $generated['question_ids'] ?? [],
        ];
        $data['generated_question_count'] = $generated['question_count'];

        unset(
            $data['grade_level'],
            $data['skill_type'],
            $data['question_configs']
        );

        $assignment = $this->assignmentService->assignExercise($data);
        $classId    = $assignment->session->class_id;

        return redirect()->route('admin.classes.show', $classId)
            ->with('success', 'Giao bài tập thành công!');
    }

    public function destroy(int $id)
    {
        $assignment = $this->assignmentService->getAssignment($id);
        $classId    = $assignment->session->class_id;
        $this->assignmentService->deleteAssignment($id);

        return redirect()->route('admin.classes.show', $classId)
            ->with('success', 'Xoá bài tập thành công!');
    }

    /**
     * GET /admin/assignments/{id}/submissions
     * Xem bài nộp + chấm điểm.
     */
    public function submissions(int $id)
    {
        $assignment  = $this->assignmentService->getAssignment($id);
        $submissions = $this->assignmentService->getSubmissionsForAssignment($id);
        return view('admin.assignments.submissions', compact('assignment', 'submissions'));
    }

    /**
     * POST /admin/assignments/{id}/submissions/{subId}/grade
     */
    public function grade(Request $request, int $id, int $subId)
    {
        $request->validate([
            'score'    => 'required|numeric|min:0',
            'feedback' => 'nullable|string',
        ]);

        $this->assignmentService->gradeSubmission($subId, $request->score, $request->feedback);
        return redirect()->route('admin.assignments.submissions', $id)
            ->with('success', 'Chấm điểm thành công!');
    }
}
