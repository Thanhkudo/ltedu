<div class="admin-topbar">
    <div class="admin-title-wrap">
        <button class="btn btn-sm btn-outline-secondary admin-menu-toggle" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#adminMobileMenu" aria-controls="adminMobileMenu">
            <i class="bi bi-list"></i>
        </button>
        <h5>@yield('page-title', 'Dashboard')</h5>
    </div>
    <div class="admin-topbar-actions">
        <span class="badge bg-light text-dark border admin-user-badge">
            {{ $adminUser->name ?? 'User' }} ({{ strtoupper($adminUser->role ?? 'n/a') }})
        </span>
        <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-box-arrow-right me-1"></i><span>{{ __('ui.logout') }}</span>
            </button>
        </form>
        @yield('page-actions')
    </div>
</div>
