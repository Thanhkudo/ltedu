<div class="s-topbar">
    <a href="/" class="brand"><span class="logo-pill">&#x1F4DA;</span>LTEdu</a>
    @if(session('student_id'))
        @php $__topStudent = \App\Models\Student::find(session('student_id')); @endphp
        <div class="d-flex align-items-center gap-2">
            <div class="language-switcher" aria-label="{{ __('ui.language') }}">
                <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('language.switch', 'vi') }}" class="{{ app()->getLocale() === 'vi' ? 'active' : '' }}">VI</a>
            </div>
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
