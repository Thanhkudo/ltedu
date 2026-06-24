@extends('layouts.app')
@section('title', $class->name)

@push('styles')
<style>
    .class-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 22px 20px;
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 38%, #06b6d4 100%);
        color: #fff;
        box-shadow: 0 22px 50px rgba(37, 99, 235, .22);
    }
    .class-hero::before,
    .class-hero::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
    }
    .class-hero::before {
        width: 180px;
        height: 180px;
        right: -40px;
        top: -55px;
    }
    .class-hero::after {
        width: 110px;
        height: 110px;
        left: -20px;
        bottom: -32px;
    }
    .class-hero > * {
        position: relative;
        z-index: 1;
    }
    .class-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .14);
        font-size: .78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .3px;
    }
    .class-overview-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }
    .class-overview-card {
        background: rgba(255, 255, 255, .12);
        border-radius: 20px;
        padding: 14px 16px;
        backdrop-filter: blur(8px);
    }
    .class-overview-value {
        font-size: 1.75rem;
        line-height: 1;
        font-weight: 900;
    }
    .class-overview-label {
        margin-top: 6px;
        font-size: .78rem;
        font-weight: 700;
        opacity: .9;
    }
    .class-page-shell {
        margin-top: 18px;
    }
    .session-timeline {
        display: grid;
        gap: 16px;
    }
    .session-card {
        background: linear-gradient(180deg, rgba(255,255,255,.96), #ffffff);
        border: 1px solid #e7ecff;
        border-radius: 24px;
        padding: 18px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
    }
    .session-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 14px;
    }
    .session-kicker {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: .74rem;
        font-weight: 900;
        margin-bottom: 10px;
    }
    .session-title {
        font-size: 1.05rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .session-meta {
        color: #64748b;
        font-size: .86rem;
        font-weight: 700;
    }
    .session-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 900;
        white-space: nowrap;
    }
    .session-status.scheduled {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .session-status.completed {
        background: #dcfce7;
        color: #15803d;
    }
    .session-status.cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }
    .assignment-grid {
        display: grid;
        gap: 12px;
    }
    .assignment-card {
        border: 1px solid #edf2ff;
        border-radius: 20px;
        padding: 15px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .assignment-card:hover {
        transform: translateY(-2px);
        border-color: #bfdbfe;
        box-shadow: 0 14px 24px rgba(37, 99, 235, .12);
    }
    .assignment-top {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: flex-start;
    }
    .assignment-title {
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .assignment-meta {
        font-size: .84rem;
        color: #64748b;
        font-weight: 700;
    }
    .assignment-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    .assignment-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 900;
    }
    .chip-type {
        background: #eef2ff;
        color: #4f46e5;
    }
    .chip-deadline {
        background: #fff7ed;
        color: #c2410c;
    }
    .chip-status.todo {
        background: #fef3c7;
        color: #92400e;
    }
    .chip-status.late {
        background: #fee2e2;
        color: #b91c1c;
    }
    .chip-status.done {
        background: #d1fae5;
        color: #047857;
    }
    .chip-status.graded {
        background: #dcfce7;
        color: #166534;
    }
    .assignment-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 14px;
    }
    .empty-block {
        border: 1px dashed #cbd5e1;
        border-radius: 20px;
        padding: 22px 18px;
        background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
        text-align: center;
        color: #64748b;
    }
    .empty-block .icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 10px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 1.3rem;
    }
    @media (max-width: 767.98px) {
        .class-overview-grid {
            grid-template-columns: 1fr;
        }
        .session-head,
        .assignment-top,
        .assignment-foot {
            flex-direction: column;
            align-items: flex-start;
        }
        .class-hero {
            padding: 20px 18px;
        }
    }
</style>
@endpush

@section('content')
@php
    $sessionsCount = $class->sessions->count();
    $assignmentsCount = $class->sessions->sum(fn ($session) => $session->assignments->count());
    $submittedCount = $class->sessions->sum(function ($session) use ($student) {
        return $session->assignments->filter(function ($assignment) use ($student) {
            return $assignment->submissions->where('student_id', $student->id)->isNotEmpty();
        })->count();
    });
@endphp

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="margin-bottom:0;">
        <li class="breadcrumb-item"><a href="/" style="text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item active">{{ $class->name }}</li>
    </ol>
</nav>

