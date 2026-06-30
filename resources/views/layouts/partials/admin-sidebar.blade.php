<div class="admin-sidebar">
    <div class="brand"><i class="bi bi-shield-check me-2"></i>LTEdu Admin</div>
    <nav class="nav flex-column mt-2">
        <span class="nav-section">{{ __('ui.overview') }}</span>
        @if (in_array('dashboard', $allowed))
            <a class="nav-link {{ request()->is('admin') ? 'active' : '' }}" href="/admin">
                <i class="bi bi-grid-1x2-fill"></i> {{ __('ui.overview') }}
            </a>
        @endif

        <span class="nav-section">{{ __('ui.management') }}</span>
        @if (in_array('students', $allowed))
            <a class="nav-link {{ request()->is('admin/students*') ? 'active' : '' }}" href="/admin/students">
                <i class="bi bi-people-fill"></i> {{ __('ui.students') }}
            </a>
        @endif
        @if (in_array('classes', $allowed))
            <a class="nav-link {{ request()->is('admin/classes*') ? 'active' : '' }}" href="/admin/classes">
                <i class="bi bi-journal-bookmark-fill"></i> {{ __('ui.classes') }}
            </a>
        @endif
        @if ($adminUser->role === 'admin')
            <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="/admin/users">
                <i class="bi bi-people-fill"></i> {{ __('ui.users') }}
            </a>
        @endif
        @if (in_array('assignments', $allowed))
            <a class="nav-link {{ request()->is('admin/assignments*') ? 'active' : '' }}"
                href="/admin/assignments/create">
                <i class="bi bi-clipboard2-check-fill"></i> {{ __('ui.assignments') }}
            </a>
        @endif
        @if (in_array('question-bank', $allowed))
            <a class="nav-link {{ request()->is('admin/question-bank*') ? 'active' : '' }}"
                href="{{ route('admin.question-bank.index') }}">
                <i class="bi bi-collection-fill"></i> {{ __('ui.question_bank') }}
            </a>
        @endif
        @if (in_array('question-categories', $allowed))
            <a class="nav-link {{ request()->is('admin/question-categories*') ? 'active' : '' }}"
                href="{{ route('admin.question-categories.index') }}">
                <i class="bi bi-tags-fill"></i> {{ __('ui.question_categories') }}
            </a>
        @endif

        <span class="nav-section">{{ __('ui.system') }}</span>
        <a class="nav-link {{ request()->is('admin/huong-dan') ? 'active' : '' }}" href="{{ route('admin.guide') }}">
            <i class="bi bi-question-circle-fill"></i> {{ __('ui.guide.admin_menu') }}
        </a>
        <a class="nav-link" href="/"><i class="bi bi-box-arrow-left"></i> {{ __('ui.student_site') }}</a>
    </nav>
</div>
