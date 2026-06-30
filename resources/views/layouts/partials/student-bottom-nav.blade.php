@if(session('student_id'))
@php $__navStudent = \App\Models\Student::with('classes')->find(session('student_id')); @endphp
<nav class="s-bottom-nav">
    <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">
        <i class="bi bi-house-heart-fill"></i>
        {{ __('ui.home') }}
        @if(request()->is('/')) <span class="s-nav-pip"></span> @endif
    </a>
    <a href="{{ route('guide') }}" class="{{ request()->is('huong-dan') ? 'active' : '' }}">
        <i class="bi bi-question-circle-fill"></i>
        {{ __('ui.guide.menu') }}
        @if(request()->is('huong-dan')) <span class="s-nav-pip"></span> @endif
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
