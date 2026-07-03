@extends('layouts.admin')
@section('title', 'Bài nộp - ' . $assignment->exercise->title)
@section('page-title', 'Bài nộp: ' . $assignment->exercise->title)
@section('page-actions')
    <a href="/admin/classes/{{ $assignment->session->class_id }}/show" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Trở về lớp
    </a>
@endsection

@push('styles')
    <style>
        .submission-student-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
        }

        .submission-student-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .submission-tabs {
            gap: 6px;
            padding: 12px 12px 0;
            border-bottom: 0;
        }

        .submission-tabs .nav-link {
            border-radius: 10px;
            border: 1px solid #dbe3ef;
            color: #475569;
            font-weight: 700;
            padding: 8px 12px;
        }

        .submission-tabs .nav-link.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .submission-pane {
            padding: 14px;
        }

        .submission-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .submission-summary-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            background: #fff;
        }

        .submission-summary-item .label {
            font-size: .75rem;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
        }

        .submission-summary-item .value {
            font-weight: 800;
            color: #0f172a;
        }

        .admin-result-list {
            display: grid;
            gap: 10px;
        }

        .admin-result-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            background: #fff;
        }

        .admin-result-card.correct {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .admin-result-card.wrong {
            border-color: #fecdd3;
            background: #fff7f8;
        }

        .admin-result-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }

        .admin-question-title {
            font-weight: 800;
            color: #0f172a;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .admin-result-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: .75rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .admin-result-pill.correct {
            background: #dcfce7;
            color: #166534;
        }

        .admin-result-pill.wrong {
            background: #ffe4e6;
            color: #be123c;
        }

        .admin-answer-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .admin-answer-box {
            border: 1px solid rgba(148, 163, 184, .35);
            border-radius: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, .75);
        }

        .admin-answer-box .label {
            font-size: .75rem;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 5px;
        }

        .admin-answer-box .text {
            color: #0f172a;
            font-weight: 700;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .submission-grade-form {
            display: grid;
            grid-template-columns: 90px minmax(0, 1fr) auto;
            gap: 8px;
            margin-top: 14px;
        }

        @media (max-width: 767.98px) {
            .submission-student-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .submission-summary,
            .admin-answer-grid,
            .submission-grade-form {
                grid-template-columns: 1fr;
            }

            .submission-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 2px;
            }

            .submission-tabs .nav-link {
                white-space: nowrap;
            }

            .admin-result-head {
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col">
            <div class="card bg-light border-0">
                <div class="card-body py-2 px-3">
                    <span class="text-muted me-3">Buổi học:</span>
                    <strong>{{ $assignment->session->title ?? 'Buổi ' . $assignment->session->session_number }}</strong>
                    <span class="text-muted ms-4 me-3">Hạn nộp:</span>
                    <strong class="{{ $assignment->due_date && $assignment->isPastDue() ? 'text-danger' : 'text-success' }}">
                        {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y H:i') : 'Không giới hạn' }}
                    </strong>
                    <span class="text-muted ms-4 me-3">Điểm tối đa:</span> <strong>{{ $assignment->max_score }}</strong>
                </div>
            </div>
        </div>
    </div>

    @forelse($submissionGroups as $studentId => $studentSubmissions)
        @php
            $student = optional($studentSubmissions->first())->student;
            $latestSubmission = $studentSubmissions->first();
            $latestResult = data_get($latestSubmission->json_params ?? [], 'result', []);
        @endphp

        <div class="submission-student-card card mb-3">
            <div class="submission-student-head">
                <div>
                    <div class="fw-bold">{{ $student->full_name ?? 'Học viên #' . $studentId }}</div>
                    <div class="text-muted small">{{ $student->code ?? '' }}</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-primary-subtle text-primary">
                        {{ $studentSubmissions->count() }} lần nộp gần nhất
                    </span>
                    <span class="badge bg-success-subtle text-success">
                        Mới nhất: {{ $latestSubmission->score ?? data_get($latestResult, 'score', 0) }}/{{ data_get($latestResult, 'max_score', $assignment->max_score) }}
                    </span>
                </div>
            </div>

            <ul class="nav nav-tabs submission-tabs" id="student-{{ $studentId }}-tabs" role="tablist">
                @foreach($studentSubmissions as $sub)
                    @php
                        $tabId = 'student-' . $studentId . '-submission-' . $sub->id;
                    @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                id="{{ $tabId }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#{{ $tabId }}"
                                type="button"
                                role="tab"
                                aria-controls="{{ $tabId }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            Lần {{ $loop->iteration }}
                            <span class="d-none d-sm-inline">
                                - {{ optional($sub->submitted_at)->format('d/m H:i') }}
                            </span>
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($studentSubmissions as $sub)
                    @php
                        $tabId = 'student-' . $studentId . '-submission-' . $sub->id;
                        $jsonParams = $sub->json_params ?? [];
                        $result = data_get($jsonParams, 'result', []);
                        $items = data_get($result, 'items', []);
                        $score = $sub->score ?? data_get($result, 'score', 0);
                        $maxScore = data_get($result, 'max_score', $assignment->max_score);
                        $contentData = null;

                        if (is_string($sub->content) && strpos($sub->content, '{') === 0) {
                            $decoded = json_decode($sub->content, true);
                            if (is_array($decoded)) {
                                $contentData = $decoded;
                            }
                        }
                    @endphp

                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                         id="{{ $tabId }}"
                         role="tabpanel"
                         aria-labelledby="{{ $tabId }}-tab">
                        <div class="submission-pane">
                            <div class="submission-summary">
                                <div class="submission-summary-item">
                                    <div class="label">Thời gian nộp</div>
                                    <div class="value">{{ optional($sub->submitted_at)->format('d/m/Y H:i') ?: '-' }}</div>
                                </div>
                                <div class="submission-summary-item">
                                    <div class="label">Điểm</div>
                                    <div class="value">{{ $score }}/{{ $maxScore }}</div>
                                </div>
                                <div class="submission-summary-item">
                                    <div class="label">Số câu đúng</div>
                                    <div class="value">{{ data_get($result, 'correct', 0) }}/{{ data_get($result, 'total', count($items)) }}</div>
                                </div>
                                <div class="submission-summary-item">
                                    <div class="label">Trạng thái</div>
                                    <div class="value">{{ $sub->status === 'graded' ? 'Đã chấm' : 'Đã nộp' }}</div>
                                </div>
                            </div>

                            @if(!empty($items))
                                <div class="admin-result-list">
                                    @foreach($items as $item)
                                        @php
                                            $isCorrectItem = (bool) data_get($item, 'is_correct');
                                            $studentAnswerText = data_get($item, 'answer_text') ?: 'Chưa trả lời';
                                            $expectedText = data_get($item, 'expected_text', '');
                                        @endphp

                                        <article class="admin-result-card {{ $isCorrectItem ? 'correct' : 'wrong' }}">
                                            <div class="admin-result-head">
                                                <div>
                                                    <div class="text-muted small fw-bold mb-1">
                                                        Câu {{ $loop->iteration }} - {{ data_get($item, 'type_label', 'Câu hỏi') }}
                                                    </div>
                                                    <div class="admin-question-title">{{ data_get($item, 'question_text') }}</div>
                                                </div>
                                                <span class="admin-result-pill {{ $isCorrectItem ? 'correct' : 'wrong' }}">
                                                    <i class="bi {{ $isCorrectItem ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                                    {{ $isCorrectItem ? 'Đúng' : 'Sai' }}
                                                    - {{ data_get($item, 'score', 0) }}/{{ data_get($item, 'max_score', 0) }} điểm
                                                </span>
                                            </div>

                                            <div class="admin-answer-grid">
                                                <div class="admin-answer-box">
                                                    <div class="label">Đáp án học viên</div>
                                                    <div class="text">{!! nl2br(e($studentAnswerText)) !!}</div>
                                                </div>
                                                <div class="admin-answer-box">
                                                    <div class="label">Đáp án đúng</div>
                                                    <div class="text">{!! nl2br(e($expectedText)) !!}</div>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @elseif($contentData && ($contentData['mode'] ?? '') === 'question_flow')
                                <div class="alert alert-info mb-0">
                                    Bài nộp theo từng câu nhưng chưa có dữ liệu kết quả chi tiết.
                                </div>
                            @else
                                <div class="admin-answer-box">
                                    <div class="label">Nội dung học viên nộp</div>
                                    <div class="text">{!! nl2br(e($sub->content ?: 'Không có nội dung')) !!}</div>
                                </div>
                            @endif

                            @if($sub->file_path)
                                <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-3">
                                    <i class="bi bi-paperclip"></i> File đính kèm
                                </a>
                            @endif

                            <form method="POST"
                                  action="/admin/assignments/{{ $assignment->id }}/submissions/{{ $sub->id }}/grade"
                                  class="submission-grade-form">
                                @csrf
                                <input type="number" name="score" min="0" max="{{ $assignment->max_score }}" step="0.5"
                                       class="form-control form-control-sm"
                                       value="{{ $sub->score }}"
                                       placeholder="Điểm">
                                <input type="text" name="feedback" class="form-control form-control-sm"
                                       value="{{ $sub->feedback }}"
                                       placeholder="Nhận xét">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check2 me-1"></i>Lưu
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center text-muted py-4">
                Chưa có học viên nào nộp bài.
            </div>
        </div>
    @endforelse
@endsection
