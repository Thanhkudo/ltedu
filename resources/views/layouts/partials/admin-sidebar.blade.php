<div class="admin-sidebar admin-sidebar-desktop">
    @include('layouts.partials.admin-sidebar-nav')
</div>

<div class="offcanvas offcanvas-start admin-mobile-menu" tabindex="-1" id="adminMobileMenu"
     aria-labelledby="adminMobileMenuLabel">
    <div class="admin-sidebar admin-sidebar-mobile">
        <div class="d-flex align-items-center justify-content-between">
            <span class="visually-hidden" id="adminMobileMenuLabel">Menu admin</span>
            <button type="button" class="btn btn-sm btn-outline-light ms-auto me-3 mt-3"
                    data-bs-dismiss="offcanvas" aria-label="Đóng menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        @include('layouts.partials.admin-sidebar-nav')
    </div>
</div>
