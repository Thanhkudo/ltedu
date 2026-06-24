@extends('layouts.admin')
@section('title', 'Bài nộp — ' . $test->title)
@section('page-title', 'Bài nộp: ' . $test->title)
@section('page-actions')
    <a href="/admin/tests/{{ $test->id }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Trở về bài kiểm tra
    </a>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <div class="card bg-light border-0">
            <div class="card-body py-2 px-3 small">
                <span class="text-muted me-2">Lớp:</span><strong class="me-4">{{ $test->schoolClass->name ?? '—' }}</strong>
                <span class="text-muted me-2">Tổng điểm:</span><strong class="me-4">{{ $test->total_score }}</strong>
                <span class="text-muted me-2">Số bài nộp:</span><strong>{{ $submissions->count() }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Học viên</th>
                    <th>Nộp lúc</th>
                    <th>Điểm</th>
                    <th>Trạng thái</th>
                    <th>Kết quả</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $sub->student->full_name ?? '—' }}</div>
                            <small class="text-muted">{{ $sub->student->code ?? '' }}</small>
                        </td>
                        <td>
                            <small>{{ $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d/m/Y H:i') : '—' }}</small>
                        </td>
                        <td>
                            <span class="fw-bold fs-5">{{ $sub->total_score ?? '—' }}</span>
                            <span class="text-muted">/ {{ $test->total_score }}</span>
                        </td>
                        <td>
                            @if($sub->status === 'submitted')
                                <span class="badge bg-success">Đã nộp</span>
                            @elseif($sub->status === 'in_progress')
                                <span class="badge bg-warning text-dark">Đang làm</span>
                            @else
                                <span class="badge bg-secondary">{{ $sub->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($sub->total_score !== null && $test->total_score > 0)
                                @php $pct = ($sub->total_score / $test->total_score) * 100; $pass = $pct >= ($test->passing_score ?? 60); @endphp
                                <span class="badge {{ $pass ? 'bg-success' : 'bg-danger' }}">
                                    {{ $pass ? 'Đạt' : 'Không đạt' }} ({{ number_format($pct, 0) }}%)
                                </span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có bài nộp nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
