@extends('layouts.admin')
@section('title', 'Bài kiểm tra')
@section('page-title', 'Quản lý bài kiểm tra')
@section('page-actions')
    <a href="/admin/tests/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tạo bài kiểm tra
    </a>
@endsection

@section('content')
@forelse($classes as $class)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-people-fill me-2 text-primary"></i>{{ $class->name }} — {{ $class->subject }}
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Tiêu đề</th><th>Thời gian</th><th>Trạng thái</th><th>Câu hỏi</th><th>Bài nộp</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($class->tests as $test)
                        <tr>
                            <td class="fw-semibold">{{ $test->title }}</td>
                            <td>
                                <small>
                                    @if($test->starts_at)
                                        {{ \Carbon\Carbon::parse($test->starts_at)->format('d/m H:i') }}
                                        → {{ \Carbon\Carbon::parse($test->ends_at)->format('d/m H:i') }}
                                    @else
                                        —
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($test->status === 'published')
                                    <span class="badge bg-success">Đã xuất bản</span>
                                @elseif($test->status === 'closed')
                                    <span class="badge bg-secondary">Đã đóng</span>
                                @else
                                    <span class="badge bg-warning text-dark">Nháp</span>
                                @endif
                            </td>
                            <td>{{ $test->questions->count() }}</td>
                            <td>{{ $test->submissions->count() }}</td>
                            <td class="text-end">
                                <a href="/admin/tests/{{ $test->id }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/admin/tests/{{ $test->id }}/edit" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form method="POST" action="/admin/tests/{{ $test->id }}" class="d-inline"
                                      onsubmit="return confirm('Xoá bài kiểm tra này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Chưa có bài kiểm tra.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">Chưa có lớp học nào.</div>
@endforelse
@endsection
