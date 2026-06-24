@extends('layouts.admin')
@section('title', 'Tạo bài kiểm tra')
@section('page-title', 'Tạo bài kiểm tra mới')
@section('page-actions')
    <a href="/admin/tests" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Trở về</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="/admin/tests">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lớp học <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">-- Chọn lớp --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} — {{ $class->subject }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Thời gian làm bài (phút)</label>
                            <input type="number" name="duration" min="1" class="form-control" value="{{ old('duration', 45) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Điểm tổng</label>
                            <input type="number" name="total_score" min="1" class="form-control" value="{{ old('total_score', 100) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Điểm đạt (%)</label>
                            <input type="number" name="passing_score" min="0" max="100" class="form-control" value="{{ old('passing_score', 60) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bắt đầu</label>
                            <input type="time" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at', '08:00') }}">
                            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kết thúc</label>
                            <input type="time" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at', '09:00') }}">
                            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="auto_generate_questions" name="auto_generate_questions" value="1" onchange="toggleAutoGen()" {{ old('auto_generate_questions') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="auto_generate_questions">Tự động sinh câu hỏi từ kho theo cấu hình</label>
                        </div>

                        <div id="autoGenWrap" class="d-none">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Trình độ</label>
                                    <select name="grade_level" class="form-select @error('grade_level') is-invalid @enderror" onchange="syncTestConfigCategories()">
                                        @for($g = 6; $g <= 9; $g++)
                                            <option value="{{ $g }}" {{ (string) old('grade_level', 6) === (string) $g ? 'selected' : '' }}>Lớp {{ $g }}</option>
                                        @endfor
                                    </select>
                                    @error('grade_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Loại kỹ năng</label>
                                    <select name="skill_type" class="form-select @error('skill_type') is-invalid @enderror" onchange="syncTestConfigCategories()">
                                        @foreach(['listening'=>'Nghe','speaking'=>'Nói','reading'=>'Đọc','writing'=>'Viết','grammar'=>'Ngữ pháp','vocabulary'=>'Từ vựng'] as $k => $lbl)
                                            <option value="{{ $k }}" {{ old('skill_type', 'listening') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                    @error('skill_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <table class="table table-sm" id="testConfigTable">
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
                                        ['category_id' => '', 'answer_mode' => '', 'context_type' => '', 'quantity' => 10],
                                    ]);
                                @endphp
                                @foreach($oldConfigs as $index => $config)
                                <tr>
                                    <td>
                                        <select name="question_configs[{{ $index }}][category_id]" class="form-select form-select-sm @error('question_configs.' . $index . '.category_id') is-invalid @enderror test-category-select" data-selected="{{ $config['category_id'] ?? '' }}">
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
                                        <input type="number" name="question_configs[{{ $index }}][quantity]" min="1" value="{{ $config['quantity'] ?? 10 }}" class="form-control form-control-sm @error('question_configs.' . $index . '.quantity') is-invalid @enderror">
                                        @error('question_configs.' . $index . '.quantity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        <div class="form-text test-config-hint mt-1"></div>
                                    </td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTestRow(this)">X</button></td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="small text-muted mt-2">Danh mục sẽ tự lọc theo trình độ và kỹ năng đã chọn.</div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTestConfigRow()">+ Thêm cấu hình dòng</button>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Tạo bài kiểm tra</button>
                        <a href="/admin/tests" class="btn btn-outline-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let tIdx = {{ count($oldConfigs) }};
const questionInventory = @json($questionInventory);

const testCategoryOptions = `
    <option value="">-- Tất cả --</option>
    @foreach($categories as $cat)
        <option value="{{ $cat->id }}" data-grade="{{ $cat->grade_level }}" data-skill="{{ $cat->skill_type }}">L{{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}</option>
    @endforeach
`;

function toggleAutoGen() {
    const on = document.getElementById('auto_generate_questions').checked;
    document.getElementById('autoGenWrap').classList.toggle('d-none', !on);
}

function syncCategorySelect(select) {
    const grade = document.querySelector('[name="grade_level"]')?.value || '';
    const skill = document.querySelector('[name="skill_type"]')?.value || '';
    const selectedValue = select.dataset.selected || select.value || '';

    select.innerHTML = testCategoryOptions;

    Array.from(select.options).forEach(function(option) {
        if (!option.value) {
            return;
        }

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

function syncTestConfigCategories() {
    document.querySelectorAll('.test-category-select').forEach(syncCategorySelect);
    updateAllTestConfigHints();
}

function countAvailableQuestions(row) {
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

function updateTestConfigHint(row) {
    const hint = row.querySelector('.test-config-hint');
    if (!hint) return;

    const requested = Number(row.querySelector('[name*="[quantity]"]')?.value || 0);
    const available = countAvailableQuestions(row);

    if (available === 0) {
        hint.textContent = 'Hiện có 0 câu phù hợp với cấu hình này trong kho.';
        hint.className = 'form-text test-config-hint mt-1 text-danger';
        return;
    }

    if (requested > 0 && requested > available) {
        hint.textContent = 'Hiện có ' + available + ' câu phù hợp trong kho, ít hơn số câu bạn đang yêu cầu.';
        hint.className = 'form-text test-config-hint mt-1 text-warning';
        return;
    }

    hint.textContent = 'Hiện có ' + available + ' câu phù hợp với cấu hình này trong kho.';
    hint.className = 'form-text test-config-hint mt-1 text-success';
}

function updateAllTestConfigHints() {
    document.querySelectorAll('#testConfigTable tbody tr').forEach(updateTestConfigHint);
}

function bindTestConfigRowEvents(row) {
    row.querySelectorAll('select, input').forEach(function(field) {
        field.addEventListener('change', function() {
            if (field.classList.contains('test-category-select')) {
                field.dataset.selected = field.value;
            }
            updateTestConfigHint(row);
        });

        if (field.name && field.name.indexOf('[quantity]') !== -1) {
            field.addEventListener('input', function() {
                updateTestConfigHint(row);
            });
        }
    });
}

function addTestConfigRow() {
    const tbody = document.querySelector('#testConfigTable tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="question_configs[${tIdx}][category_id]" class="form-select form-select-sm test-category-select" data-selected="">
            </select>
        </td>
        <td>
            <select name="question_configs[${tIdx}][answer_mode]" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="select">Chọn đáp án</option>
                <option value="input">Nhập đáp án</option>
            </select>
        </td>
        <td>
            <select name="question_configs[${tIdx}][context_type]" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="normal">Thường</option>
                <option value="reading">Đọc hiểu</option>
                <option value="listening">Nghe</option>
            </select>
        </td>
        <td>
            <input type="number" name="question_configs[${tIdx}][quantity]" min="1" value="5" class="form-control form-control-sm">
            <div class="form-text test-config-hint mt-1"></div>
        </td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTestRow(this)">X</button></td>`;
    tbody.appendChild(row);
    syncCategorySelect(row.querySelector('.test-category-select'));
    bindTestConfigRowEvents(row);
    updateTestConfigHint(row);
    tIdx++;
}
function removeTestRow(btn) {
    const rows = document.querySelectorAll('#testConfigTable tbody tr');
    if (rows.length === 1) return;
    btn.closest('tr').remove();
}
toggleAutoGen();
document.querySelectorAll('#testConfigTable tbody tr').forEach(bindTestConfigRowEvents);
syncTestConfigCategories();
</script>
@endpush
