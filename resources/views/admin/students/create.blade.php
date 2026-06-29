@extends('layouts.admin')
@section('title', 'Thêm học viên')
@section('page-title', 'Thêm học viên')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header bg-white fw-semibold">Thông tin học viên</div>
    <div class="card-body">
        <form method="POST" action="/admin/students">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Ho ten <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                       value="{{ old('full_name') }}" required>
                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Mã học viên</label>
                    <input type="text" name="student_code" class="form-control @error('student_code') is-invalid @enderror"
                           value="{{ old('student_code') }}" placeholder="Tu dong sinh neu de trong">
                    @error('student_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">So dien thoai</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Dia chi</label>
                <textarea name="address" rows="2" class="form-control">{{ old('address') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Lưu</button>
                <a href="/admin/students" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

