@extends('layouts.admin')
@section('title', 'Quản lý lớp học')
@section('page-title', 'Quản lý lớp học')
@section('page-actions')
    <a href="/admin/classes/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Thêm lớp học
    </a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã lớp</th><th>Tên lớp</th><th>Giáo viên</th>
                    <th>Bắt đầu</th><th>Trạng thái</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $class)
                    <tr>
                        <td><span class="badge bg-light text-dark">{{ $class->class_code }}</span></td>
                        <td class="fw-semibold">{{ $class->name }}</td>
                        <td>{{ $class->teacher->name ?? '—' }}</td>
                        <td>{{ $class->start_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $class->status }}">{{ ucfirst($class->status) }}</span>
                        </td>
                        <td class="text-end">
                            <a href="/admin/classes/{{ $class->id }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="/admin/classes/{{ $class->id }}/edit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/admin/classes/{{ $class->id }}" class="d-inline"
                                  onsubmit="return confirm('Xoá lớp học này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có lớp học nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($classes, 'links'))
        <div class="card-footer bg-white">{{ $classes->links() }}</div>
    @endif
</div>
@endsection
