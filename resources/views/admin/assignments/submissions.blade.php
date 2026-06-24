@extends('layouts.admin')
@section('title', 'Bài nộp — ' . $assignment->exercise->title)
@section('page-title', 'Bài nộp: ' . $assignment->exercise->title)
@section('page-actions')
    <a href="/admin/classes/{{ $assignment->session->class_id }}/show" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Trở về lớp
    </a>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <div class="card bg-light border-0">
            <div class="card-body py-2 px-3">
                <span class="text-muted me-3">Buổi học:</span> <strong>{{ $assignment->session->title ?? 'Buổi ' . $assignment->session->session_number }}</strong>
                <span class="text-muted ms-4 me-3">Hạn nộp:</span>
                <strong class="{{ $assignment->isPastDue() ? 'text-danger' : 'text-success' }}">
                    {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y H:i') : '—' }}
                </strong>
                <span class="text-muted ms-4 me-3">Điểm tối đa:</span> <strong>{{ $assignment->max_score }}</strong>
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
                    <th>Nội dung nộp</th>
                    <th>Thời gian nộp</th>
                    <th style="width:200px">Chấm điểm</th>
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
                            @php
                                $contentData = null;
                                if (is_string($sub->content) && strpos($sub->content, '{') === 0) {
                                    $decoded = json_decode($sub->content, true);
                                    if (is_array($decoded)) {
                                        $contentData = $decoded;
                                    }
                                }
                            @endphp

                            @if($contentData && ($contentData['mode'] ?? '') === 'question_flow')
                                <div class="fw-semibold small">Bài nộp theo từng câu</div>
                                <div class="text-muted small">
                                    Số câu trả lời: {{ is_array($contentData['answers'] ?? null) ? count($contentData['answers']) : 0 }}
                                </div>
                            @else
                                <div style="max-width:300px" class="text-truncate" title="{{ $sub->content }}">
                                    {{ $sub->content }}
                                </div>
                            @endif

                            @if($sub->file_path)
                                <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="small">
                                    <i class="bi bi-paperclip"></i> File đính kèm
                                </a>
                            @endif
                        </td>
                        <td>
                            <small>{{ $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d/m/Y H:i') : '—' }}</small>
                        </td>
                        <td>
                            <form method="POST" action="/admin/assignments/{{ $assignment->id }}/submissions/{{ $sub->id }}/grade" class="d-flex gap-1">
                                @csrf
                                <input type="number" name="score" min="0" max="{{ $assignment->max_score }}" step="0.5"
                                       class="form-control form-control-sm" style="width:70px"
                                       value="{{ $sub->score }}" placeholder="Điểm">
                                <input type="text" name="feedback" class="form-control form-control-sm"
                                       value="{{ $sub->feedback }}" placeholder="Nhận xét">
                                <button type="submit" class="btn btn-sm btn-success flex-shrink-0">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Chưa có học viên nào nộp bài.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
