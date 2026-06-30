<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.admin_login') }} - LTEdu</title>
    @include('layouts.partials.meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&subset=vietnamese&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { min-height: 100vh; font-family: 'Montserrat', system-ui, -apple-system, 'Segoe UI', sans-serif; background: radial-gradient(circle at 20% 20%, #4f46e5 0%, #312e81 40%, #0f172a 100%); }
        .auth-card { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(2, 6, 23, .4); overflow: hidden; }
        .auth-left { background: linear-gradient(135deg, #0ea5e9 0%, #4f46e5 100%); color: #fff; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
<div class="container" style="max-width: 980px;">
    <div class="row g-0 auth-card bg-white">
        <div class="col-md-5 auth-left p-4 p-md-5 d-flex flex-column justify-content-between">
            <div><h4 class="fw-bold mb-3">LTEdu Admin</h4><p class="mb-0 opacity-75">{{ __('ui.admin_login_intro') }}</p></div>
            <small class="opacity-75">{{ __('ui.admin_only') }}</small>
        </div>
        <div class="col-md-7 p-4 p-md-5">
            <h4 class="fw-bold mb-1">{{ __('ui.sign_in') }}</h4>
            <p class="text-muted mb-4">{{ __('ui.sign_in_hint') }}</p>
            @if(session('error'))<div class="alert alert-warning py-2">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger py-2">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('admin.login') }}" class="vstack gap-3">
                @csrf
                <div><label class="form-label small fw-semibold">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
                <div><label class="form-label small fw-semibold">{{ __('ui.password') }}</label><input type="password" name="password" class="form-control" required></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"><label class="form-check-label" for="remember">{{ __('ui.remember_me') }}</label></div>
                <button class="btn btn-primary w-100">{{ __('ui.sign_in') }}</button>
            </form>
            @if(!empty($canPublicRegister))<div class="mt-3 text-center"><a href="{{ route('admin.register.form') }}" class="small">Chưa có admin đầu tiên? Đăng ký ngay</a></div>@endif
            <div class="mt-2 text-center"><a href="/" class="small text-muted">{{ __('ui.back_to_student_site') }}</a></div>
        </div>
    </div>
</div>
</body>
</html>
