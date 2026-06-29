@extends('layouts.admin')
@section('title', 'Thêm câu hỏi kho')
@section('page-title', 'Thêm câu hỏi vào kho')
@section('page-actions')
    <a href="{{ route('admin.question-bank.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Trở về kho câu hỏi
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.question-bank.store') }}" id="bankForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                Lớp {{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>                <div class="col-md-3">
                    <label class="form-label fw-semibold">Kiểu câu hỏi</label>
                    <select name="question_type" id="question_type" class="form-select" onchange="toggleQuestionType()">
                        <option value="select" {{ old('question_type', 'select') === 'select' ? 'selected' : '' }}>Chọn đáp án</option>
                        <option value="input" {{ old('question_type') === 'input' ? 'selected' : '' }}>Nhập đáp án</option>
                        <option value="matching" {{ old('question_type') === 'matching' ? 'selected' : '' }}>Nối đáp án</option>
                        <option value="ordering" {{ old('question_type') === 'ordering' ? 'selected' : '' }}>Sắp xếp đáp án</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Ngữ cảnh câu hỏi</label>
                    <select name="context_type" id="context_type" class="form-select" onchange="toggleContext()">
                        <option value="normal">Bình thường</option>
                        <option value="reading">Đọc hiểu</option>
                        <option value="listening">Nghe</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nhóm bài đọc/nghe</label>
                    <select name="group_id" id="group_id" class="form-select" onchange="applyQuestionGroup()">
                        <option value="">Không dùng nhóm</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}"
                                data-category="{{ $group->category_id }}"
                                data-type="{{ $group->type }}"
                                {{ (string) old('group_id') === (string) $group->id ? 'selected' : '' }}>
                                {{ $group->type === 'reading' ? 'Bài đọc' : 'Bài nghe' }} - {{ $group->title ?: 'Nhóm #' . $group->id }}
                                (Lớp {{ $group->category->grade_level ?? '?' }} - {{ $group->category->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Nếu chọn nhóm, hệ thống sẽ lấy danh mục, đoạn văn hoặc audio từ nhóm.</div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label fw-semibold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                <textarea name="question_text" rows="3" class="form-control" required>{{ old('question_text') }}</textarea>
            </div>

            <div class="mt-3 d-none" id="passageWrap">
                <label class="form-label fw-semibold">Đoạn văn</label>
                <textarea name="passage" rows="4" class="form-control">{{ old('passage') }}</textarea>
            </div>

            <div class="mt-3 d-none" id="audioWrap">
                <label class="form-label fw-semibold">File audio</label>
                <div class="input-group">
                    <input type="text" name="audio_url" id="audio_url" class="form-control" placeholder="/data/audio/..." value="{{ old('audio_url') }}" readonly>
                    <button type="button" class="btn btn-outline-primary" onclick="chooseCkfinderFile('audio_url', 'Audios')">Chọn từ CKFinder</button>
                </div>
            </div>

            <div class="mt-3 normal-only" id="selectOptionsWrap">
                <label class="form-label fw-semibold">Các lựa chọn</label>
                @for($i = 0; $i < 4; $i++)
                    <div class="input-group mb-2">
                        <span class="input-group-text"><input type="radio" name="correct_option" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}></span>
                        <input type="text" name="options[]" class="form-control" placeholder="Đáp án {{ $i + 1 }}">
                    </div>
                @endfor
            </div>

            <div class="mt-3 d-none normal-only" id="inputAnswerWrap">
                <label class="form-label fw-semibold">Đáp án đúng</label>
                <input type="text" name="correct_answer" class="form-control" value="{{ old('correct_answer') }}">
            </div>

            <div class="mt-3 d-none" id="orderingWrap">
                <label class="form-label fw-semibold">Các đáp án theo đúng thứ tự</label>
                <div id="orderingRows">
                    @for($i = 0; $i < 4; $i++)
                        <input type="text" name="ordering_items[]" class="form-control mb-2" placeholder="Mục thứ {{ $i + 1 }}">
                    @endfor
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOrderingRow()">+ Thêm mục</button>
            </div>

            <div class="mt-3 d-none" id="matchingWrap">
                <label class="form-label fw-semibold">Các cặp nối đáp án</label>
                <div id="matchingRows">
                    @for($i = 0; $i < 3; $i++)
                        <div class="row g-2 mb-2 matching-row">
                            <div class="col-md-4"><input type="text" name="matching_left[]" class="form-control" placeholder="Vế trái"></div>
                            <div class="col-md-2">
                                <select name="matching_right_type[]" class="form-select" onchange="toggleMatchingRight(this)">
                                    <option value="text">Chữ</option>
                                    <option value="image">Ảnh</option>
                                </select>
                            </div>
                            <div class="col-md-6 right-text"><input type="text" name="matching_right_text[]" class="form-control" placeholder="Vế phải bằng chữ"></div>
                            <div class="col-md-6 right-image d-none">
                                <div class="input-group">
                                    <input type="text" name="matching_right_image[]" class="form-control ckfinder-image-input" placeholder="/data/images/..." readonly>
                                    <button type="button" class="btn btn-outline-primary" onclick="chooseCkfinderSiblingFile(this, 'Images')">Chọn ảnh</button>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMatchingRow()">+ Thêm cặp</button>

            <div class="mt-3">
                <label class="form-label fw-semibold">Giải thích</label>
                <textarea name="explanation" rows="2" class="form-control">{{ old('explanation') }}</textarea>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Lưu câu hỏi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/ckfinder/ckfinder.js') }}"></script>
<script>CKFinder.config({ connectorPath: @json(route('ckfinder_connector')) });</script>
<script>
function chooseCkfinderFile(inputId, resourceType) {
    if (typeof CKFinder === 'undefined') {
        alert('CKFinder chưa được tải.');
        return;
    }

    CKFinder.popup({
        chooseFiles: true,
        resourceType: resourceType,
        connectorPath: '{{ route('ckfinder_connector') }}',
        onInit: function(finder) {
            finder.on('files:choose', function(evt) {
                const file = evt.data.files.first();
                document.getElementById(inputId).value = file.getUrl();
            });
        }
    });
}

function chooseCkfinderSiblingFile(button, resourceType) {
    const input = button.closest('.input-group').querySelector('input');
    if (!input) return;

    if (!input.id) {
        input.id = 'ckfinder_file_' + Math.random().toString(36).slice(2);
    }

    chooseCkfinderFile(input.id, resourceType);
}

function toggleContext() {
    const context = document.getElementById('context_type').value;
    document.getElementById('passageWrap').classList.toggle('d-none', context !== 'reading');
    document.getElementById('audioWrap').classList.toggle('d-none', context !== 'listening');
}
function applyQuestionGroup() {
    const select = document.getElementById('group_id');
    const option = select.options[select.selectedIndex];
    if (!option || !option.value) {
        toggleContext();
        return;
    }

    const category = document.querySelector('[name="category_id"]');
    const context = document.getElementById('context_type');
    if (category && option.dataset.category) category.value = option.dataset.category;
    if (context && option.dataset.type) context.value = option.dataset.type;
    toggleContext();
}
function toggleQuestionType() {
    const type = document.getElementById('question_type').value;
    document.getElementById('selectOptionsWrap').classList.toggle('d-none', type !== 'select');
    document.getElementById('inputAnswerWrap').classList.toggle('d-none', type !== 'input');
    document.getElementById('orderingWrap').classList.toggle('d-none', type !== 'ordering');
    document.getElementById('matchingWrap').classList.toggle('d-none', type !== 'matching');
}
function addOrderingRow() {
    const input = document.createElement('input');
    input.type = 'text'; input.name = 'ordering_items[]'; input.className = 'form-control mb-2'; input.placeholder = 'Mục tiếp theo';
    document.getElementById('orderingRows').appendChild(input);
}
function toggleMatchingRight(select) {
    const row = select.closest('.matching-row');
    row.querySelector('.right-text').classList.toggle('d-none', select.value !== 'text');
    row.querySelector('.right-image').classList.toggle('d-none', select.value !== 'image');
}
function addMatchingRow() {
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 matching-row';
    row.innerHTML = `<div class="col-md-4"><input type="text" name="matching_left[]" class="form-control" placeholder="Vế trái"></div><div class="col-md-2"><select name="matching_right_type[]" class="form-select" onchange="toggleMatchingRight(this)"><option value="text">Chữ</option><option value="image">Ảnh</option></select></div><div class="col-md-6 right-text"><input type="text" name="matching_right_text[]" class="form-control" placeholder="Vế phải bằng chữ"></div><div class="col-md-6 right-image d-none"><div class="input-group"><input type="text" name="matching_right_image[]" class="form-control ckfinder-image-input" placeholder="/data/images/..." readonly><button type="button" class="btn btn-outline-primary" onclick="chooseCkfinderSiblingFile(this, 'Images')">Chọn ảnh</button></div></div>`;
    document.getElementById('matchingRows').appendChild(row);
}
toggleQuestionType(); applyQuestionGroup();
</script>
@endpush
