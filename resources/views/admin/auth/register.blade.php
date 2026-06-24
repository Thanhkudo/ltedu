<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&#272;&#259;ng k&#253; Admin - LinhTrang</title>
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
    <h4 class="fw-bold mb-1">{!! $bootstrapMode ? 'T&#7841;o admin &#273;&#7847;u ti&#234;n' : 'T&#7841;o t&#224;i kho&#7843;n qu&#7843;n tr&#7883;' !!}</h4>
    <p class="text-muted mb-4">{!! $bootstrapMode ? 'H&#7879; th&#7889;ng ch&#432;a c&#243; admin. T&#224;i kho&#7843;n n&#224;y s&#7869; &#273;&#432;&#7907;c g&#225;n quy&#7873;n Admin.' : 'Ch&#7881; admin hi&#7879;n t&#7841;i m&#7899;i &#273;&#432;&#7907;c t&#7841;o th&#234;m t&#224;i kho&#7843;n qu&#7843;n tr&#7883;.' !!}</p>
    @if($errors->any())<div class="alert alert-danger py-2">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('admin.register') }}" class="vstack gap-3">
        @csrf
        <div><label class="form-label small fw-semibold">H&#7885; t&#234;n</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
        <div><label class="form-label small fw-semibold">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
        @if($canSelectRole)
            <div><label class="form-label small fw-semibold">Vai tr&#242;</label><select name="role" class="form-select" required><option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option><option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option></select></div>
        @else
            <input type="hidden" name="role" value="admin"><div class="alert alert-info py-2 mb-0">Vai tr&#242; &#273;&#432;&#7907;c g&#225;n t&#7921; &#273;&#7897;ng: <strong>Admin</strong></div>
        @endif
        <div class="row g-3"><div class="col-md-6"><label class="form-label small fw-semibold">M&#7853;t kh&#7849;u</label><input type="password" name="password" class="form-control" required></div><div class="col-md-6"><label class="form-label small fw-semibold">X&#225;c nh&#7853;n m&#7853;t kh&#7849;u</label><input type="password" name="password_confirmation" class="form-control" required></div></div>
        <button class="btn btn-primary w-100 mt-2">{!! $bootstrapMode ? 'T&#7841;o admin v&#224; &#273;&#259;ng nh&#7853;p' : 'T&#7841;o t&#224;i kho&#7843;n qu&#7843;n tr&#7883;' !!}</button>
    </form>
    <div class="text-center mt-3">@if($bootstrapMode)<a href="{{ route('admin.login.form') }}" class="small">&#272;&#227; c&#243; t&#224;i kho&#7843;n? &#272;&#259;ng nh&#7853;p</a>@else<a href="{{ route('admin.dashboard') }}" class="small">Quay l&#7841;i trang qu&#7843;n tr&#7883;</a>@endif</div>
</div>
</body>
</html>