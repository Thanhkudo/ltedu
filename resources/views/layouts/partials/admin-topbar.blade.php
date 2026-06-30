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
