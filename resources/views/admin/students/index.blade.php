@extends('layouts.admin')
@section('title', 'Quản lý học viên')
@section('page-title', 'Quản lý học viên')
@section('page-actions')
    <a href="/admin/students/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Thêm học viên
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:280px"
                   placeholder="Tim kiem ten, email, ma HV..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="/admin/students" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Ma HV</th><th>Ho ten</th><th>Email</th><th>So dien thoai</th><th>Lop</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td><span class="badge bg-light text-dark">{{ $student->student_code }}</span></td>
                        <td class="fw-semibold">{{ $student->full_name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->phone ?? '-' }}</td>
                        <td>{{ $student->classes_count ?? '-' }}</td>
                        <td class="text-end">
                            <a href="/admin/students/{{ $student->id }}/edit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/admin/students/{{ $student->id }}" class="d-inline"
                                  onsubmit="return confirm('Xóa học viên này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Không có học viên nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($students, 'links'))
        <div class="card-footer bg-white">{{ $students->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

