@extends('layouts.admin')
@section('title', 'Giao bài tập')
@section('page-title', 'Giao bài tập cho lớp')
@section('page-actions')
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Trở về</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="/admin/assignments" id="assignmentForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Buổi học <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-select @error('session_id') is-invalid @enderror" required>
                            <option value="">-- Chọn buổi học --</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}" {{ (old('session_id', request('session_id')) == $session->id) ? 'selected' : '' }}>
                                    [{{ $session->schoolClass->name ?? '?' }}] Buổi {{ $session->session_number }} — {{ $session->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="border rounded p-3 mb-3 bg-light-subtle">
                        <h6 class="fw-semibold mb-2">Cấu hình bài tập từ kho câu hỏi</h6>
                        <p class="text-muted small mb-3">Bài tập của buổi học này sẽ được hệ thống sinh tự động từ kho câu hỏi theo cấu hình bên dưới.</p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Trình độ</label>
                                <select name="grade_level" class="form-select">
                                    <option value="6">Lớp 6</option>
                                    <option value="7">Lớp 7</option>
                                    <option value="8">Lớp 8</option>
                                    <option value="9">Lớp 9</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Loại kỹ năng</label>
                                <select name="skill_type" class="form-select">
                                    @foreach(['listening'=>'Nghe','speaking'=>'Nói','reading'=>'Đọc','writing'=>'Viết','grammar'=>'Ngữ pháp','vocabulary'=>'Từ vựng'] as $k => $lbl)
                                        <option value="{{ $k }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <table class="table table-sm" id="configTable">
                            <thead>
                            <tr>
                                <th>Danh mục</th>
                                <th>Dạng trả lời</th>
                                <th>Ngữ cảnh</th>
                                <th>Kiểu câu</th>
                                <th>Số câu</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <select name="question_configs[0][category_id]" class="form-select form-select-sm">
                                        <option value="">-- Tất cả --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">L{{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="question_configs[0][answer_mode]" class="form-select form-select-sm">
                                        <option value="">Tất cả</option>
                                        <option value="select">Chọn đáp án</option>
                                        <option value="input">Nhập đáp án</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="question_configs[0][context_type]" class="form-select form-select-sm">
                                        <option value="">Tất cả</option>
                                        <option value="normal">Thường</option>
                                        <option value="reading">Đọc hiểu</option>
                                        <option value="listening">Nghe</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="question_configs[0][interaction_type]" class="form-select form-select-sm">
                                        <option value="">Tất cả</option>
                                        <option value="normal">Bình thường</option>
                                        <option value="ordering">Sắp xếp</option>
                                        <option value="matching">Nối đáp án</option>
                                    </select>
                                </td>
                                <td><input type="number" name="question_configs[0][quantity]" min="1" value="5" class="form-control form-control-sm"></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">X</button></td>
                            </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addConfigRow()">+ Thêm cấu hình dòng</button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hướng dẫn</label>
                        <textarea name="instructions" class="form-control" rows="3">{{ old('instructions') }}</textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hạn nộp <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Điểm tối đa</label>
                            <input type="number" name="max_score" min="0" max="1000" step="1" class="form-control @error('max_score') is-invalid @enderror" value="{{ old('max_score', 10) }}">
                            @error('max_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Giao bài</button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let configIndex = 1;

function addConfigRow() {
    const tbody = document.querySelector('#configTable tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="question_configs[${configIndex}][category_id]" class="form-select form-select-sm">
                <option value="">-- Tất cả --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">L{{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="question_configs[${configIndex}][answer_mode]" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="select">Chọn đáp án</option>
                <option value="input">Nhập đáp án</option>
            </select>
        </td>
        <td>
            <select name="question_configs[${configIndex}][context_type]" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="normal">Thường</option>
                <option value="reading">Đọc hiểu</option>
                <option value="listening">Nghe</option>
            </select>
        </td>
        <td>
            <select name="question_configs[${configIndex}][interaction_type]" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="normal">Bình thường</option>
                <option value="ordering">Sắp xếp</option>
                <option value="matching">Nối đáp án</option>
            </select>
        </td>
        <td><input type="number" name="question_configs[${configIndex}][quantity]" min="1" value="5" class="form-control form-control-sm"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">X</button></td>`;
    tbody.appendChild(row);
    configIndex++;
}

function removeRow(btn) {
    const rows = document.querySelectorAll('#configTable tbody tr');
    if (rows.length === 1) return;
    btn.closest('tr').remove();
}
</script>
@endpush
