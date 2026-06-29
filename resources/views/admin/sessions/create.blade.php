@extends('layouts.admin')
@section('title', 'Thêm buổi học')
@section('page-title', 'Thêm buổi học - ' . $class->name)

@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/classes/{{ $class->id }}/sessions">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Tiêu đề buổi học <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Ngày học <span class="text-danger">*</span></label>
                <input type="date" name="session_date"
                       class="form-control @error('session_date') is-invalid @enderror"
                       value="{{ old('session_date') }}" required>
                @error('session_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label">Mô tả</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Lưu</button>
                <a href="/admin/classes/{{ $class->id }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection

