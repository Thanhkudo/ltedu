@extends('layouts.admin')
@section('title', 'Quản lý người dùng')
@section('page-title', 'Quản lý người dùng')
@section('page-actions')
    <a href="/admin/users/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Thêm người dùng
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:280px"
                   placeholder="Tìm kiếm tên, email, vai trò..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="/admin/users" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/admin/users/{{ $user->id }}" class="d-inline"
                                  onsubmit="return confirm('Xóa người dùng này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Không có người dùng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($users, 'links'))
        <div class="card-footer bg-white">{{ $users->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

