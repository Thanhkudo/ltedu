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
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Kiểu câu hỏi</label>
                    <select name="interaction_type" id="interaction_type" class="form-select" onchange="toggleInteractionType()">
                        <option value="normal" {{ old('interaction_type', 'normal') === 'normal' ? 'selected' : '' }}>Bình thường</option>
                        <option value="ordering" {{ old('interaction_type') === 'ordering' ? 'selected' : '' }}>Sắp xếp đáp án</option>
                        <option value="matching" {{ old('interaction_type') === 'matching' ? 'selected' : '' }}>Nối đáp án</option>
                    </select>
                </div>
                <div class="col-md-3 normal-only">
                    <label class="form-label fw-semibold">Kiểu trả lời</label>
                    <select name="answer_mode" id="answer_mode" class="form-select" onchange="toggleAnswerMode()">
                        <option value="select" {{ old('answer_mode') === 'select' ? 'selected' : '' }}>Chọn đáp án đúng</option>
                        <option value="input" {{ old('answer_mode') === 'input' ? 'selected' : '' }}>Nhập đáp án đúng</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Ngữ cảnh câu hỏi</label>
                    <select name="context_type" id="context_type" class="form-select" onchange="toggleContext()">
                        <option value="normal">Bình thường</option>
                        <option value="reading">Đọc đoạn văn</option>
                        <option value="listening">Nghe audio</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Độ khó</label>
                    <select name="difficulty" class="form-select">
                        <option value="easy">Easy</option>
                        <option value="medium" selected>Medium</option>
                        <option value="hard">Hard</option>
                    </select>
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
                <label class="form-label fw-semibold">Link audio</label>
                <input type="url" name="audio_url" class="form-control" placeholder="https://..." value="{{ old('audio_url') }}">
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
                            <div class="col-md-6 right-image d-none"><input type="url" name="matching_right_image[]" class="form-control" placeholder="Link ảnh https://..."></div>
                        </div>
                    @endfor
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMatchingRow()">+ Thêm cặp</button>
            </div>

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
<script>
function toggleAnswerMode() {
    const mode = document.getElementById('answer_mode').value;
    document.getElementById('selectOptionsWrap').classList.toggle('d-none', mode !== 'select');
    document.getElementById('inputAnswerWrap').classList.toggle('d-none', mode !== 'input');
}
function toggleContext() {
    const context = document.getElementById('context_type').value;
    document.getElementById('passageWrap').classList.toggle('d-none', context !== 'reading');
    document.getElementById('audioWrap').classList.toggle('d-none', context !== 'listening');
}
function toggleInteractionType() {
    const type = document.getElementById('interaction_type').value;
    document.querySelectorAll('.normal-only').forEach(el => el.classList.toggle('d-none', type !== 'normal'));
    document.getElementById('orderingWrap').classList.toggle('d-none', type !== 'ordering');
    document.getElementById('matchingWrap').classList.toggle('d-none', type !== 'matching');
    toggleAnswerMode();
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
    row.innerHTML = `<div class="col-md-4"><input type="text" name="matching_left[]" class="form-control" placeholder="Vế trái"></div><div class="col-md-2"><select name="matching_right_type[]" class="form-select" onchange="toggleMatchingRight(this)"><option value="text">Chữ</option><option value="image">Ảnh</option></select></div><div class="col-md-6 right-text"><input type="text" name="matching_right_text[]" class="form-control" placeholder="Vế phải bằng chữ"></div><div class="col-md-6 right-image d-none"><input type="url" name="matching_right_image[]" class="form-control" placeholder="Link ảnh https://..."></div>`;
    document.getElementById('matchingRows').appendChild(row);
}
toggleInteractionType(); toggleContext();
</script>
@endpush