<section class="class-hero slide-up mb-4">
    <div class="class-hero-badge">
        <i class="bi bi-stars"></i>
        Khu vuc hoc tap cua ban
    </div>

    <div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div>
            <h2 class="mb-2" style="font-size:1.75rem;font-weight:900;line-height:1.15;">{{ $class->name }}</h2>
            <div style="font-size:.95rem;opacity:.92;max-width:560px;">
                Giao vien: <strong>{{ $class->teacher->name ?? '—' }}</strong>
                <span class="mx-2">|</span>
                {{ $class->start_date->format('d/m/Y') }} - {{ $class->end_date ? $class->end_date->format('d/m/Y') : 'Chua xac dinh' }}
            </div>
        </div>

        <span class="s-tag {{ $class->status === 'active' ? 'tag-green' : 'tag-purple' }}" style="font-size:.82rem;">
            {{ $class->status === 'active' ? 'Dang hoc' : ucfirst($class->status) }}
        </span>
    </div>

    <div class="class-overview-grid">
        <div class="class-overview-card">
            <div class="class-overview-value">{{ $sessionsCount }}</div>
            <div class="class-overview-label">Buoi hoc</div>
        </div>
        <div class="class-overview-card">
            <div class="class-overview-value">{{ $assignmentsCount }}</div>
            <div class="class-overview-label">Bai tap duoc giao</div>
        </div>
        <div class="class-overview-card">
            <div class="class-overview-value">{{ $submittedCount }}</div>
            <div class="class-overview-label">Bai da nop</div>
        </div>
    </div>
</section>

<div class="class-page-shell">
    <div class="s-section-title mb-3">Lich hoc va bai tap</div>

    <div class="session-timeline">
        @forelse($class->sessions as $session)
            <section class="session-card slide-up">
                <div class="session-head">
                    <div>
                        <div class="session-kicker">
                            <i class="bi bi-calendar2-week"></i>
                            Buoi {{ $session->session_number }}
                        </div>
                        <div class="session-title">{{ $session->title }}</div>
                        <div class="session-meta">
                            <i class="bi bi-clock me-1"></i>{{ $session->session_date->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <span class="session-status {{ $session->status }}">
                        {{ ['scheduled' => 'Sap hoc', 'completed' => 'Da hoc', 'cancelled' => 'Da huy'][$session->status] ?? $session->status }}
                    </span>
                </div>

                @if($session->assignments->isNotEmpty())
                    <div class="assignment-grid">
                        @foreach($session->assignments as $assignment)
                            @php
                                $submission = $assignment->submissions
                                    ->where('student_id', $student->id)
                                    ->sortByDesc('submitted_at')
                                    ->first();
                                $statusClass = 'todo';
                                $statusLabel = 'Chua nop';

                                if (!$submission && $assignment->isPastDue()) {
                                    $statusClass = 'late';
                                    $statusLabel = 'Qua han';
                                } elseif ($submission && $submission->status === 'graded') {
                                    $statusClass = 'graded';
                                    $statusLabel = 'Da cham: ' . $submission->score . '/' . $assignment->max_score;
                                } elseif ($submission) {
                                    $statusClass = 'done';
                                    $statusLabel = 'Da nop';
                                }
                            @endphp

                            <article class="assignment-card">
                                <div class="assignment-top">
                                    <div>
                                        <div class="assignment-title">{{ $assignment->exercise->title }}</div>
                                        <div class="assignment-meta">Bai tap cho buoi {{ $session->session_number }}</div>
                                    </div>
                                    <a href="/assignments/{{ $assignment->id }}" class="btn-app btn-purple" style="padding:8px 16px;font-size:.8rem;">
                                        {{ $submission ? 'Xem bai' : 'Lam bai' }}
                                    </a>
                                </div>

                                <div class="assignment-chips">
                                    <span class="assignment-chip chip-type">
                                        <i class="bi bi-bookmark-star-fill"></i>
                                        {{ $assignment->exercise->type }}
                                    </span>
                                    <span class="assignment-chip chip-deadline">
                                        <i class="bi bi-hourglass-split"></i>
                                        Han: {{ $assignment->due_date->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="assignment-chip chip-status {{ $statusClass }}">
                                        <i class="bi bi-check2-circle"></i>
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="assignment-foot">
                                    <div class="assignment-meta">
                                        {{ $assignment->isPastDue() && !$submission ? 'Can uu tien hoan thanh som.' : 'Nhan vao de xem chi tiet va nop bai.' }}
                                    </div>
                                    <div style="font-size:.82rem;font-weight:900;color:#2563eb;">Mo bai tap <i class="bi bi-arrow-right-short"></i></div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-block">
                        <div class="icon"><i class="bi bi-journal-text"></i></div>
                        <div class="fw-bold mb-1">Buoi nay chua co bai tap</div>
                        <div class="small">Noi dung bai tap se xuat hien tai day khi giao vien giao bai.</div>
                    </div>
                @endif
            </section>
        @empty
            <div class="empty-block">
                <div class="icon"><i class="bi bi-calendar-x"></i></div>
                <div class="fw-bold mb-1">Lop hoc chua co lich hoc</div>
                <div class="small">Khi co buoi hoc moi, thong tin se duoc hien tai trang nay.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
