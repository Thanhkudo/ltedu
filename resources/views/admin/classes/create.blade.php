@extends('layouts.admin')
@section('title', 'Thêm lớp học')
@section('page-title', 'Thêm lớp học')

@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card">
    <div class="card-header bg-white fw-semibold">Thông tin lớp học</div>
    <div class="card-body">
        <form method="POST" action="/admin/classes">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Tên lớp <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Mã lớp</label>
                <input type="text" name="class_code" class="form-control" value="{{ old('class_code') }}"
                       placeholder="Tự động sinh nếu để trống">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Giáo viên <span class="text-danger">*</span></label>
                <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                    <option value="">-- Chọn giáo viên --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
                @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                           value="{{ old('start_date') }}" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Mô tả</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Lưu</button>
                <a href="/admin/classes" class="btn btn-outline-secondary">Huỷ</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection
