@extends('layouts.admin')
@section('title', 'Giao bài tập')
@section('page-title', 'Giao bài tập cho lớp')
@section('page-actions')
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Trở về</a>
@endsection

@section('content')
<div class="row justify-content-center assignment-create-page">
    <div class="col-xl-9 col-lg-10">
        <div class="card">
            <div class="card-body p-4 assignment-create-body">
                <form method="POST" action="/admin/assignments" id="assignmentForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Buổi học <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-select @error('session_id') is-invalid @enderror" required>
                            <option value="">-- Chọn buổi học --</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}" {{ (old('session_id', request('session_id')) == $session->id) ? 'selected' : '' }}>
                                    [{{ $session->schoolClass->name ?? '?' }}] Buổi {{ $session->session_number }} - {{ $session->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="assignment-config-panel border rounded p-3 mb-3 bg-light-subtle">
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
                        </div>

                        <div class="config-list" id="configList">
                            <div class="config-row">
                                <div class="config-field config-field-category">
                                    <label class="config-label">Danh mục</label>
                                    <select name="question_configs[0][category_id]" class="form-select form-select-sm">
                                        <option value="">-- Tất cả --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">L{{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="config-field">
                                    <label class="config-label">Kiểu câu hỏi</label>
                                    <select name="question_configs[0][question_type]" class="form-select form-select-sm">
                                        <option value="">Tất cả</option>
                                        <option value="select">Chọn đáp án</option>
                                        <option value="input">Nhập đáp án</option>
                                        <option value="matching">Nối đáp án</option>
                                        <option value="ordering">Sắp xếp đáp án</option>
                                    </select>
                                </div>
                                <div class="config-field">
                                    <label class="config-label">Ngữ cảnh</label>
                                    <select name="question_configs[0][context_type]" class="form-select form-select-sm">
                                        <option value="">Tất cả</option>
                                        <option value="normal">Thường</option>
                                        <option value="reading">Đọc hiểu</option>
                                        <option value="listening">Nghe</option>
                                    </select>
                                </div>
                                <div class="config-field config-field-quantity">
                                    <label class="config-label">Số câu</label>
                                    <input type="number" name="question_configs[0][quantity]" min="1" value="5" class="form-control form-control-sm">
                                </div>
                                <div class="config-actions">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addConfigRow()">
                            <i class="bi bi-plus-lg me-1"></i>Thêm cấu hình
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hướng dẫn</label>
                        <textarea name="instructions" class="form-control" rows="3">{{ old('instructions') }}</textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hạn nộp <small class="text-muted">(không bắt buộc)</small></label>
                            <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}">
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Điểm tối đa</label>
                            <input type="number" name="max_score" min="0" max="1000" step="1" class="form-control @error('max_score') is-invalid @enderror" value="{{ old('max_score', 10) }}">
                            @error('max_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="assignment-submit-actions d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Giao bài</button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .assignment-config-panel {
        border-color: #dbe4f0 !important;
    }

    .config-list {
        display: grid;
        gap: 10px;
    }

    .config-row {
        display: grid;
        grid-template-columns: minmax(220px, 1.45fr) minmax(150px, 1fr) minmax(130px, .9fr) 90px 42px;
        gap: 10px;
        align-items: end;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
    }

    .config-field {
        min-width: 0;
    }

    .config-label {
        display: block;
        margin-bottom: 5px;
        font-size: .76rem;
        font-weight: 800;
        color: #64748b;
    }

    .config-actions {
        display: flex;
        justify-content: flex-end;
    }

    .config-actions .btn {
        width: 38px;
        min-height: 32px;
    }

    @media (max-width: 767.98px) {
        .assignment-create-body {
            padding: 14px !important;
        }

        .assignment-config-panel {
            padding: 12px !important;
        }

        .config-row {
            grid-template-columns: 1fr;
            gap: 9px;
            padding: 12px;
        }

        .config-actions {
            justify-content: stretch;
        }

        .config-actions .btn {
            width: 100%;
        }

        .assignment-submit-actions {
            display: grid !important;
            grid-template-columns: 1fr;
        }

        .assignment-submit-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
let configIndex = 1;

function addConfigRow() {
    const list = document.querySelector('#configList');
    const row = document.createElement('div');
    row.className = 'config-row';
    row.innerHTML = `
        <div class="config-field config-field-category">
            <label class="config-label">Danh mục</label>
            <select name="question_configs[${configIndex}][category_id]" class="form-select form-select-sm">
                <option value="">-- Tất cả --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">L{{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="config-field">
            <label class="config-label">Kiểu câu hỏi</label>
            <select name="question_configs[${configIndex}][question_type]" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="select">Chọn đáp án</option>
                <option value="input">Nhập đáp án</option>
                <option value="matching">Nối đáp án</option>
                <option value="ordering">Sắp xếp đáp án</option>
            </select>
        </div>
        <div class="config-field">
            <label class="config-label">Ngữ cảnh</label>
            <select name="question_configs[${configIndex}][context_type]" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="normal">Thường</option>
                <option value="reading">Đọc hiểu</option>
                <option value="listening">Nghe</option>
            </select>
        </div>
        <div class="config-field config-field-quantity">
            <label class="config-label">Số câu</label>
            <input type="number" name="question_configs[${configIndex}][quantity]" min="1" value="5" class="form-control form-control-sm">
        </div>
        <div class="config-actions">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>`;
    list.appendChild(row);
    configIndex++;
}

function removeRow(btn) {
    const rows = document.querySelectorAll('#configList .config-row');
    if (rows.length === 1) return;
    btn.closest('.config-row').remove();
}
</script>
@endpush
