<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.create_admin_account') }} - LTEdu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&subset=vietnamese&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { min-height: 100vh; font-family: 'Montserrat', system-ui, -apple-system, 'Segoe UI', sans-serif; background: linear-gradient(160deg, #0f172a 0%, #1d4ed8 45%, #0ea5e9 100%); }
        .register-card { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(2, 6, 23, .4); }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
<div class="card register-card p-4 p-md-5" style="max-width: 560px; width: 100%;">
    @php
        $bootstrapMode = isset($bootstrapMode) ? (bool) $bootstrapMode : false;
        $canSelectRole = isset($canSelectRole) ? (bool) $canSelectRole : false;
    @endphp
    <h4 class="fw-bold mb-1">{{ $bootstrapMode ? __('ui.create_first_admin') : __('ui.create_admin_account') }}</h4>
    <p class="text-muted mb-4">{{ $bootstrapMode ? __('ui.first_admin_hint') : __('ui.admin_account_hint') }}</p>
    @if($errors->any())<div class="alert alert-danger py-2">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('admin.register') }}" class="vstack gap-3">
        @csrf
        <div><label class="form-label small fw-semibold">{{ __('ui.full_name') }}</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
        <div><label class="form-label small fw-semibold">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
        @if($canSelectRole)
            <div><label class="form-label small fw-semibold">{{ __('ui.role') }}</label><select name="role" class="form-select" required><option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option><option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option></select></div>
        @else
            <input type="hidden" name="role" value="admin"><div class="alert alert-info py-2 mb-0">Vai trò được gán tự động: <strong>Admin</strong></div>
        @endif
        <div class="row g-3"><div class="col-md-6"><label class="form-label small fw-semibold">{{ __('ui.password') }}</label><input type="password" name="password" class="form-control" required></div><div class="col-md-6"><label class="form-label small fw-semibold">{{ __('ui.confirm_password') }}</label><input type="password" name="password_confirmation" class="form-control" required></div></div>
        <button class="btn btn-primary w-100 mt-2">{{ $bootstrapMode ? __('ui.create_and_sign_in') : __('ui.create_admin_account') }}</button>
    </form>
    <div class="text-center mt-3">@if($bootstrapMode)<a href="{{ route('admin.login.form') }}" class="small">{{ __('ui.already_have_account') }}</a>@else<a href="{{ route('admin.dashboard') }}" class="small">{{ __('ui.back_to_admin') }}</a>@endif</div>
</div>
</body>
</html>
