@extends('layouts.admin')
@section('title', 'Cập nhật người dùng')
@section('page-title', 'Cập nhật người dùng')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header bg-white fw-semibold">Thông tin người dùng</div>
    <div class="card-body">
        <form method="POST" action="/admin/users/{{ $user->id }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Họ tên <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Vai trò <span class="text-danger">*</span></label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="teacher" {{ old('role', $user->role) === 'teacher' ? 'selected' : '' }}>Giáo viên</option>
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Mật khẩu mới</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Lưu thay đổi</button>
                <a href="/admin/users" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

