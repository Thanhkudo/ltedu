@extends('layouts.app')
@section('title', 'Chao mung den LinhTrang')

@push('styles')
<style>
    body { background: linear-gradient(160deg,#6c63ff 0%,#a855f7 45%,#ff6b9d 100%) !important; min-height:100vh; }
    .s-topbar { background: rgba(0,0,0,.15) !important; box-shadow: none !important; }
    .pick-card { background:#fff; border-radius:28px; padding:36px 32px; box-shadow:0 20px 60px rgba(0,0,0,.2); max-width:400px; margin:0 auto; }
    .pick-emoji { font-size:4rem; }
    .pick-select { border:2px solid #e0d9ff; border-radius:16px; padding:12px 16px; font-weight:700; font-size:.95rem; }
    .pick-select:focus { border-color:#6c63ff; box-shadow:0 0 0 4px rgba(108,99,255,.15); outline:none; }
    .pick-btn { width:100%; padding:14px; font-size:1rem; font-weight:900; letter-spacing:.3px; border-radius:16px; }
    .floating-shape { position:fixed; border-radius:50%; opacity:.12; pointer-events:none; }
    .shape1 { width:200px;height:200px;background:#fff;top:-60px;right:-60px; }
    .shape2 { width:150px;height:150px;background:#ffd166;bottom:100px;left:-50px; }
    .shape3 { width:80px;height:80px;background:#fff;bottom:180px;right:30px; }
</style>
@endpush

@section('content')
<div class="floating-shape shape1 float-anim"></div>
<div class="floating-shape shape2 float-anim" style="animation-delay:.8s"></div>
<div class="floating-shape shape3 float-anim" style="animation-delay:1.4s"></div>

<div class="pick-card slide-up" style="margin-top:24px">
    <div class="text-center mb-4">
        <div class="pick-emoji mb-2">&#x1F393;</div>
        <h2 style="font-weight:900;color:#1f2937;font-size:1.6rem">Chao mung ban!</h2>
        <p style="color:#6b7280;font-weight:600;font-size:.9rem">Nhap ma vao hoc de bat dau &#x1F60A;</p>
    </div>

    <form method="POST" action="{{ route('student.pick') }}">
        @csrf
        <div class="mb-4">
            <label style="font-weight:800;color:#374151;font-size:.85rem;margin-bottom:8px;display:block">&#x1F511; Ma vao hoc</label>
            <input
                type="text"
                name="entry_code"
                value="{{ old('entry_code') }}"
                class="form-control pick-select @error('entry_code') is-invalid @enderror"
                placeholder="Vi du: HV0001"
                required
            >
            @error('entry_code')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-app btn-purple pick-btn">
            <i class="bi bi-box-arrow-in-right"></i> Vao hoc thoi!
        </button>
    </form>

    <div class="text-center mt-4 pt-3" style="border-top:1px solid #f3f0ff">
        <a href="/admin" style="color:#9ca3af;font-size:.8rem;font-weight:700;text-decoration:none">
            &#x1F6E1;&#xFE0F; Giao vien / Admin
        </a>
    </div>
</div>
@endsection
