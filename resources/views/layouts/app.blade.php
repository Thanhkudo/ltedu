<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('ui.student_portal')) - LinhTrang School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&subset=vietnamese&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root{--purple:#6c63ff;--pink:#ff6b9d;--teal:#00cba9;--orange:#ff9a3c;--bg:#f0f1ff}
        :root{--bs-body-font-family:'Montserrat',system-ui,-apple-system,'Segoe UI',sans-serif}
        *{font-family:'Montserrat',system-ui,-apple-system,'Segoe UI',sans-serif}
        body{background:var(--bg);padding-bottom:85px;min-height:100vh}
        .s-topbar{background:linear-gradient(135deg,#6c63ff 0%,#a855f7 100%);padding:10px 16px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 4px 20px rgba(108,99,255,.35)}
        .s-topbar .brand{color:#fff;font-weight:900;font-size:1.15rem;text-decoration:none;display:flex;align-items:center;gap:6px}
        .s-topbar .brand .logo-pill{background:rgba(255,255,255,.25);border-radius:10px;padding:3px 10px}
        .language-switcher{display:flex;align-items:center;border:1px solid rgba(255,255,255,.38);border-radius:10px;overflow:hidden;background:rgba(255,255,255,.12)}
        .language-switcher a{color:rgba(255,255,255,.76);font-size:.7rem;font-weight:900;padding:5px 8px;text-decoration:none;line-height:1}
        .language-switcher a.active{background:#fff;color:var(--purple)}
        .s-avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#ffd166,#ff6b9d);display:flex;align-items:center;justify-content:center;font-weight:900;color:#fff;font-size:.9rem;border:2px solid rgba(255,255,255,.6);flex-shrink:0}
        .s-bottom-nav{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:2px solid #ede9fe;display:flex;z-index:200;box-shadow:0 -4px 20px rgba(108,99,255,.12)}
        .s-bottom-nav a{flex:1;display:flex;flex-direction:column;align-items:center;padding:8px 4px 6px;color:#9ca3af;text-decoration:none;font-size:.62rem;font-weight:800;transition:all .2s;gap:2px;position:relative}
        .s-bottom-nav a i{font-size:1.35rem}
        .s-bottom-nav a.active{color:var(--purple)}
        .s-bottom-nav a:hover{color:var(--purple)}
        .s-nav-pip{width:5px;height:5px;background:var(--purple);border-radius:50%;position:absolute;bottom:4px}
        .s-card{background:#fff;border-radius:20px;box-shadow:0 2px 16px rgba(108,99,255,.08);border:none;overflow:hidden;transition:transform .2s,box-shadow .2s}
        .s-card:hover{transform:translateY(-3px);box-shadow:0 8px 30px rgba(108,99,255,.15)}
        .s-stat{border-radius:20px;padding:18px 20px;color:#fff;position:relative;overflow:hidden}
        .s-stat .bg-blob{position:absolute;border-radius:50%;background:rgba(255,255,255,.15)}
        .s-stat .blob1{width:80px;height:80px;top:-20px;right:-20px}
        .s-stat .blob2{width:110px;height:110px;bottom:-40px;right:-10px}
        .s-stat .num{font-size:2.8rem;font-weight:900;line-height:1;position:relative;z-index:1}
        .s-stat .lbl{font-size:.78rem;font-weight:800;opacity:.9;position:relative;z-index:1}
        .btn-app{border:none;border-radius:14px;font-weight:800;padding:10px 22px;display:inline-flex;align-items:center;gap:7px;transition:all .2s;font-size:.9rem;cursor:pointer}
        .btn-app:hover{transform:translateY(-2px)}
        .btn-purple{background:linear-gradient(135deg,#6c63ff,#a855f7);color:#fff;box-shadow:0 4px 14px rgba(108,99,255,.4)}
        .btn-purple:hover{color:#fff;box-shadow:0 6px 20px rgba(108,99,255,.5)}
        .btn-green{background:linear-gradient(135deg,#00cba9,#38ef7d);color:#fff;box-shadow:0 4px 14px rgba(0,203,169,.35)}
        .btn-green:hover{color:#fff}
        .btn-orange{background:linear-gradient(135deg,#ff9a3c,#ff6b9d);color:#fff;box-shadow:0 4px 14px rgba(255,107,157,.35)}
        .btn-orange:hover{color:#fff}
        .btn-light-purple{background:#f0edff;color:var(--purple);border:none;border-radius:14px;font-weight:800;padding:8px 18px}
        .btn-light-purple:hover{background:#e5e0ff;color:var(--purple)}
        .s-tag{display:inline-block;padding:3px 12px;border-radius:20px;font-size:.72rem;font-weight:800}
        .tag-purple{background:#f0edff;color:#6c63ff}
        .tag-green{background:#ecfdf5;color:#00a882}
        .tag-orange{background:#fff7ed;color:#ea6d0a}
        .tag-red{background:#fff1f2;color:#e11d48}
        .tag-blue{background:#eff6ff;color:#2563eb}
        .s-toast{border-radius:16px;padding:12px 16px;font-weight:700;border:none;margin-bottom:12px}
        .s-section-title{font-size:.85rem;font-weight:900;color:#374151;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:6px}
        .s-section-title::before{content:'';width:4px;height:14px;background:linear-gradient(135deg,var(--purple),var(--pink));border-radius:4px;flex-shrink:0}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-7px)}}
        @keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        @keyframes pop{0%{transform:scale(.9)}70%{transform:scale(1.05)}100%{transform:scale(1)}}
        .float-anim{animation:float 3s ease-in-out infinite}
        .slide-up{animation:slideUp .4s ease both}
        .pop-in{animation:pop .35s ease both}
        .s-list-item{background:#fff;border-radius:16px;padding:14px 16px;margin-bottom:10px;display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;box-shadow:0 2px 10px rgba(108,99,255,.06);transition:all .2s;border:2px solid transparent}
        .s-list-item:hover{border-color:#e0d9ff;transform:translateX(3px);color:inherit}
        .s-list-icon{width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0}
    </style>
    @stack('styles')
</head>
<body>

<div class="s-topbar">
    <a href="/" class="brand"><span class="logo-pill">&#x1F4DA;</span>LinhTrang</a>
    @if(session('student_id'))
        @php $__topStudent = \App\Models\Student::find(session('student_id')); @endphp
        <div class="d-flex align-items-center gap-2">
            <div class="language-switcher" aria-label="{{ __('ui.language') }}">
                <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('language.switch', 'vi') }}" class="{{ app()->getLocale() === 'vi' ? 'active' : '' }}">VI</a>
            </div>
            <a href="{{ route('guide') }}" style="color:#fff;font-size:.78rem;font-weight:800;text-decoration:none;background:rgba(255,255,255,.2);padding:6px 10px;border-radius:10px;">
                <i class="bi bi-question-circle"></i> {{ __('ui.guide.menu') }}
            </a>
            <div class="s-avatar">{{ mb_substr($__topStudent->full_name ?? 'U', 0, 1) }}</div>
            <div style="color:#fff;line-height:1.2">
                <div style="font-size:.7rem;opacity:.8">{{ __('ui.hello') }} &#x1F44B;</div>
                <div style="font-size:.82rem;font-weight:800">{{ $__topStudent->full_name ?? '' }}</div>
            </div>
            <form method="POST" action="/logout-student" class="m-0 ms-1">
                @csrf
                <button style="background:rgba(255,255,255,.2);color:#fff;border:none;border-radius:10px;font-size:.72rem;font-weight:800;padding:5px 10px;cursor:pointer;">
                    &#x21A9; {{ __('ui.switch_student') }}
                </button>
            </form>
        </div>
    @else
        <div class="d-flex align-items-center gap-2">
            <div class="language-switcher" aria-label="{{ __('ui.language') }}">
                <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('language.switch', 'vi') }}" class="{{ app()->getLocale() === 'vi' ? 'active' : '' }}">VI</a>
            </div>
            <a href="{{ route('guide') }}" style="color:#fff;font-size:.78rem;font-weight:800;text-decoration:none;background:rgba(255,255,255,.2);padding:6px 10px;border-radius:10px;">
                <i class="bi bi-question-circle"></i> {{ __('ui.guide.menu') }}
            </a>
            <a href="/admin" style="color:#fff;font-size:.78rem;font-weight:800;text-decoration:none;background:rgba(255,255,255,.2);padding:6px 12px;border-radius:10px;">
                &#x1F6E1;&#xFE0F; {{ __('ui.admin') }}
            </a>
        </div>
    @endif
</div>

<div class="px-3 py-3 mx-auto" style="max-width:1200px">
    @if(session('success'))
        <div class="alert s-toast alert-success alert-dismissible fade show slide-up">
            &#x2705; {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert s-toast alert-danger alert-dismissible fade show slide-up">
            &#x274C; {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @yield('content')
</div>

@if(session('student_id'))
@php $__navStudent = \App\Models\Student::with('classes')->find(session('student_id')); @endphp
<nav class="s-bottom-nav">
    <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">
        <i class="bi bi-house-heart-fill"></i>
        {{ __('ui.home') }}
        @if(request()->is('/')) <span class="s-nav-pip"></span> @endif
    </a>
    @if($__navStudent)
        @foreach($__navStudent->classes->take(3) as $__navClass)
        <a href="/classes/{{ $__navClass->id }}" class="{{ request()->is('classes/'.$__navClass->id) ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark-fill"></i>
            {{ \Illuminate\Support\Str::limit($__navClass->name, 6, '...') }}
            @if(request()->is('classes/'.$__navClass->id)) <span class="s-nav-pip"></span> @endif
        </a>
        @endforeach
    @endif
</nav>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
