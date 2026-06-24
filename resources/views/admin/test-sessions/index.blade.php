@extends('layouts.admin')
@section('title', 'Phiên kiểm tra')
@section('page-title', 'Quản lý phiên kiểm tra')
@section('page-actions')
    <a href="{{ route('admin.test-sessions.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tạo phiên kiểm tra
    </a>
@endsection

@section('content')
@forelse($classes as $class)
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>{{ $class->name }}</h6>
            <span class="badge bg-light text-dark border">{{ $class->testSessions->count() }} phiên</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Phiên</th>
                        <th>Đề thi</th>
                        <th>Thời gian mở</th>
                        <th>Trạng thái</th>
                        <th>Bài nộp</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($class->testSessions as $session)
                        <tr>
                            <td class="fw-semibold">{{ $session->display_title }}</td>
                            <td>
                                <a href="{{ route('admin.tests.show', $session->test_id) }}" class="text-decoration-none">
                                    {{ $session->test->title ?? 'Đề đã xoá' }}
                                </a>
                                <div class="small text-muted">{{ $session->test->questions->count() ?? 0 }} câu - {{ $session->effective_duration }} phút</div>
                            </td>
                            <td>
                                <small>{{ $session->starts_at->format('d/m/Y H:i') }} → {{ $session->ends_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                @if($session->status === 'open')
                                    <span class="badge bg-success">Đang mở</span>
                                @elseif($session->status === 'closed')
                                    <span class="badge bg-secondary">Đã đóng</span>
                                @else
                                    <span class="badge bg-warning text-dark">Nháp</span>
                                @endif
                            </td>
                            <td>{{ $session->submissions->count() }}</td>
                            <td class="text-end">
                                @if($session->status === 'draft')
                                    <form method="POST" action="{{ route('admin.test-sessions.open', $session->id) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success"><i class="bi bi-play-fill"></i></button>
                                    </form>
                                @endif
                                @if($session->status === 'open')
                                    <form method="POST" action="{{ route('admin.test-sessions.close', $session->id) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-stop-fill"></i></button>
                                    </form>
                                @endif
                                @if($session->submissions->count() === 0)
                                    <form method="POST" action="{{ route('admin.test-sessions.destroy', $session->id) }}" class="d-inline" onsubmit="return confirm('Xoá phiên kiểm tra này?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Chưa có phiên kiểm tra.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">Chưa có lớp học nào.</div>
@endforelse
@endsection
