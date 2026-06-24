<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBankItem;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $query = QuestionBankItem::with(['category', 'options'])->latest();

        if ($request->filled('keyword')) {
            $query->where('question_text', 'like', '%' . trim((string) $request->keyword) . '%');
        }

        if ($request->filled('grade_level')) {
            $query->whereHas('category', fn ($q) => $q->where('grade_level', (int) $request->grade_level));
        }

        if ($request->filled('skill_type')) {
            $query->whereHas('category', fn ($q) => $q->where('skill_type', (string) $request->skill_type));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->category_id);
        }

        if ($request->filled('answer_mode')) {
            $query->where('answer_mode', (string) $request->answer_mode);
        }

        if ($request->filled('context_type')) {
            $query->where('context_type', (string) $request->context_type);
        }

        $questions = $query->paginate(15)->withQueryString();
        $categories = QuestionCategory::orderBy('grade_level')->orderBy('skill_type')->orderBy('name')->get();

        return view('admin.question-bank.index', compact('questions', 'categories'));
    }

    public function create()
    {
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('grade_level')->orderBy('skill_type')->orderBy('name')
            ->get();

        return view('admin.question-bank.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(true));
        $interactionType = (string) ($data['interaction_type'] ?? 'normal');
        $interactionData = $this->buildInteractionData($data, $interactionType);

        DB::transaction(function () use ($data, $interactionType, $interactionData) {
            $answerMode = $interactionType === 'normal' ? $data['answer_mode'] : 'input';

            $item = QuestionBankItem::create([
                'category_id' => $data['category_id'],
                'title' => $data['title'] ?? null,
                'question_text' => $data['question_text'],
                'passage' => $data['passage'] ?? null,
                'audio_url' => $data['audio_url'] ?? null,
                'answer_mode' => $answerMode,
                'interaction_type' => $interactionType,
                'interaction_data' => $interactionData,
                'context_type' => $data['context_type'],
                'difficulty' => $data['difficulty'] ?? 'medium',
                'correct_answer' => $answerMode === 'input' && $interactionType === 'normal' ? ($data['correct_answer'] ?? null) : null,
                'explanation' => $data['explanation'] ?? null,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            if ($interactionType === 'normal' && $answerMode === 'select') {
                $this->syncSelectOptions($item, $data);
            }
        });

        return redirect()->route('admin.question-bank.index')->with('success', 'Thêm câu hỏi vào kho thành công.');
    }

    public function edit(int $id)
    {
        $question = QuestionBankItem::with(['category', 'options'])->findOrFail($id);
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('grade_level')->orderBy('skill_type')->orderBy('name')
            ->get();

        return view('admin.question-bank.edit', compact('question', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $question = QuestionBankItem::with('options')->findOrFail($id);
        $data = $request->validate($this->rules(false));
        $interactionType = (string) ($data['interaction_type'] ?? $question->interaction_type ?? 'normal');
        $interactionData = $this->buildInteractionData($data, $interactionType);

        DB::transaction(function () use ($question, $data, $interactionType, $interactionData) {
            $answerMode = $interactionType === 'normal' ? ($data['answer_mode'] ?? $question->answer_mode) : 'input';

            $question->update([
                'category_id' => $data['category_id'],
                'title' => $data['title'] ?? null,
                'question_text' => $data['question_text'],
                'passage' => $data['passage'] ?? null,
                'audio_url' => $data['audio_url'] ?? null,
                'answer_mode' => $answerMode,
                'interaction_type' => $interactionType,
                'interaction_data' => $interactionData,
                'context_type' => $data['context_type'],
                'difficulty' => $data['difficulty'] ?? 'medium',
                'correct_answer' => $answerMode === 'input' && $interactionType === 'normal' ? ($data['correct_answer'] ?? null) : null,
                'explanation' => $data['explanation'] ?? null,
            ]);

            if ($interactionType === 'normal' && $answerMode === 'select') {
                $question->options()->delete();
                $this->syncSelectOptions($question, $data);
            } else {
                $question->options()->delete();
            }
        });

        return redirect()->route('admin.question-bank.index')->with('success', 'Cập nhật câu hỏi thành công.');
    }

    public function destroy(int $id)
    {
        QuestionBankItem::findOrFail($id)->delete();

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Đã xóa câu hỏi khỏi kho.');
    }

    public function categories()
    {
        $categories = QuestionCategory::latest()->paginate(20);

        return view('admin.question-bank.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|in:6,7,8,9',
            'skill_type' => 'required|in:listening,speaking,reading,writing,grammar,vocabulary',
            'topic' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        QuestionCategory::create([
            'name' => $data['name'],
            'grade_level' => $data['grade_level'],
            'skill_type' => $data['skill_type'],
            'topic' => $data['topic'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return back()->with('success', 'Tạo danh mục câu hỏi thành công.');
    }

    public function updateCategory(Request $request, int $id)
    {
        $category = QuestionCategory::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|in:6,7,8,9',
            'skill_type' => 'required|in:listening,speaking,reading,writing,grammar,vocabulary',
            'topic' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $data['name'],
            'grade_level' => $data['grade_level'],
            'skill_type' => $data['skill_type'],
            'topic' => $data['topic'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return back()->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroyCategory(int $id)
    {
        $category = QuestionCategory::findOrFail($id);
        abort_if($category->bankItems()->exists(), 422, 'Danh mục đang có câu hỏi, không thể xóa.');
        $category->delete();

        return back()->with('success', 'Đã xóa danh mục câu hỏi.');
    }

    private function rules(bool $creating): array
    {
        return [
            'category_id' => 'required|exists:question_categories,id',
            'title' => 'nullable|string|max:255',
            'question_text' => 'required|string',
            'passage' => 'nullable|string',
            'audio_url' => 'nullable|url',
            'answer_mode' => 'required|in:select,input',
            'interaction_type' => 'required|in:normal,ordering,matching',
            'context_type' => 'required|in:normal,reading,listening',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'correct_answer' => 'nullable|string|max:255',
            'explanation' => 'nullable|string',
            'options' => 'nullable|array|min:2',
            'options.*' => 'nullable|string',
            'correct_option' => 'nullable|integer|min:0',
            'ordering_items' => 'nullable|array|min:2',
            'ordering_items.*' => 'nullable|string|max:255',
            'matching_left' => 'nullable|array|min:2',
            'matching_left.*' => 'nullable|string|max:255',
            'matching_right_type' => 'nullable|array',
            'matching_right_type.*' => 'nullable|in:text,image',
            'matching_right_text' => 'nullable|array',
            'matching_right_text.*' => 'nullable|string|max:255',
            'matching_right_image' => 'nullable|array',
            'matching_right_image.*' => 'nullable|url',
        ];
    }

    private function syncSelectOptions(QuestionBankItem $item, array $data): void
    {
        $options = collect($data['options'] ?? [])->filter(fn ($v) => filled($v))->values();
        abort_if($options->count() < 2, 422, 'Câu hỏi chọn đáp án cần ít nhất 2 đáp án.');

        $correctIndex = (int) ($data['correct_option'] ?? -1);
        abort_if($correctIndex < 0 || $correctIndex >= $options->count(), 422, 'Đáp án đúng không hợp lệ.');

        foreach ($options as $idx => $optionText) {
            $item->options()->create([
                'option_text' => $optionText,
                'is_correct' => $idx === $correctIndex,
                'order_index' => $idx + 1,
            ]);
        }
    }

    private function buildInteractionData(array $data, string $interactionType): ?array
    {
        if ($interactionType === 'normal') {
            return null;
        }

        if ($interactionType === 'ordering') {
            $items = collect($data['ordering_items'] ?? [])
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->values();

            abort_if($items->count() < 2, 422, 'Câu hỏi sắp xếp cần ít nhất 2 đáp án.');

            return ['items' => $items->all()];
        }

        $left = $data['matching_left'] ?? [];
        $rightTypes = $data['matching_right_type'] ?? [];
        $rightTexts = $data['matching_right_text'] ?? [];
        $rightImages = $data['matching_right_image'] ?? [];
        $pairs = [];

        foreach ($left as $idx => $leftText) {
            $leftText = trim((string) $leftText);
            $type = (string) ($rightTypes[$idx] ?? 'text');
            $rightValue = $type === 'image'
                ? trim((string) ($rightImages[$idx] ?? ''))
                : trim((string) ($rightTexts[$idx] ?? ''));

            if ($leftText === '' && $rightValue === '') {
                continue;
            }

            abort_if($leftText === '' || $rightValue === '', 422, 'Mỗi cặp nối đáp án cần đủ vế trái và vế phải.');

            $pairs[] = [
                'left' => $leftText,
                'right_type' => $type,
                'right' => $rightValue,
            ];
        }

        abort_if(count($pairs) < 2, 422, 'Câu hỏi nối đáp án cần ít nhất 2 cặp.');

        return ['pairs' => $pairs];
    }
}