@extends('layouts.admin')
@section('title', 'Chỉnh sửa bài tập')
@section('page-title', 'Chỉnh sửa bài tập')
@section('page-actions')
    <a href="/admin/exercises" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Trở về</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="/admin/exercises/{{ $exercise->id }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $exercise->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Loại bài tập <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach(['reading','writing','listening','speaking','grammar','vocabulary'] as $type)
                                    <option value="{{ $type }}" {{ old('type', $exercise->type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Độ khó <span class="text-danger">*</span></label>
                            <select name="difficulty" class="form-select @error('difficulty') is-invalid @enderror" required>
                                @foreach(['easy','medium','hard'] as $d)
                                    <option value="{{ $d }}" {{ old('difficulty', $exercise->difficulty) == $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
                                @endforeach
                            </select>
                            @error('difficulty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả ngắn</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $exercise->description) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nội dung bài tập <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                                  rows="8" required>{{ old('content', $exercise->content) }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Cập nhật</button>
                        <a href="/admin/exercises" class="btn btn-outline-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
