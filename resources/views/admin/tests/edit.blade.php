@extends('layouts.admin')
@section('title', 'Sửa bài kiểm tra')
@section('page-title', 'Sửa bài kiểm tra')
@section('page-actions')
    <a href="/admin/tests/{{ $test->id }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Trở về</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="/admin/tests/{{ $test->id }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lớp học <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">-- Chọn lớp --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (string) old('class_id', $test->class_id) === (string) $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} — {{ $class->subject }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $test->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $test->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thời gian làm bài (phút)</label>
                            <input type="number" name="duration" min="1" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration', (int) $test->duration) }}">
                            @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Điểm tổng</label>
                            <input type="number" name="total_score" min="1" class="form-control @error('total_score') is-invalid @enderror" value="{{ old('total_score', (float) $test->total_score) }}">
                            @error('total_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bắt đầu</label>
                            <input type="time" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at', optional($test->starts_at)->format('H:i')) }}" required>
                            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kết thúc</label>
                            <input type="time" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at', optional($test->ends_at)->format('H:i')) }}" required>
                            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="regenerate_questions" name="regenerate_questions" value="1" onchange="toggleRegenConfig()" {{ old('regenerate_questions') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="regenerate_questions">Tạo lại bộ câu hỏi theo cấu hình mới</label>
                        </div>

                        <div id="regenConfigWrap" class="d-none">
                            <div class="alert alert-warning py-2 small mb-3">
                                Khi bật tính năng này, hệ thống sẽ xóa toàn bộ câu hỏi hiện tại của bài test và sinh lại theo cấu hình bên dưới.
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Trình độ</label>
                                    <select name="grade_level" class="form-select @error('grade_level') is-invalid @enderror" onchange="syncRegenCategories()">
                                        @for($g = 6; $g <= 9; $g++)
                                            <option value="{{ $g }}" {{ (string) old('grade_level', 6) === (string) $g ? 'selected' : '' }}>Lớp {{ $g }}</option>
                                        @endfor
                                    </select>
                                    @error('grade_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Loại kỹ năng</label>
                                    <select name="skill_type" class="form-select @error('skill_type') is-invalid @enderror" onchange="syncRegenCategories()">
                                        @foreach(['listening'=>'Nghe','speaking'=>'Nói','reading'=>'Đọc','writing'=>'Viết','grammar'=>'Ngữ pháp','vocabulary'=>'Từ vựng'] as $k => $lbl)
                                            <option value="{{ $k }}" {{ old('skill_type', 'listening') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                    @error('skill_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <table class="table table-sm" id="regenConfigTable">
                                <thead>
                                    <tr>
                                        <th>Danh mục</th>
                                        <th>Dạng trả lời</th>
                                        <th>Ngữ cảnh</th>
                                        <th>Số câu</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                    $oldConfigs = old('question_configs', [
                                        ['category_id' => '', 'answer_mode' => '', 'context_type' => '', 'quantity' => 5],
                                    ]);
                                @endphp
                                @foreach($oldConfigs as $index => $config)
                                    <tr>
                                        <td>
                                            <select name="question_configs[{{ $index }}][category_id]" class="form-select form-select-sm @error('question_configs.' . $index . '.category_id') is-invalid @enderror regen-category-select" data-selected="{{ $config['category_id'] ?? '' }}">
                                                <option value="">-- Tất cả --</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" data-grade="{{ $cat->grade_level }}" data-skill="{{ $cat->skill_type }}" {{ (string) ($config['category_id'] ?? '') === (string) $cat->id ? 'selected' : '' }}>L{{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('question_configs.' . $index . '.category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </td>
                                        <td>
                                            <select name="question_configs[{{ $index }}][answer_mode]" class="form-select form-select-sm @error('question_configs.' . $index . '.answer_mode') is-invalid @enderror">
                                                <option value="">Tất cả</option>
                                                <option value="select" {{ ($config['answer_mode'] ?? '') === 'select' ? 'selected' : '' }}>Chọn đáp án</option>
                                                <option value="input" {{ ($config['answer_mode'] ?? '') === 'input' ? 'selected' : '' }}>Nhập đáp án</option>
                                            </select>
                                            @error('question_configs.' . $index . '.answer_mode')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </td>
                                        <td>
                                            <select name="question_configs[{{ $index }}][context_type]" class="form-select form-select-sm @error('question_configs.' . $index . '.context_type') is-invalid @enderror">
                                                <option value="">Tất cả</option>
                                                <option value="normal" {{ ($config['context_type'] ?? '') === 'normal' ? 'selected' : '' }}>Thường</option>
                                                <option value="reading" {{ ($config['context_type'] ?? '') === 'reading' ? 'selected' : '' }}>Đọc hiểu</option>
                                                <option value="listening" {{ ($config['context_type'] ?? '') === 'listening' ? 'selected' : '' }}>Nghe</option>
                                            </select>
                                            @error('question_configs.' . $index . '.context_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </td>
                                        <td>
                                            <input type="number" name="question_configs[{{ $index }}][quantity]" min="1" value="{{ $config['quantity'] ?? 5 }}" class="form-control form-control-sm @error('question_configs.' . $index . '.quantity') is-invalid @enderror">
                                            @error('question_configs.' . $index . '.quantity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            <div class="form-text regen-config-hint mt-1"></div>
                                        </td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRegenRow(this)">X</button></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="small text-muted mt-2">Danh mục sẽ tự lọc theo trình độ và kỹ năng đã chọn.</div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRegenRow()">+ Thêm cấu hình dòng</button>
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Chỉnh sửa câu hỏi hiện có</h6>
                            <span class="badge bg-light text-dark border">{{ $test->questions->count() }} câu</span>
                        </div>
                        <div class="small text-muted mb-3">Bạn có thể sửa nội dung câu hỏi, điểm và đáp án đúng trực tiếp tại đây.</div>

                        @if($test->questions->isEmpty())
                            <div class="alert alert-light border mb-0">Bài test chưa có câu hỏi. Bạn có thể tạo lại câu hỏi bằng cấu hình phía trên hoặc thêm từ trang chi tiết test.</div>
                        @else
                            <div class="d-grid gap-3">
                                @foreach($test->questions->sortBy('order_index')->values() as $qIndex => $question)
                                    @php
                                        $oldQuestion = old('existing_questions.' . $qIndex, []);
                                        $oldType = $oldQuestion['question_type'] ?? $question->question_type;
                                        $oldOptions = $oldQuestion['options'] ?? $question->options->map(function ($opt) {
                                            return [
                                                'id' => $opt->id,
                                                'option_text' => $opt->option_text,
                                                'is_correct' => $opt->is_correct,
                                                'order_index' => $opt->order_index,
                                            ];
                                        })->values()->all();
                                    @endphp

                                    <div class="border rounded p-3">
                                        <input type="hidden" name="existing_questions[{{ $qIndex }}][id]" value="{{ $question->id }}">

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-secondary">Câu {{ $qIndex + 1 }}</span>
                                            <small class="text-muted">ID: {{ $question->id }}</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nội dung câu hỏi</label>
                                            <textarea name="existing_questions[{{ $qIndex }}][question_text]" rows="2" class="form-control @error('existing_questions.' . $qIndex . '.question_text') is-invalid @enderror" required>{{ old('existing_questions.' . $qIndex . '.question_text', $question->question_text) }}</textarea>
                                            @error('existing_questions.' . $qIndex . '.question_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Loại câu hỏi</label>
                                                <select name="existing_questions[{{ $qIndex }}][question_type]" class="form-select form-select-sm js-edit-question-type @error('existing_questions.' . $qIndex . '.question_type') is-invalid @enderror" data-target="edit-options-{{ $qIndex }}">
                                                    <option value="multiple_choice" {{ $oldType === 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                                                    <option value="true_false" {{ $oldType === 'true_false' ? 'selected' : '' }}>Đúng / Sai</option>
                                                    <option value="short_answer" {{ $oldType === 'short_answer' ? 'selected' : '' }}>Trả lời ngắn</option>
                                                    <option value="essay" {{ $oldType === 'essay' ? 'selected' : '' }}>Tự luận</option>
                                                </select>
                                                @error('existing_questions.' . $qIndex . '.question_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Điểm</label>
                                                <input type="number" step="0.5" min="0" name="existing_questions[{{ $qIndex }}][score]" class="form-control form-control-sm @error('existing_questions.' . $qIndex . '.score') is-invalid @enderror" value="{{ old('existing_questions.' . $qIndex . '.score', $question->score) }}">
                                                @error('existing_questions.' . $qIndex . '.score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Thứ tự</label>
                                                <input type="number" min="1" name="existing_questions[{{ $qIndex }}][order_index]" class="form-control form-control-sm @error('existing_questions.' . $qIndex . '.order_index') is-invalid @enderror" value="{{ old('existing_questions.' . $qIndex . '.order_index', $question->order_index) }}">
                                                @error('existing_questions.' . $qIndex . '.order_index')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div id="edit-options-{{ $qIndex }}" class="js-edit-options {{ in_array($oldType, ['multiple_choice', 'true_false']) ? '' : 'd-none' }}">
                                            <div class="small text-muted mb-2">Tick vào "Đúng" để chọn đáp án đúng.</div>
                                            @foreach($oldOptions as $oIndex => $option)
                                                <div class="row g-2 mb-2 align-items-center">
                                                    <input type="hidden" name="existing_questions[{{ $qIndex }}][options][{{ $oIndex }}][id]" value="{{ $option['id'] ?? '' }}">
                                                    <input type="hidden" name="existing_questions[{{ $qIndex }}][options][{{ $oIndex }}][order_index]" value="{{ $option['order_index'] ?? ($oIndex + 1) }}">

                                                    <div class="col-md-9">
                                                        <input type="text"
                                                            name="existing_questions[{{ $qIndex }}][options][{{ $oIndex }}][option_text]"
                                                            class="form-control form-control-sm @error('existing_questions.' . $qIndex . '.options.' . $oIndex . '.option_text') is-invalid @enderror"
                                                            value="{{ old('existing_questions.' . $qIndex . '.options.' . $oIndex . '.option_text', $option['option_text'] ?? '') }}"
                                                            placeholder="Nội dung đáp án {{ $oIndex + 1 }}">
                                                        @error('existing_questions.' . $qIndex . '.options.' . $oIndex . '.option_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="hidden" name="existing_questions[{{ $qIndex }}][options][{{ $oIndex }}][is_correct]" value="0">
                                                        <div class="form-check mt-1">
                                                            <input class="form-check-input" type="checkbox" name="existing_questions[{{ $qIndex }}][options][{{ $oIndex }}][is_correct]" value="1" id="q{{ $qIndex }}opt{{ $oIndex }}"
                                                                {{ (string) old('existing_questions.' . $qIndex . '.options.' . $oIndex . '.is_correct', ($option['is_correct'] ?? false) ? '1' : '0') === '1' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="q{{ $qIndex }}opt{{ $oIndex }}">Đúng</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            @error('existing_questions.' . $qIndex . '.options')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Lưu thay đổi</button>
                        <a href="/admin/tests/{{ $test->id }}" class="btn btn-outline-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let regenIdx = {{ count($oldConfigs) }};
const questionInventory = @json($questionInventory);

const regenCategoryOptions = `
    <option value="">-- Tất cả --</option>
    @foreach($categories as $cat)
        <option value="{{ $cat->id }}" data-grade="{{ $cat->grade_level }}" data-skill="{{ $cat->skill_type }}">L{{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}</option>
    @endforeach
`;

function toggleRegenConfig() {
    const on = document.getElementById('regenerate_questions').checked;
    document.getElementById('regenConfigWrap').classList.toggle('d-none', !on);
}

function syncOneRegenCategory(select) {
    const grade = document.querySelector('[name="grade_level"]')?.value || '';
    const skill = document.querySelector('[name="skill_type"]')?.value || '';
    const selectedValue = select.dataset.selected || select.value || '';

    select.innerHTML = regenCategoryOptions;

    Array.from(select.options).forEach(function(option) {
        if (!option.value) return;
        const matches = option.dataset.grade === grade && option.dataset.skill === skill;
        option.hidden = !matches;
        option.disabled = !matches;
    });

    const canKeepSelected = Array.from(select.options).some(function(option) {
        return option.value === selectedValue && !option.disabled;
    });

    select.value = canKeepSelected ? selectedValue : '';
    select.dataset.selected = select.value;
}

function syncRegenCategories() {
    document.querySelectorAll('.regen-category-select').forEach(syncOneRegenCategory);
    updateAllRegenHints();
}

function countRegenAvailable(row) {
    const grade = document.querySelector('[name="grade_level"]')?.value || '';
    const skill = document.querySelector('[name="skill_type"]')?.value || '';
    const categoryId = row.querySelector('[name*="[category_id]"]')?.value || '';
    const answerMode = row.querySelector('[name*="[answer_mode]"]')?.value || '';
    const contextType = row.querySelector('[name*="[context_type]"]')?.value || '';

    return questionInventory.filter(function(item) {
        if (String(item.grade_level) !== String(grade)) return false;
        if (String(item.skill_type) !== String(skill)) return false;
        if (categoryId && String(item.category_id) !== String(categoryId)) return false;
        if (answerMode && String(item.answer_mode) !== String(answerMode)) return false;
        if (contextType && String(item.context_type) !== String(contextType)) return false;
        return true;
    }).length;
}

function updateRegenHint(row) {
    const hint = row.querySelector('.regen-config-hint');
    if (!hint) return;

    const requested = Number(row.querySelector('[name*="[quantity]"]')?.value || 0);
    const available = countRegenAvailable(row);

    if (available === 0) {
        hint.textContent = 'Hiện có 0 câu phù hợp với cấu hình này trong kho.';
        hint.className = 'form-text regen-config-hint mt-1 text-danger';
        return;
    }

    if (requested > 0 && requested > available) {
        hint.textContent = 'Hiện có ' + available + ' câu phù hợp trong kho, ít hơn số câu bạn đang yêu cầu.';
        hint.className = 'form-text regen-config-hint mt-1 text-warning';
        return;
    }

    hint.textContent = 'Hiện có ' + available + ' câu phù hợp với cấu hình này trong kho.';
    hint.className = 'form-text regen-config-hint mt-1 text-success';
}

function updateAllRegenHints() {
    document.querySelectorAll('#regenConfigTable tbody tr').forEach(updateRegenHint);
}

function bindRegenRowEvents(row) {
    row.querySelectorAll('select, input').forEach(function(field) {
        field.addEventListener('change', function() {
            if (field.classList.contains('regen-category-select')) {
                field.dataset.selected = field.value;
            }
            updateRegenHint(row);
        });

        if (field.name && field.name.indexOf('[quantity]') !== -1) {
            field.addEventListener('input', function() {
                updateRegenHint(row);
            });
        }
    });
}

function addRegenRow() {
    const tbody = document.querySelector('#regenConfigTable tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="question_configs[${regenIdx}][category_id]" class="form-select form-select-sm regen-category-select" data-selected=""></select>
        </td>
        <td>
            <select name="question_configs[${regenIdx}][answer_mode]" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="select">Chọn đáp án</option>
                <option value="input">Nhập đáp án</option>
            </select>
        </td>
        <td>
            <select name="question_configs[${regenIdx}][context_type]" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="normal">Thường</option>
                <option value="reading">Đọc hiểu</option>
                <option value="listening">Nghe</option>
            </select>
        </td>
        <td>
            <input type="number" name="question_configs[${regenIdx}][quantity]" min="1" value="5" class="form-control form-control-sm">
            <div class="form-text regen-config-hint mt-1"></div>
        </td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRegenRow(this)">X</button></td>
    `;

    tbody.appendChild(row);
    syncOneRegenCategory(row.querySelector('.regen-category-select'));
    bindRegenRowEvents(row);
    updateRegenHint(row);
    regenIdx++;
}

function removeRegenRow(btn) {
    const rows = document.querySelectorAll('#regenConfigTable tbody tr');
    if (rows.length === 1) return;
    btn.closest('tr').remove();
}

function toggleEditOptionsByType(selectEl) {
    const targetId = selectEl.dataset.target;
    if (!targetId) return;

    const wrap = document.getElementById(targetId);
    if (!wrap) return;

    const type = selectEl.value;
    const show = type === 'multiple_choice' || type === 'true_false';
    wrap.classList.toggle('d-none', !show);
}

document.querySelectorAll('.js-edit-question-type').forEach(function(selectEl) {
    selectEl.addEventListener('change', function() {
        toggleEditOptionsByType(selectEl);
    });
    toggleEditOptionsByType(selectEl);
});

toggleRegenConfig();
document.querySelectorAll('#regenConfigTable tbody tr').forEach(bindRegenRowEvents);
syncRegenCategories();
</script>
@endpush
