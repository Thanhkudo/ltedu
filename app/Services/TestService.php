<?php

namespace App\Services;

use App\Models\SchoolTest;
use App\Models\TestQuestion;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;

class TestService
{
    public function getTestsByClass(int $classId)
    {
        return SchoolTest::where('class_id', $classId)
            ->with('creator')
            ->latest()
            ->get();
    }

    public function getTest(int $id, bool $withAnswers = false): SchoolTest
    {
        $relations = ['schoolClass', 'creator', 'questions.options'];
        return SchoolTest::with($relations)->findOrFail($id);
    }

    public function createTest(array $data): SchoolTest
    {
        return SchoolTest::create($data);
    }

    public function updateTest(int $id, array $data): SchoolTest
    {
        $test = SchoolTest::findOrFail($id);

        abort_if($test->status === 'closed', 422, 'Không thể chỉnh sửa bài kiểm tra đã đóng.');

        $test->update($data);
        return $test->fresh();
    }

    public function deleteTest(int $id): bool
    {
        $test = SchoolTest::findOrFail($id);
        abort_if($test->status === 'published', 422, 'Không thể xoá bài kiểm tra đã phát hành.');
        return $test->delete();
    }

    public function publishTest(int $id): SchoolTest
    {
        $test = SchoolTest::withCount('questions')->findOrFail($id);

        abort_if($test->questions_count === 0, 422, 'Bài kiểm tra cần có ít nhất 1 câu hỏi trước khi phát hành.');
        abort_if($test->status === 'published', 422, 'Bài kiểm tra đã được phát hành.');

        $test->update(['status' => 'published']);
        return $test;
    }

    /**
     * Thêm câu hỏi + đáp án vào bài kiểm tra.
     *
     * @param int   $testId
     * @param array $questionData  {
     *   question_text, question_type, score, order_index,
     *   options: [{option_text, is_correct, order_index}, ...]
     * }
     */
    public function addQuestion(int $testId, array $questionData): TestQuestion
    {
        $test = SchoolTest::findOrFail($testId);
        abort_if($test->status === 'closed', 422, 'Không thể thêm câu hỏi vào bài đã đóng.');

        return DB::transaction(function () use ($testId, $questionData) {
            $options = $questionData['options'] ?? [];
            unset($questionData['options']);

            $question = TestQuestion::create(array_merge($questionData, ['test_id' => $testId]));

            foreach ($options as $option) {
                $question->options()->create($option);
            }

            return $question->load('options');
        });
    }

    public function deleteQuestion(int $questionId): bool
    {
        return TestQuestion::findOrFail($questionId)->delete();
    }

    public function syncQuestions(int $testId, array $questions): void
    {
        $test = SchoolTest::with('questions.options')->findOrFail($testId);

        abort_if($test->status === 'closed', 422, 'Không thể chỉnh sửa câu hỏi của bài đã đóng.');

        DB::transaction(function () use ($test, $questions) {
            $questionMap = $test->questions->keyBy('id');

            foreach ($questions as $payload) {
                $questionId = (int) ($payload['id'] ?? 0);
                $question = $questionMap->get($questionId);

                if (!$question) {
                    continue;
                }

                $questionType = (string) ($payload['question_type'] ?? $question->question_type);

                $question->update([
                    'question_text' => (string) ($payload['question_text'] ?? $question->question_text),
                    'question_type' => $questionType,
                    'score' => (float) ($payload['score'] ?? $question->score),
                    'order_index' => (int) ($payload['order_index'] ?? $question->order_index),
                ]);

                if (!in_array($questionType, ['multiple_choice', 'true_false'], true)) {
                    $question->options()->delete();
                    continue;
                }

                $optionPayloads = $payload['options'] ?? [];
                $optionMap = $question->options->keyBy('id');
                $hasCorrect = false;

                foreach ($optionPayloads as $optIndex => $optPayload) {
                    $optionId = (int) ($optPayload['id'] ?? 0);
                    $option = $optionMap->get($optionId);

                    if (!$option) {
                        continue;
                    }

                    $optionText = trim((string) ($optPayload['option_text'] ?? $option->option_text));
                    if ($optionText === '') {
                        continue;
                    }

                    $isCorrect = (bool) ($optPayload['is_correct'] ?? false);
                    if ($isCorrect) {
                        $hasCorrect = true;
                    }

                    $option->update([
                        'option_text' => $optionText,
                        'is_correct' => $isCorrect,
                        'order_index' => (int) ($optPayload['order_index'] ?? ($optIndex + 1)),
                    ]);
                }

                if (!$hasCorrect) {
                    $firstOption = $question->options()->orderBy('order_index')->first();
                    if ($firstOption) {
                        $firstOption->update(['is_correct' => true]);
                    }
                }
            }
        });
    }
}
