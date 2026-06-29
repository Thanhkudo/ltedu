@extends('layouts.admin')
@section('title', 'Sửa lớp học')
@section('page-title', 'Sửa lớp học: ' . $class->name)

@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/classes/{{ $class->id }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Tên lớp <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $class->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Giáo viên <span class="text-danger">*</span></label>
                <select name="teacher_id" class="form-select" required>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $class->teacher_id) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control"
                           value="{{ old('start_date', $class->start_date->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ old('end_date', $class->end_date ? $class->end_date->format('Y-m-d') : '') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    @foreach(['active','inactive','completed'] as $s)
                        <option value="{{ $s }}" {{ old('status', $class->status) == $s ? 'selected' : '' }}>{{ ['active' => 'Đang hoạt động', 'inactive' => 'Tạm dừng', 'completed' => 'Đã hoàn thành'][$s] ?? ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Mô tả</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description', $class->description) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Cập nhật</button>
                <a href="/admin/classes/{{ $class->id }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection

