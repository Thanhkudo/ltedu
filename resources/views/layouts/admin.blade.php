<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('ui.admin')) - LTEdu Admin</title>
    @include('layouts.partials.meta')
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
            overflow-y: auto;
        }

        .admin-sidebar-desktop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .admin-mobile-menu {
            width: 290px;
            max-width: 88vw;
            border: 0;
            background: #1e2a3b;
        }

        .admin-sidebar-mobile {
            width: 100%;
            min-height: 100%;
            position: static;
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
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .admin-topbar h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .admin-title-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .admin-menu-toggle {
            display: none;
            width: 36px;
            height: 36px;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .admin-topbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .admin-page-body {
            padding: 24px;
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

        .form-control,
        .form-select,
        .btn {
            min-height: 38px;
        }

        .btn-sm {
            min-height: 32px;
        }

        .table-responsive {
            border-radius: 10px;
        }

        .card-header form.d-flex,
        .admin-filter-actions {
            flex-wrap: wrap;
        }

        .card-header form.d-flex .form-control,
        .card-header form.d-flex .form-select {
            flex: 1 1 220px;
            max-width: 100% !important;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar-desktop {
                display: none;
            }

            .admin-content {
                margin-left: 0;
            }

            .admin-menu-toggle {
                display: inline-flex;
            }

            .admin-topbar {
                padding: 10px 14px;
            }

            .admin-page-body {
                padding: 14px;
            }
        }

        @media (max-width: 767.98px) {
            body {
                background: #f6f8fb;
            }

            .admin-topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .admin-title-wrap,
            .admin-topbar-actions {
                width: 100%;
            }

            .admin-topbar-actions {
                justify-content: flex-start;
            }

            .admin-user-badge {
                max-width: 100%;
                white-space: normal;
                text-align: left;
            }

            .card {
                border-radius: 12px;
            }

            .card-body {
                padding: 14px;
            }

            .row {
                --bs-gutter-x: .75rem;
                --bs-gutter-y: .75rem;
            }

            .table {
                min-width: 680px;
            }

            .table:not(.table-sm) td,
            .table:not(.table-sm) th {
                padding: .65rem .75rem;
            }

            .btn,
            .form-control,
            .form-select {
                font-size: .92rem;
            }

            .admin-topbar-actions .btn {
                padding-left: 10px;
                padding-right: 10px;
            }

            .card-header form.d-flex {
                gap: 8px !important;
            }

            .card-header form.d-flex .btn {
                flex: 0 0 auto;
            }

            .pagination {
                flex-wrap: wrap;
                gap: 4px;
            }
        }

        @media (max-width: 420px) {
            .admin-page-body {
                padding: 10px;
            }

            .admin-topbar h5 {
                font-size: .95rem;
            }
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
    @include('layouts.partials.admin-sidebar')

    {{-- Main content --}}
    <div class="admin-content">
        @include('layouts.partials.admin-topbar')

        <div class="admin-page-body">
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

