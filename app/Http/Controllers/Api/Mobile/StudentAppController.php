<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSession;
use App\Models\MobileApiToken;
use App\Models\QuestionBankItem;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\AssignmentService;
use App\Services\QuestionAnswerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentAppController extends Controller
{
    private AssignmentService $assignmentService;
    private QuestionAnswerService $questionAnswerService;

    public function __construct(AssignmentService $assignmentService, QuestionAnswerService $questionAnswerService)
    {
        $this->assignmentService = $assignmentService;
        $this->questionAnswerService = $questionAnswerService;
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'student_code' => 'required|string|max:50',
            'device_name' => 'nullable|string|max:100',
        ]);

        $student = Student::whereRaw('UPPER(student_code) = ?', [strtoupper(trim($data['student_code']))])->first();

        if (!$student) {
            return response()->json(['message' => 'Mã học viên không đúng.'], 422);
        }

        $plainToken = Str::random(80);
        $token = MobileApiToken::create([
            'student_id' => $student->id,
            'name' => $data['device_name'] ?? 'mobile',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addDays(90),
        ]);

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $plainToken,
            'expires_at' => optional($token->expires_at)->toDateTimeString(),
            'student' => $this->studentPayload($student),
        ]);
    }

    public function logout(Request $request)
    {
        optional($request->attributes->get('mobile_token'))->delete();

        return response()->json(['message' => 'Đã đăng xuất.']);
    }

    public function me(Request $request)
    {
        return response()->json([
            'student' => $this->studentPayload($this->student($request)->load('classes.teacher')),
        ]);
    }

    public function dashboard(Request $request)
    {
        $student = $this->student($request)->load('classes.teacher');
        $classIds = $student->classes->pluck('id');

        $assignmentCount = Assignment::whereHas('session', fn ($query) => $query->whereIn('class_id', $classIds)->where('status', '!=', 'cancelled'))->count();
        $completedAssignmentCount = Assignment::whereHas('session', fn ($query) => $query->whereIn('class_id', $classIds)->where('status', '!=', 'cancelled'))
            ->whereHas('submissions', fn ($query) => $query->where('student_id', $student->id))
            ->count();

        $upcomingAssignments = Assignment::with(['session.schoolClass', 'exercise'])
            ->whereHas('session', fn ($query) => $query->whereIn('class_id', $classIds)->where('status', '!=', 'cancelled'))
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn ($assignment) => $this->assignmentPayload($assignment, $student->id));

        return response()->json([
            'student' => $this->studentPayload($student),
            'stats' => [
                'assignment_count' => $assignmentCount,
                'completed_assignment_count' => $completedAssignmentCount,
            ],
            'upcoming_assignments' => $upcomingAssignments,
        ]);
    }

    public function classes(Request $request)
    {
        $student = $this->student($request)->load('classes.teacher');

        return response()->json([
            'data' => $student->classes->map(fn ($class) => $this->classPayload($class))->values(),
        ]);
    }

    public function sessions(Request $request, int $classId)
    {
        $student = $this->student($request);
        $class = $this->studentClass($student, $classId)->load([
            'sessions.assignments.exercise',
        ]);

        return response()->json([
            'class' => $this->classPayload($class),
            'data' => $class->sessions->map(fn ($session) => $this->sessionPayload($session, $student->id))->values(),
        ]);
    }

    public function sessionDetail(Request $request, int $sessionId)
    {
        $student = $this->student($request);
        $session = ClassSession::with(['schoolClass', 'assignments.exercise'])->findOrFail($sessionId);

        $this->abortIfSessionNotAllowed($student, $session);

        return response()->json([
            'data' => $this->sessionPayload($session, $student->id),
        ]);
    }

    public function assignment(Request $request, int $assignmentId)
    {
        $student = $this->student($request);
        $assignment = Assignment::with(['exercise', 'session.schoolClass'])->findOrFail($assignmentId);
        $this->abortIfAssignmentNotAllowed($student, $assignment);

        $questions = $this->assignmentQuestions($assignment);

        return response()->json([
            'assignment' => $this->assignmentPayload($assignment, $student->id),
            'questions' => $questions->map(fn ($question) => $this->questionPayload($question))->values(),
            'latest_submission' => $this->submissionPayload($assignment->submissionOf($student->id)),
        ]);
    }

    public function checkAnswer(Request $request, int $assignmentId, int $questionId)
    {
        $student = $this->student($request);
        $assignment = Assignment::with(['session.schoolClass'])->findOrFail($assignmentId);
        $this->abortIfAssignmentNotAllowed($student, $assignment);

        $request->validate(['answer' => 'nullable']);

        $question = $this->assignmentQuestions($assignment)->firstWhere('id', $questionId);
        abort_if(!$question, 404, 'Không tìm thấy câu hỏi trong bài tập.');

        return response()->json([
            'data' => $this->questionAnswerService->evaluateSingleQuestion($question, $request->input('answer', '')),
        ]);
    }

    public function submitAssignment(Request $request, int $assignmentId)
    {
        $student = $this->student($request);
        $assignment = Assignment::with(['session.schoolClass'])->findOrFail($assignmentId);
        $this->abortIfAssignmentNotAllowed($student, $assignment);

        $questionIds = data_get($assignment->generation_config, 'question_ids', []);

        if (is_array($questionIds) && !empty($questionIds)) {
            $request->validate(['answers' => 'nullable|array']);

            $questions = $this->assignmentQuestions($assignment);
            $answers = [];

            foreach ($questionIds as $questionId) {
                $rawAnswer = $request->input('answers.' . $questionId, '');
                $answers[(string) $questionId] = is_array($rawAnswer)
                    ? json_encode($rawAnswer, JSON_UNESCAPED_UNICODE)
                    : trim((string) $rawAnswer);
            }

            $result = $this->questionAnswerService->evaluateQuestionFlowDetails($questions, $answers, (float) $assignment->max_score);
            $payload = [
                'mode' => 'question_flow',
                'question_ids' => $questionIds,
                'answers' => $answers,
                'result' => $result,
            ];

            $submission = $this->assignmentService->submitAssignment($assignment->id, $student->id, [
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'json_params' => $payload,
                'score' => $result['score'],
            ]);
        } else {
            $request->validate(['content' => 'nullable|string']);

            $submission = $this->assignmentService->submitAssignment($assignment->id, $student->id, [
                'content' => (string) $request->input('content', ''),
            ]);
        }

        return response()->json([
            'message' => 'Nộp bài thành công.',
            'submission' => $this->submissionPayload($submission->fresh()),
        ], 201);
    }

    public function submissions(Request $request, int $assignmentId)
    {
        $student = $this->student($request);
        $assignment = Assignment::with(['session.schoolClass'])->findOrFail($assignmentId);
        $this->abortIfAssignmentNotAllowed($student, $assignment);

        $submissions = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return response()->json([
            'assignment' => $this->assignmentPayload($assignment, $student->id),
            'data' => $submissions->map(fn ($submission) => $this->submissionPayload($submission))->values(),
        ]);
    }

    private function student(Request $request): Student
    {
        return $request->attributes->get('mobile_student');
    }

    private function studentClass(Student $student, int $classId): SchoolClass
    {
        $class = $student->classes()->where('classes.id', $classId)->first();
        abort_if(!$class, 404, 'Không tìm thấy lớp học.');

        return $class;
    }

    private function abortIfSessionNotAllowed(Student $student, ClassSession $session): void
    {
        $allowed = $student->classes()->where('classes.id', $session->class_id)->exists();
        abort_if(!$allowed, 404, 'Không tìm thấy buổi học.');
    }

    private function abortIfAssignmentNotAllowed(Student $student, Assignment $assignment): void
    {
        $allowed = $student->classes()->where('classes.id', optional($assignment->session)->class_id)->exists();
        abort_if(!$allowed, 404, 'Không tìm thấy bài tập.');
        abort_if(optional($assignment->session)->status === 'cancelled', 403, 'Buổi học này đã hủy, học viên không cần làm bài tập của buổi này.');
    }

    private function assignmentQuestions(Assignment $assignment)
    {
        $questionIds = data_get($assignment->generation_config, 'question_ids', []);

        if (!is_array($questionIds) || empty($questionIds)) {
            return collect();
        }

        return QuestionBankItem::with(['group', 'options'])
            ->whereIn('id', $questionIds)
            ->get()
            ->sortBy(fn ($item) => array_search($item->id, $questionIds, true))
            ->values();
    }

    private function studentPayload(Student $student): array
    {
        return [
            'id' => $student->id,
            'student_code' => $student->student_code,
            'full_name' => $student->full_name,
            'email' => $student->email,
            'phone' => $student->phone,
            'date_of_birth' => optional($student->date_of_birth)->toDateString(),
            'address' => $student->address,
        ];
    }

    private function classPayload(SchoolClass $class): array
    {
        return [
            'id' => $class->id,
            'class_code' => $class->class_code,
            'name' => $class->name,
            'description' => $class->description,
            'teacher' => optional($class->teacher)->name,
            'start_date' => optional($class->start_date)->toDateString(),
            'end_date' => optional($class->end_date)->toDateString(),
            'status' => $class->status,
        ];
    }

    private function sessionPayload(ClassSession $session, ?int $studentId = null): array
    {
        return [
            'id' => $session->id,
            'class_id' => $session->class_id,
            'session_number' => $session->session_number,
            'title' => $session->title,
            'description' => $session->description,
            'session_date' => optional($session->session_date)->toDateTimeString(),
            'status' => $session->status,
            'completed_at' => optional($session->completed_at)->toDateTimeString(),
            'cancelled_at' => optional($session->cancelled_at)->toDateTimeString(),
            'class' => $session->relationLoaded('schoolClass') && $session->schoolClass ? $this->classPayload($session->schoolClass) : null,
            'assignments' => $session->status !== 'cancelled' && $session->relationLoaded('assignments')
                ? $session->assignments->map(fn ($assignment) => $this->assignmentPayload($assignment, $studentId))->values()
                : [],
        ];
    }

    private function assignmentPayload(Assignment $assignment, ?int $studentId = null): array
    {
        $latestSubmission = $studentId ? $assignment->submissionOf($studentId) : null;

        return [
            'id' => $assignment->id,
            'session_id' => $assignment->session_id,
            'title' => optional($assignment->exercise)->title,
            'description' => optional($assignment->exercise)->description,
            'instructions' => $assignment->instructions,
            'due_date' => optional($assignment->due_date)->toDateTimeString(),
            'max_score' => (float) $assignment->max_score,
            'generated_question_count' => $assignment->generated_question_count,
            'has_questions' => !empty(data_get($assignment->generation_config, 'question_ids', [])),
            'latest_submission' => $this->submissionPayload($latestSubmission),
        ];
    }

    private function questionPayload(QuestionBankItem $question): array
    {
        $interactionType = $question->interaction_type ?? 'normal';
        $questionType = $interactionType === 'normal' ? $question->answer_mode : $interactionType;

        return [
            'id' => $question->id,
            'group' => $question->group ? [
                'id' => $question->group->id,
                'title' => $question->group->title,
                'passage' => $question->group->passage,
                'audio_url' => $this->publicFileUrl($question->group->audio_url),
            ] : null,
            'title' => $question->title,
            'question_text' => $question->question_text,
            'passage' => $question->passage,
            'audio_url' => $this->publicFileUrl($question->audio_url),
            'context_type' => $question->context_type,
            'question_type' => $questionType,
            'type_label' => $this->questionAnswerService->questionTypeLabel($question),
            'interaction_data' => $this->publicInteractionData($question->interaction_data ?? []),
            'options' => $question->options->map(fn ($option) => [
                'id' => $option->id,
                'option_text' => $option->option_text,
                'order_index' => $option->order_index,
            ])->values(),
        ];
    }

    private function submissionPayload(?AssignmentSubmission $submission): ?array
    {
        if (!$submission) {
            return null;
        }

        return [
            'id' => $submission->id,
            'assignment_id' => $submission->assignment_id,
            'student_id' => $submission->student_id,
            'content' => $submission->content,
            'file_url' => $this->publicFileUrl($submission->file_path ? 'storage/' . $submission->file_path : null),
            'score' => $submission->score,
            'feedback' => $submission->feedback,
            'status' => $submission->status,
            'submitted_at' => optional($submission->submitted_at)->toDateTimeString(),
            'result' => data_get($submission->json_params ?? [], 'result'),
            'answers' => data_get($submission->json_params ?? [], 'answers', []),
        ];
    }

    private function publicInteractionData(array $data): array
    {
        if (isset($data['pairs']) && is_array($data['pairs'])) {
            foreach ($data['pairs'] as &$pair) {
                if (($pair['right_type'] ?? null) === 'image' && !empty($pair['right'])) {
                    $pair['right_url'] = $this->publicFileUrl($pair['right']);
                }
            }
            unset($pair);
        }

        return $data;
    }

    private function publicFileUrl($path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return url(ltrim($path, '/'));
    }
}
