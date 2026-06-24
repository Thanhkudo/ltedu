<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateTestRequest;
use App\Models\QuestionBankItem;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\QuestionGenerationService;
use App\Services\SubmissionService;
use App\Services\TestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    private TestService $testService;
    private SubmissionService $submissionService;
    private QuestionGenerationService $questionGenerationService;

    public function __construct(TestService $testService, SubmissionService $submissionService, QuestionGenerationService $questionGenerationService)
    {
        $this->testService       = $testService;
        $this->submissionService = $submissionService;
        $this->questionGenerationService = $questionGenerationService;
    }

    public function index()
    {
        $classes = SchoolClass::with(['tests' => fn ($q) => $q->latest()])->get();
        return view('admin.tests.index', compact('classes'));
    }

    public function create(Request $request)
    {
        $classes   = SchoolClass::where('status', 'active')->get();
        $classId   = $request->query('class_id');
        $categories = $this->questionGenerationService->categoryOptions();
        $questionInventory = QuestionBankItem::query()
            ->select([
                'question_bank_items.category_id',
                'question_bank_items.answer_mode',
                'question_bank_items.context_type',
                'question_categories.grade_level',
                'question_categories.skill_type',
            ])
            ->join('question_categories', 'question_categories.id', '=', 'question_bank_items.category_id')
            ->where('question_bank_items.is_active', true)
            ->where('question_categories.is_active', true)
            ->get();

        return view('admin.tests.create', compact('classes', 'classId', 'categories', 'questionInventory'));
    }

    public function store(StoreTestRequest $request)
    {
        $data = $request->validated();
        $creator = User::where('role', 'teacher')->first() ?? User::first();

        $autoGenerate = (bool) ($data['auto_generate_questions'] ?? false);

        // Time inputs are submitted as HH:mm; convert to datetime for persistence.
        $baseDate = Carbon::today();
        $startsAt = Carbon::createFromFormat('Y-m-d H:i', $baseDate->format('Y-m-d') . ' ' . $data['starts_at']);
        $endsAt = Carbon::createFromFormat('Y-m-d H:i', $baseDate->format('Y-m-d') . ' ' . $data['ends_at']);
        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            $endsAt->addDay();
        }

        $data['starts_at'] = $startsAt;
        $data['ends_at'] = $endsAt;

        $testData = $data;
        unset($testData['auto_generate_questions'], $testData['grade_level'], $testData['skill_type'], $testData['question_configs']);

        $test = DB::transaction(function () use ($autoGenerate, $creator, $data, $testData) {
            $test = $this->testService->createTest(
                array_merge($testData, ['created_by' => $creator->id])
            );

            if ($autoGenerate) {
                $count = $this->questionGenerationService->addQuestionsToTestFromConfig($test->id, [
                    'grade_level' => $data['grade_level'],
                    'skill_type' => $data['skill_type'],
                    'question_configs' => $data['question_configs'] ?? [],
                ]);

                $test->generated_question_count = $count;
            }

            return $test;
        });

        if ($autoGenerate) {
            $count = (int) ($test->generated_question_count ?? 0);

            return redirect()->route('admin.tests.show', $test->id)
                ->with('success', 'Tạo bài kiểm tra thành công! Đã thêm ngẫu nhiên ' . $count . ' câu từ kho câu hỏi.');
        }

        return redirect()->route('admin.tests.show', $test->id)
            ->with('success', 'Tạo bài kiểm tra thành công! Hãy thêm câu hỏi.');
    }

    public function edit(int $id)
    {
        $test = $this->testService->getTest($id);

        $classes = SchoolClass::where('status', 'active')
            ->orWhere('id', $test->class_id)
            ->orderBy('name')
            ->get();

        $categories = $this->questionGenerationService->categoryOptions();
        $questionInventory = QuestionBankItem::query()
            ->select([
                'question_bank_items.category_id',
                'question_bank_items.answer_mode',
                'question_bank_items.context_type',
                'question_categories.grade_level',
                'question_categories.skill_type',
            ])
            ->join('question_categories', 'question_categories.id', '=', 'question_bank_items.category_id')
            ->where('question_bank_items.is_active', true)
            ->where('question_categories.is_active', true)
            ->get();

        return view('admin.tests.edit', compact('test', 'classes', 'categories', 'questionInventory'));
    }

    public function update(UpdateTestRequest $request, int $id)
    {
        $data = $request->validated();
        $test = $this->testService->getTest($id);
        $regenerateQuestions = (bool) ($data['regenerate_questions'] ?? false);

        $baseDate = $test->starts_at ? Carbon::parse($test->starts_at)->startOfDay() : Carbon::today();
        $startsAt = Carbon::createFromFormat('Y-m-d H:i', $baseDate->format('Y-m-d') . ' ' . $data['starts_at']);
        $endsAt = Carbon::createFromFormat('Y-m-d H:i', $baseDate->format('Y-m-d') . ' ' . $data['ends_at']);
        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            $endsAt->addDay();
        }

        $data['starts_at'] = $startsAt;
        $data['ends_at'] = $endsAt;

        $updateData = $data;
        unset(
            $updateData['regenerate_questions'],
            $updateData['grade_level'],
            $updateData['skill_type'],
            $updateData['question_configs'],
            $updateData['existing_questions']
        );

        $generatedCount = 0;
        $editedQuestions = $data['existing_questions'] ?? [];

        DB::transaction(function () use ($id, $updateData, $regenerateQuestions, $data, $editedQuestions, &$generatedCount) {
            $updatedTest = $this->testService->updateTest($id, $updateData);

            if ($regenerateQuestions) {
                $updatedTest->questions()->delete();

                $generatedCount = $this->questionGenerationService->addQuestionsToTestFromConfig($id, [
                    'grade_level' => $data['grade_level'],
                    'skill_type' => $data['skill_type'],
                    'question_configs' => $data['question_configs'] ?? [],
                ]);
            } elseif (!empty($editedQuestions)) {
                $this->testService->syncQuestions($id, $editedQuestions);
            }
        });

        if ($regenerateQuestions) {
            return redirect()->route('admin.tests.show', $id)
                ->with('success', 'Cập nhật bài kiểm tra thành công! Đã tạo lại ' . $generatedCount . ' câu hỏi theo cấu hình mới.');
        }

        return redirect()->route('admin.tests.show', $id)
            ->with('success', 'Cập nhật bài kiểm tra thành công!');
    }

    public function show(int $id)
    {
        $test = $this->testService->getTest($id);
        return view('admin.tests.show', compact('test'));
    }

    public function destroy(int $id)
    {
        $this->testService->deleteTest($id);
        return redirect()->route('admin.tests.index')
            ->with('success', 'Xoá bài kiểm tra thành công!');
    }

    public function publish(int $id)
    {
        $this->testService->publishTest($id);
        return redirect()->route('admin.tests.show', $id)
            ->with('success', 'Phát hành bài kiểm tra thành công!');
    }

    /**
     * POST /admin/tests/{id}/questions
     */
    public function addQuestion(StoreQuestionRequest $request, int $id)
    {
        $this->testService->addQuestion($id, $request->validated());
        return redirect()->route('admin.tests.show', $id)
            ->with('success', 'Thêm câu hỏi thành công!');
    }

    /**
     * POST /admin/questions/{questionId}/delete
     */
    public function deleteQuestion(int $questionId)
    {
        $question = \App\Models\TestQuestion::findOrFail($questionId);
        $testId   = $question->test_id;
        $this->testService->deleteQuestion($questionId);
        return redirect()->route('admin.tests.show', $testId)
            ->with('success', 'Xoá câu hỏi thành công!');
    }

    /**
     * GET /admin/tests/{id}/submissions
     */
    public function submissions(int $id)
    {
        $test        = $this->testService->getTest($id);
        $submissions = $this->submissionService->getSubmissionsForTest($id);
        return view('admin.tests.submissions', compact('test', 'submissions'));
    }
}
