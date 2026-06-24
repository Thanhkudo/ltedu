@extends('layouts.admin')
@section('title', 'Tạo phiên kiểm tra')
@section('page-title', 'Tạo phiên kiểm tra')
@section('page-actions')
    <a href="{{ route('admin.test-sessions.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Trở về</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.test-sessions.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Đề thi <span class="text-danger">*</span></label>
                        <select name="test_id" class="form-select @error('test_id') is-invalid @enderror" required>
                            <option value="">-- Chọn đề thi --</option>
                            @foreach($tests as $test)
                                <option value="{{ $test->id }}" data-class="{{ $test->class_id }}" data-duration="{{ $test->duration }}" {{ (string) old('test_id', optional($selectedTest)->id) === (string) $test->id ? 'selected' : '' }}>
                                    {{ $test->title }} - {{ $test->schoolClass->name ?? 'Chưa rõ lớp' }} - {{ $test->questions_count }} câu
                                </option>
                            @endforeach
                        </select>
                        @error('test_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lớp áp dụng <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">-- Chọn lớp --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (string) old('class_id', optional($selectedTest)->class_id) === (string) $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên phiên</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Ví dụ: Kiểm tra 15 phút tuần 3">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Thời gian làm bài</label>
                            <input type="number" name="duration" min="1" max="300" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration', optional($selectedTest)->duration) }}" placeholder="Theo đề">
                            @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}" required>
                            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at') }}" required>
                            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Mở ngay</option>
                            <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Đóng</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Tạo phiên</button>
                        <a href="{{ route('admin.test-sessions.index') }}" class="btn btn-outline-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
