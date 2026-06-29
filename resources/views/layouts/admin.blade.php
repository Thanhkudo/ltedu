<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('ui.admin')) - LTEdu Admin</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&subset=vietnamese&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bs-body-font-family: 'Montserrat', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }

        body {
            background: #f0f2f5;
            font-family: 'Montserrat', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }

        button, input, select, textarea {
            font-family: inherit;
        }

        .admin-sidebar {
            width: 240px;
            min-height: 100vh;
            background: #1e2a3b;
            color: #cdd4de;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .admin-sidebar .brand {
            padding: 20px 20px 10px;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid #2e3f56;
        }

        .admin-sidebar .nav-link {
            color: #cdd4de;
            padding: 9px 20px;
            border-radius: 0;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: #2e3f56;
            color: #fff;
        }

        .admin-sidebar .nav-section {
            padding: 14px 20px 4px;
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 1px;
        }

        .admin-content {
            margin-left: 240px;
            min-height: 100vh;
        }

        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .admin-topbar h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
        }

        .card {
            border: none;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        }

        .table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #64748b;
        }

        .badge-active {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-inactive {
            background: #f1f5f9;
            color: #64748b;
        }

        .badge-completed {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-draft {
            background: #fef9c3;
            color: #92400e;
        }

        .badge-published {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-closed {
            background: #f1f5f9;
            color: #64748b;
        }

        .field-error-label {
            display: block;
            margin-top: 6px;
            font-size: .82rem;
            font-weight: 600;
            color: #dc3545;
        }
    </style>
    @stack('styles')
</head>

<body>
    @php
        $adminUser = auth()->user();
        $roleModules = [
            'admin' => [
                'dashboard',
                'students',
                'classes',
                'sessions',
                'assignments',
                'question-bank',
                'question-categories',
            ],
            'teacher' => [
                'dashboard',
                'classes',
                'sessions',
                'assignments',
                'question-bank',
                'question-categories',
            ],
        ];
        $allowed = $roleModules[$adminUser->role ?? ''] ?? [];
    @endphp
    {{-- Sidebar --}}
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

    {{-- Main content --}}
    <div class="admin-content">
        <div class="admin-topbar">
            <h5>@yield('page-title', 'Dashboard')</h5>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-light text-dark border">
                    {{ $adminUser->name ?? 'User' }} ({{ strtoupper($adminUser->role ?? 'n/a') }})
                </span>
                <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-1"></i>{{ __('ui.logout') }}
                    </button>
                </form>
                @yield('page-actions')
            </div>
        </div>

        <div class="p-4">
            {{-- Flash messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Vui lòng kiểm tra các trường được đánh dấu bên dưới.
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @if ($errors->any())
        <script>
            (function() {
                const errors = @json($errors->toArray());

                function toBracketName(key) {
                    const parts = key.split('.');
                    return parts.reduce(function(result, part, index) {
                        return index === 0 ? part : result + '[' + part + ']';
                    }, '');
                }

                function findField(key) {
                    const exactSelector = '[name="' + key.replace(/"/g, '\"') + '"]';
                    let field = document.querySelector(exactSelector);

                    if (field) {
                        return field;
                    }

                    const bracketName = toBracketName(key);
                    const bracketSelector = '[name="' + bracketName.replace(/"/g, '\"') + '"]';
                    return document.querySelector(bracketSelector);
                }

                Object.keys(errors).forEach(function(key) {
                    const field = findField(key);
                    if (!field) {
                        return;
                    }

                    field.classList.add('is-invalid');

                    const parent = field.parentElement;
                    if (!parent) {
                        return;
                    }

                    const hasExistingFeedback = parent.querySelector('.invalid-feedback');
                    if (hasExistingFeedback) {
                        hasExistingFeedback.classList.add('d-block');
                        return;
                    }

                    if (parent.querySelector('[data-auto-error-for="' + key + '"]')) {
                        return;
                    }

                    const label = document.createElement('label');
                    label.className = 'field-error-label';
                    label.setAttribute('data-auto-error-for', key);
                    label.textContent = errors[key][0];
                    field.insertAdjacentElement('afterend', label);
                });

                const firstInvalid = document.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            })();
        </script>
    @endif
    @stack('scripts')
</body>

</html>
