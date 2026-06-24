<?php

namespace App\Http\Requests;

use App\Models\QuestionBankItem;
use App\Models\QuestionCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1|max:300',
            'total_score' => 'nullable|numeric|min:1',
            'starts_at' => 'required|date_format:H:i',
            'ends_at' => 'required|date_format:H:i|after:starts_at',

            'regenerate_questions' => 'nullable|boolean',
            'grade_level' => 'required_if:regenerate_questions,1|nullable|integer|in:6,7,8,9',
            'skill_type' => 'required_if:regenerate_questions,1|nullable|in:listening,speaking,reading,writing,grammar,vocabulary',
            'question_configs' => 'required_if:regenerate_questions,1|array|min:1',
            'question_configs.*.category_id' => 'nullable|exists:question_categories,id',
            'question_configs.*.answer_mode' => 'nullable|in:select,input',
            'question_configs.*.context_type' => 'nullable|in:normal,reading,listening',
            'question_configs.*.quantity' => 'required_with:question_configs.*|integer|min:1|max:100',

            'existing_questions' => 'nullable|array',
            'existing_questions.*.id' => 'required_with:existing_questions|integer|exists:test_questions,id',
            'existing_questions.*.question_text' => 'required_with:existing_questions|string',
            'existing_questions.*.question_type' => 'required_with:existing_questions|in:multiple_choice,true_false,short_answer,essay',
            'existing_questions.*.score' => 'nullable|numeric|min:0',
            'existing_questions.*.order_index' => 'nullable|integer|min:1',
            'existing_questions.*.options' => 'nullable|array',
            'existing_questions.*.options.*.id' => 'nullable|integer|exists:question_options,id',
            'existing_questions.*.options.*.option_text' => 'nullable|string',
            'existing_questions.*.options.*.is_correct' => 'nullable|boolean',
            'existing_questions.*.options.*.order_index' => 'nullable|integer|min:1',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!(bool) $this->input('regenerate_questions')) {
                return;
            }

            $gradeLevel = (int) $this->input('grade_level');
            $skillType = (string) $this->input('skill_type');
            $rows = $this->input('question_configs', []);

            foreach ($rows as $index => $row) {
                $quantity = max(0, (int) ($row['quantity'] ?? 0));
                if ($quantity < 1) {
                    continue;
                }

                $categoryId = isset($row['category_id']) && $row['category_id'] !== ''
                    ? (int) $row['category_id']
                    : null;

                if ($categoryId) {
                    $category = QuestionCategory::find($categoryId);

                    if (!$category) {
                        continue;
                    }

                    if ((int) $category->grade_level !== $gradeLevel || (string) $category->skill_type !== $skillType) {
                        $validator->errors()->add(
                            'question_configs.' . $index . '.category_id',
                            'Danh mục ở dòng ' . ($index + 1) . ' không khớp với trình độ và kỹ năng đã chọn.'
                        );
                        continue;
                    }
                }

                $query = QuestionBankItem::query()
                    ->where('is_active', true)
                    ->whereHas('category', function ($q) use ($gradeLevel, $skillType, $categoryId) {
                        $q->where('grade_level', $gradeLevel)
                            ->where('skill_type', $skillType)
                            ->where('is_active', true);

                        if ($categoryId) {
                            $q->where('id', $categoryId);
                        }
                    });

                if (!empty($row['answer_mode'])) {
                    $query->where('answer_mode', (string) $row['answer_mode']);
                }

                if (!empty($row['context_type'])) {
                    $query->where('context_type', (string) $row['context_type']);
                }

                $availableCount = $query->count();

                if ($availableCount === 0) {
                    $validator->errors()->add(
                        'question_configs.' . $index . '.quantity',
                        'Dòng ' . ($index + 1) . ' hiện không có câu hỏi nào phù hợp trong kho. Hãy đổi danh mục hoặc bộ lọc.'
                    );
                    continue;
                }

                if ($quantity > $availableCount) {
                    $validator->errors()->add(
                        'question_configs.' . $index . '.quantity',
                        'Dòng ' . ($index + 1) . ' chỉ có ' . $availableCount . ' câu phù hợp trong kho, không đủ ' . $quantity . ' câu như đã chọn.'
                    );
                }
            }

            $questions = $this->input('existing_questions', []);

            foreach ($questions as $qIndex => $question) {
                $type = (string) ($question['question_type'] ?? '');
                $options = $question['options'] ?? [];

                if (!in_array($type, ['multiple_choice', 'true_false'], true)) {
                    continue;
                }

                $nonEmptyOptions = [];
                foreach ($options as $option) {
                    $text = trim((string) ($option['option_text'] ?? ''));
                    if ($text !== '') {
                        $nonEmptyOptions[] = $option;
                    }
                }

                if (count($nonEmptyOptions) < 2) {
                    $validator->errors()->add(
                        'existing_questions.' . $qIndex . '.options',
                        'Câu ' . ($qIndex + 1) . ' cần ít nhất 2 lựa chọn có nội dung.'
                    );
                    continue;
                }

                $correctCount = collect($nonEmptyOptions)->filter(function ($option) {
                    return (bool) ($option['is_correct'] ?? false);
                })->count();

                if ($correctCount < 1) {
                    $validator->errors()->add(
                        'existing_questions.' . $qIndex . '.options',
                        'Câu ' . ($qIndex + 1) . ' cần chọn ít nhất 1 đáp án đúng.'
                    );
                    continue;
                }

                if ($type === 'true_false' && $correctCount !== 1) {
                    $validator->errors()->add(
                        'existing_questions.' . $qIndex . '.options',
                        'Câu đúng/sai ở dòng ' . ($qIndex + 1) . ' chỉ được có đúng 1 đáp án đúng.'
                    );
                }
            }
        });
    }
}
