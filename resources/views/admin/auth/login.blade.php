<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&#272;&#259;ng nh&#7853;p Admin - LinhTrang</title>
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
            <div><h4 class="fw-bold mb-3">LinhTrang Admin</h4><p class="mb-0 opacity-75">H&#7879; th&#7889;ng qu&#7843;n tr&#7883; l&#7899;p h&#7885;c, b&#224;i t&#7853;p v&#224; ki&#7875;m tra.</p></div>
            <small class="opacity-75">Ch&#7881; t&#224;i kho&#7843;n Admin/Teacher m&#7899;i &#273;&#432;&#7907;c truy c&#7853;p.</small>
        </div>
        <div class="col-md-7 p-4 p-md-5">
            <h4 class="fw-bold mb-1">&#272;&#259;ng nh&#7853;p</h4>
            <p class="text-muted mb-4">Nh&#7853;p t&#224;i kho&#7843;n &#273;&#7875; truy c&#7853;p trang qu&#7843;n tr&#7883;.</p>
            @if(session('error'))<div class="alert alert-warning py-2">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger py-2">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('admin.login') }}" class="vstack gap-3">
                @csrf
                <div><label class="form-label small fw-semibold">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
                <div><label class="form-label small fw-semibold">M&#7853;t kh&#7849;u</label><input type="password" name="password" class="form-control" required></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"><label class="form-check-label" for="remember">Ghi nh&#7899; &#273;&#259;ng nh&#7853;p</label></div>
                <button class="btn btn-primary w-100">&#272;&#259;ng nh&#7853;p</button>
            </form>
            @if(!empty($canPublicRegister))<div class="mt-3 text-center"><a href="{{ route('admin.register.form') }}" class="small">Ch&#432;a c&#243; admin &#273;&#7847;u ti&#234;n? &#272;&#259;ng k&#253; ngay</a></div>@endif
            <div class="mt-2 text-center"><a href="/" class="small text-muted">V&#7873; trang h&#7885;c vi&#234;n</a></div>
        </div>
    </div>
</div>
</body>
</html>