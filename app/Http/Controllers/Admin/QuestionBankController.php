<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBankItem;
use App\Models\QuestionGroup;
use App\Models\QuestionCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $query = QuestionBankItem::with(['category', 'group', 'options'])->latest();

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

        if ($request->filled('question_type')) {
            $questionType = (string) $request->question_type;

            if (in_array($questionType, ['select', 'input'], true)) {
                $query->where('answer_mode', $questionType)
                    ->where('interaction_type', 'normal');
            } elseif (in_array($questionType, ['ordering', 'matching'], true)) {
                $query->where('interaction_type', $questionType);
            }
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
        $groups = $this->activeGroups();

        return view('admin.question-bank.create', compact('categories', 'groups'));
    }

    public function store(Request $request)
    {
        $this->mergeQuestionType($request);
        $data = $request->validate($this->rules(true));
        $data = $this->normalizeQuestionGroupData($data);
        $interactionType = (string) ($data['interaction_type'] ?? 'normal');
        $interactionData = $this->buildInteractionData($data, $interactionType);

        DB::transaction(function () use ($data, $interactionType, $interactionData) {
            $answerMode = $interactionType === 'normal' ? $data['answer_mode'] : 'input';

            $item = QuestionBankItem::create([
                'category_id' => $data['category_id'],
                'group_id' => $data['group_id'] ?? null,
                'title' => $data['title'] ?? null,
                'question_text' => $data['question_text'],
                'passage' => $data['passage'] ?? null,
                'audio_url' => $data['audio_url'] ?? null,
                'answer_mode' => $answerMode,
                'interaction_type' => $interactionType,
                'interaction_data' => $interactionData,
                'context_type' => $data['context_type'],
                'correct_answer' => $answerMode === 'input' && $interactionType === 'normal' ? ($data['correct_answer'] ?? null) : null,
                'explanation' => $data['explanation'] ?? null,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            if ($interactionType === 'normal' && $answerMode === 'select') {
                $this->syncSelectOptions($item, $data);
            }
        });

        return redirect()->route('admin.question-bank.index')->with('success', 'Them cau hoi vao kho thanh cong.');
    }

    public function edit(int $id)
    {
        $question = QuestionBankItem::with(['category', 'group', 'options'])->findOrFail($id);
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('grade_level')->orderBy('skill_type')->orderBy('name')
            ->get();
        $groups = $this->activeGroups();

        return view('admin.question-bank.edit', compact('question', 'categories', 'groups'));
    }

    public function update(Request $request, int $id)
    {
        $question = QuestionBankItem::with('options')->findOrFail($id);
        $this->mergeQuestionType($request);
        $data = $request->validate($this->rules(false));
        $data = $this->normalizeQuestionGroupData($data);
        $interactionType = (string) ($data['interaction_type'] ?? $question->interaction_type ?? 'normal');
        $interactionData = $this->buildInteractionData($data, $interactionType);

        DB::transaction(function () use ($question, $data, $interactionType, $interactionData) {
            $answerMode = $interactionType === 'normal' ? ($data['answer_mode'] ?? $question->answer_mode) : 'input';

            $question->update([
                'category_id' => $data['category_id'],
                'group_id' => $data['group_id'] ?? null,
                'title' => $data['title'] ?? null,
                'question_text' => $data['question_text'],
                'passage' => $data['passage'] ?? null,
                'audio_url' => $data['audio_url'] ?? null,
                'answer_mode' => $answerMode,
                'interaction_type' => $interactionType,
                'interaction_data' => $interactionData,
                'context_type' => $data['context_type'],
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

        return redirect()->route('admin.question-bank.index')->with('success', 'Cap nhat cau hoi thanh cong.');
    }

    public function destroy(int $id)
    {
        QuestionBankItem::findOrFail($id)->delete();

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Da xoa cau hoi khoi kho.');
    }

    public function importForm()
    {
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('grade_level')->orderBy('skill_type')->orderBy('name')
            ->get();

        return view('admin.question-bank.import', compact('categories'));
    }

    public function downloadImportTemplate()
    {
        $keys = $this->importHeaders();
        $headers = array_values($this->importHeaderLabels());
        $rows = [
            [
                'category_id' => '1',
                'title' => 'Câu chọn đáp án mẫu',
                'question_text' => 'Choose the correct answer: I ___ a student.',
                'question_type' => 'select',
                'context_type' => 'normal',
                'correct_answer' => '',
                'options' => 'am|is|are|be',
                'correct_option' => '1',
                'ordering_items' => '',
                'matching_pairs' => '',
                'passage' => '',
                'audio_url' => '',
                'explanation' => 'Sau I dùng am.',
            ],
            [
                'category_id' => '1',
                'title' => 'Câu nhập đáp án mẫu',
                'question_text' => 'Fill in the blank: She ___ to school every day.',
                'question_type' => 'input',
                'context_type' => 'normal',
                'correct_answer' => 'goes',
                'options' => '',
                'correct_option' => '',
                'ordering_items' => '',
                'matching_pairs' => '',
                'passage' => '',
                'audio_url' => '',
                'explanation' => '',
            ],
            [
                'category_id' => '1',
                'title' => 'Câu sắp xếp mẫu',
                'question_text' => 'Sắp xếp các từ thành câu đúng.',
                'question_type' => 'ordering',
                'context_type' => 'normal',
                'correct_answer' => '',
                'options' => '',
                'correct_option' => '',
                'ordering_items' => 'I|am|a student',
                'matching_pairs' => '',
                'passage' => '',
                'audio_url' => '',
                'explanation' => '',
            ],
            [
                'category_id' => '1',
                'title' => 'Câu nối đáp án mẫu',
                'question_text' => 'Nối từ với nghĩa phù hợp.',
                'question_type' => 'matching',
                'context_type' => 'normal',
                'correct_answer' => '',
                'options' => '',
                'correct_option' => '',
                'ordering_items' => '',
                'matching_pairs' => 'Cat=>Con mèo|Dog=>Con chó',
                'passage' => '',
                'audio_url' => '',
                'explanation' => '',
            ],
        ];

        $export = new class($headers, $keys, $rows) implements FromArray, WithHeadings {
            private array $headers;
            private array $keys;
            private array $rows;

            public function __construct(array $headers, array $keys, array $rows)
            {
                $this->headers = $headers;
                $this->keys = $keys;
                $this->rows = $rows;
            }

            public function headings(): array
            {
                return $this->headers;
            }

            public function array(): array
            {
                return array_map(function ($row) {
                    return array_map(fn ($key) => $row[$key] ?? '', $this->keys);
                }, $this->rows);
            }
        };

        return Excel::download($export, 'mau-import-kho-cau-hoi.xlsx', ExcelWriter::XLSX);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:4096',
        ]);

        try {
            $items = $this->parseImportFile($request->file('import_file'));
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages([
                'import_file' => $exception->getMessage(),
            ]);
        }

        DB::transaction(function () use ($items) {
            $groupCache = [];

            foreach ($items as $itemData) {
                $groupId = null;
                if ($itemData['group_key'] !== '' && in_array($itemData['context_type'], ['reading', 'listening'], true)) {
                    $cacheKey = $itemData['category_id'] . '|' . $itemData['context_type'] . '|' . $itemData['group_key'];

                    if (!isset($groupCache[$cacheKey])) {
                        $groupCache[$cacheKey] = QuestionGroup::create([
                            'category_id' => $itemData['category_id'],
                            'type' => $itemData['context_type'],
                            'title' => $itemData['group_title'] ?: ($itemData['title'] ?: $itemData['group_key']),
                            'passage' => $itemData['context_type'] === 'reading' ? ($itemData['passage'] ?: null) : null,
                            'audio_url' => $itemData['context_type'] === 'listening' ? ($itemData['audio_url'] ?: null) : null,
                            'difficulty' => $itemData['difficulty'],
                            'is_active' => true,
                            'created_by' => auth()->id(),
                        ])->id;
                    }

                    $groupId = $groupCache[$cacheKey];
                    $group = QuestionGroup::find($groupId);
                    if ($group) {
                        $itemData['passage'] = $group->passage ?: $itemData['passage'];
                        $itemData['audio_url'] = $group->audio_url ?: $itemData['audio_url'];
                    }
                }

                $item = QuestionBankItem::create([
                    'category_id' => $itemData['category_id'],
                    'group_id' => $groupId,
                    'title' => $itemData['title'] ?: null,
                    'question_text' => $itemData['question_text'],
                    'passage' => $itemData['passage'] ?: null,
                    'audio_url' => $itemData['audio_url'] ?: null,
                    'answer_mode' => $itemData['answer_mode'],
                    'interaction_type' => $itemData['interaction_type'],
                    'interaction_data' => $itemData['interaction_data'],
                    'context_type' => $itemData['context_type'],
                    'difficulty' => $itemData['difficulty'],
                    'correct_answer' => $itemData['correct_answer'] ?: null,
                    'explanation' => $itemData['explanation'] ?: null,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                ]);

                foreach ($itemData['options'] as $idx => $option) {
                    $item->options()->create([
                        'option_text' => $option,
                        'is_correct' => $idx === $itemData['correct_option'],
                        'order_index' => $idx + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Import thành công ' . count($items) . ' câu hỏi.');
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

        return back()->with('success', 'Tao danh muc cau hoi thanh cong.');
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

        return back()->with('success', 'Cap nhat danh muc thanh cong.');
    }

    public function destroyCategory(int $id)
    {
        $category = QuestionCategory::findOrFail($id);
        abort_if($category->bankItems()->exists(), 422, 'Danh muc dang co cau hoi, khong the xoa.');
        $category->delete();

        return back()->with('success', 'Da xoa danh muc cau hoi.');
    }

    public function groups(Request $request)
    {
        $query = QuestionGroup::with(['category'])
            ->withCount('questions')
            ->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', (string) $request->type);
        }

        $groups = $query->paginate(15)->withQueryString();
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('grade_level')->orderBy('skill_type')->orderBy('name')
            ->get();

        return view('admin.question-bank.groups', compact('groups', 'categories'));
    }

    public function storeGroup(Request $request)
    {
        $data = $request->validate($this->groupRules());

        QuestionGroup::create([
            'category_id' => $data['category_id'],
            'type' => $data['type'],
            'title' => $data['title'] ?? null,
            'passage' => $data['type'] === 'reading' ? ($data['passage'] ?? null) : null,
            'audio_url' => $data['type'] === 'listening' ? ($data['audio_url'] ?? null) : null,
            'difficulty' => 'medium',
            'is_active' => (bool) ($data['is_active'] ?? true),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Tạo nhóm câu hỏi thành công.');
    }

    public function updateGroup(Request $request, int $id)
    {
        $group = QuestionGroup::findOrFail($id);
        $data = $request->validate($this->groupRules());

        DB::transaction(function () use ($group, $data) {
            $group->update([
                'category_id' => $data['category_id'],
                'type' => $data['type'],
                'title' => $data['title'] ?? null,
                'passage' => $data['type'] === 'reading' ? ($data['passage'] ?? null) : null,
                'audio_url' => $data['type'] === 'listening' ? ($data['audio_url'] ?? null) : null,
                'difficulty' => 'medium',
                'is_active' => (bool) ($data['is_active'] ?? false),
            ]);

            $group->questions()->update([
                'category_id' => $group->category_id,
                'context_type' => $group->type,
                'passage' => $group->passage,
                'audio_url' => $group->audio_url,
            ]);
        });

        return back()->with('success', 'Cập nhật nhóm câu hỏi thành công.');
    }

    public function destroyGroup(int $id)
    {
        $group = QuestionGroup::withCount('questions')->findOrFail($id);
        abort_if($group->questions_count > 0, 422, 'Nhóm đang có câu hỏi, không thể xóa.');
        $group->delete();

        return back()->with('success', 'Đã xóa nhóm câu hỏi.');
    }

    private function activeGroups()
    {
        return QuestionGroup::with('category')
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('title')
            ->get();
    }

    private function normalizeQuestionGroupData(array $data): array
    {
        $groupId = (int) ($data['group_id'] ?? 0);
        if ($groupId < 1) {
            $data['group_id'] = null;
            return $data;
        }

        $group = QuestionGroup::findOrFail($groupId);
        $data['group_id'] = $group->id;
        $data['category_id'] = $group->category_id;
        $data['context_type'] = $group->type;
        $data['passage'] = $group->passage;
        $data['audio_url'] = $group->audio_url;

        return $data;
    }

    private function groupRules(): array
    {
        return [
            'category_id' => 'required|exists:question_categories,id',
            'type' => 'required|in:reading,listening',
            'title' => 'nullable|string|max:255',
            'passage' => 'nullable|string|required_if:type,reading',
            'audio_url' => 'nullable|string|max:2048|required_if:type,listening',
            'is_active' => 'nullable|boolean',
        ];
    }

    private function rules(bool $creating): array
    {
        return [
            'category_id' => 'required|exists:question_categories,id',
            'group_id' => 'nullable|exists:question_groups,id',
            'title' => 'nullable|string|max:255',
            'question_text' => 'required|string',
            'passage' => 'nullable|string',
            'audio_url' => 'nullable|string|max:2048',
            'question_type' => 'required|in:select,input,matching,ordering',
            'answer_mode' => 'required|in:select,input',
            'interaction_type' => 'required|in:normal,ordering,matching',
            'context_type' => 'required|in:normal,reading,listening',
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
            'matching_right_image.*' => 'nullable|string|max:2048',
        ];
    }

    private function syncSelectOptions(QuestionBankItem $item, array $data): void
    {
        $options = collect($data['options'] ?? [])->filter(fn ($v) => filled($v))->values();
        abort_if($options->count() < 2, 422, 'Cau hoi chon dap an can it nhat 2 dap an.');

        $correctIndex = (int) ($data['correct_option'] ?? -1);
        abort_if($correctIndex < 0 || $correctIndex >= $options->count(), 422, 'Dap an dung khong hop le.');

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

            abort_if($items->count() < 2, 422, 'Cau hoi sap xep can it nhat 2 dap an.');

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

            abort_if($leftText === '' || $rightValue === '', 422, 'Moi cap noi dap an can du ve trai va ve phai.');

            $pairs[] = [
                'left' => $leftText,
                'right_type' => $type,
                'right' => $rightValue,
            ];
        }

        abort_if(count($pairs) < 2, 422, 'Cau hoi noi dap an can it nhat 2 cap.');

        return ['pairs' => $pairs];
    }

    private function mergeQuestionType(Request $request): void
    {
        $questionType = (string) $request->input('question_type', 'select');

        $request->merge([
            'answer_mode' => in_array($questionType, ['select', 'input'], true) ? $questionType : 'input',
            'interaction_type' => in_array($questionType, ['ordering', 'matching'], true) ? $questionType : 'normal',
        ]);
    }

    private function importHeaders(): array
    {
        return [
            'group_key',
            'group_title',
            'category_id',
            'title',
            'question_text',
            'question_type',
            'context_type',
            'correct_answer',
            'options',
            'correct_option',
            'ordering_items',
            'matching_pairs',
            'passage',
            'audio_url',
            'explanation',
        ];
    }

    private function importHeaderLabels(): array
    {
        return [
            'group_key' => 'Mã nhóm',
            'group_title' => 'Tiêu đề nhóm',
            'category_id' => 'ID danh mục',
            'title' => 'Tiêu đề',
            'question_text' => 'Nội dung câu hỏi',
            'question_type' => 'Kiểu câu hỏi',
            'context_type' => 'Ngữ cảnh',
            'correct_answer' => 'Đáp án đúng',
            'options' => 'Danh sách đáp án',
            'correct_option' => 'Vị trí đáp án đúng',
            'ordering_items' => 'Thứ tự đáp án',
            'matching_pairs' => 'Cặp nối đáp án',
            'passage' => 'Đoạn văn',
            'audio_url' => 'File audio',
            'explanation' => 'Giải thích',
        ];

        return [
            'category_id' => 'ID danh mục',
            'title' => 'Tiêu đề',
            'question_text' => 'Nội dung câu hỏi',
            'question_type' => 'Kiểu câu hỏi',
            'context_type' => 'Ngữ cảnh',
            'correct_answer' => 'Đáp án đúng',
            'options' => 'Danh sách đáp án',
            'correct_option' => 'Vị trí đáp án đúng',
            'ordering_items' => 'Thứ tự đáp án',
            'matching_pairs' => 'Cặp nối đáp án',
            'passage' => 'Đoạn văn',
            'audio_url' => 'File audio',
            'explanation' => 'Giải thích',
        ];
    }

    private function normalizeImportHeaders(array $headers): array
    {
        $labels = array_flip($this->importHeaderLabels());

        return array_map(function ($header) use ($labels) {
            $header = trim((string) $header);

            return $labels[$header] ?? $header;
        }, $headers);
    }

    private function parseImportFile(UploadedFile $file): array
    {
        $import = new class implements ToArray {
            public function array(array $array)
            {
            }
        };

        $sheets = Excel::toArray($import, $file);
        $sheetRows = $sheets[0] ?? [];
        $headers = array_shift($sheetRows);
        if (!$headers) {
            throw ValidationException::withMessages(['import_file' => 'File import đang trống.']);
        }

        $headers = array_map(function ($value) {
            return trim((string) preg_replace('/^\xEF\xBB\xBF/', '', (string) $value));
        }, $headers);
        $headers = $this->normalizeImportHeaders($headers);

        $requiredHeaders = array_diff($this->importHeaders(), ['group_key', 'group_title']);
        $missingHeaders = array_diff($requiredHeaders, $headers);
        if (!empty($missingHeaders)) {
            throw ValidationException::withMessages([
                'import_file' => 'File thiếu cột: ' . implode(', ', $missingHeaders),
            ]);
        }

        $items = [];
        $errors = [];
        $lineNumber = 1;

        foreach ($sheetRows as $row) {
            $lineNumber++;

            if ($this->isEmptyImportRow($row)) {
                continue;
            }

            $row = array_pad($row, count($headers), '');
            $data = array_combine($headers, array_slice($row, 0, count($headers)));
            $normalized = $this->normalizeImportRow($data);
            $validator = Validator::make($normalized, [
                'group_key' => 'nullable|string|max:100',
                'group_title' => 'nullable|string|max:255',
                'category_id' => 'required|exists:question_categories,id',
                'title' => 'nullable|string|max:255',
                'question_text' => 'required|string',
                'question_type' => 'required|in:select,input,matching,ordering',
                'context_type' => 'required|in:normal,reading,listening',
                'correct_answer' => 'nullable|string|max:255',
                'passage' => 'nullable|string',
                'audio_url' => 'nullable|string|max:2048',
                'explanation' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors[] = 'Dòng ' . $lineNumber . ': ' . $validator->errors()->first();
                continue;
            }

            try {
                $items[] = $this->buildImportedQuestionData($normalized, $lineNumber);
            } catch (\InvalidArgumentException $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        if (empty($items) && empty($errors)) {
            $errors[] = 'File không có dòng dữ liệu nào.';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages(['import_file' => implode("\n", $errors)]);
        }

        return $items;
    }

    private function normalizeImportRow(array $row): array
    {
        $normalized = [];

        foreach ($this->importHeaders() as $header) {
            $normalized[$header] = trim((string) ($row[$header] ?? ''));
        }

        $normalized['question_type'] = strtolower($normalized['question_type'] ?: 'select');
        $normalized['context_type'] = strtolower($normalized['context_type'] ?: 'normal');
        $normalized['difficulty'] = strtolower($normalized['difficulty'] ?? 'medium');

        return $normalized;
    }

    private function buildImportedQuestionData(array $row, int $lineNumber): array
    {
        $questionType = $row['question_type'];
        $answerMode = in_array($questionType, ['select', 'input'], true) ? $questionType : 'input';
        $interactionType = in_array($questionType, ['ordering', 'matching'], true) ? $questionType : 'normal';
        $options = [];
        $correctOption = null;
        $interactionData = null;
        $correctAnswer = null;

        if ($questionType === 'select') {
            $options = $this->splitImportList($row['options']);
            if (count($options) < 2) {
                throw new \InvalidArgumentException('Dòng ' . $lineNumber . ': câu chọn đáp án cần ít nhất 2 lựa chọn.');
            }

            $correctOption = (int) $row['correct_option'];
            if ($row['correct_option'] === '' || $correctOption < 0 || $correctOption >= count($options)) {
                throw new \InvalidArgumentException('Dòng ' . $lineNumber . ': correct_option phải là vị trí đáp án đúng, bắt đầu từ 0.');
            }
        } elseif ($questionType === 'input') {
            $correctAnswer = $row['correct_answer'];
            if ($correctAnswer === '') {
                throw new \InvalidArgumentException('Dòng ' . $lineNumber . ': câu nhập đáp án cần correct_answer.');
            }
        } elseif ($questionType === 'ordering') {
            $items = $this->splitImportList($row['ordering_items']);
            if (count($items) < 2) {
                throw new \InvalidArgumentException('Dòng ' . $lineNumber . ': câu sắp xếp cần ít nhất 2 mục trong ordering_items.');
            }

            $interactionData = ['items' => $items];
        } elseif ($questionType === 'matching') {
            $pairs = $this->parseImportMatchingPairs($row['matching_pairs'], $lineNumber);
            if (count($pairs) < 2) {
                throw new \InvalidArgumentException('Dòng ' . $lineNumber . ': câu nối đáp án cần ít nhất 2 cặp trong matching_pairs.');
            }

            $interactionData = ['pairs' => $pairs];
        }

        return [
            'group_key' => $row['group_key'],
            'group_title' => $row['group_title'],
            'category_id' => (int) $row['category_id'],
            'title' => $row['title'],
            'question_text' => $row['question_text'],
            'passage' => $row['context_type'] === 'reading' ? $row['passage'] : '',
            'audio_url' => $row['context_type'] === 'listening' ? $row['audio_url'] : '',
            'answer_mode' => $answerMode,
            'interaction_type' => $interactionType,
            'interaction_data' => $interactionData,
            'context_type' => $row['context_type'],
            'difficulty' => $row['difficulty'],
            'correct_answer' => $correctAnswer,
            'explanation' => $row['explanation'],
            'options' => $options,
            'correct_option' => $correctOption,
        ];
    }

    private function splitImportList(string $value): array
    {
        return collect(explode('|', $value))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    private function parseImportMatchingPairs(string $value, int $lineNumber): array
    {
        $pairs = [];

        foreach ($this->splitImportList($value) as $pairText) {
            $parts = explode('=>', $pairText, 2);
            if (count($parts) !== 2) {
                throw new \InvalidArgumentException('Dòng ' . $lineNumber . ': mỗi cặp nối phải có dạng Vế trái=>Vế phải.');
            }

            $left = trim($parts[0]);
            $right = trim($parts[1]);
            if ($left === '' || $right === '') {
                throw new \InvalidArgumentException('Dòng ' . $lineNumber . ': cặp nối không được để trống vế trái hoặc vế phải.');
            }

            $rightType = strpos($right, 'image:') === 0 ? 'image' : 'text';
            $rightValue = $rightType === 'image' ? trim(substr($right, 6)) : $right;

            if ($rightValue === '') {
                throw new \InvalidArgumentException('Dòng ' . $lineNumber . ': đường dẫn ảnh trong cặp nối không được để trống.');
            }

            $pairs[] = [
                'left' => $left,
                'right_type' => $rightType,
                'right' => $rightValue,
            ];
        }

        return $pairs;
    }

    private function isEmptyImportRow(array $row): bool
    {
        return collect($row)->every(fn ($value) => trim((string) $value) === '');
    }
}
