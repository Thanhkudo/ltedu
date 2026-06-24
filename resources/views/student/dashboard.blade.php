@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .student-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 24px 22px;
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 38%, #06b6d4 100%);
        color: #fff;
        box-shadow: 0 22px 50px rgba(37, 99, 235, .22);
    }
    .student-hero::before,
    .student-hero::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
    }
    .student-hero::before {
        width: 180px;
        height: 180px;
        top: -60px;
        right: -30px;
    }
    .student-hero::after {
        width: 120px;
        height: 120px;
        bottom: -35px;
        left: -20px;
    }
    .student-hero > * {
        position: relative;
        z-index: 1;
    }
    .student-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .16);
        font-size: .78rem;
        font-weight: 900;
        letter-spacing: .3px;
        text-transform: uppercase;
    }
    .student-hero-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }
    .student-mini-stat {
        border-radius: 20px;
        padding: 14px 16px;
        background: rgba(255, 255, 255, .12);
        backdrop-filter: blur(8px);
    }
    .student-mini-stat .value {
        font-size: 1.8rem;
        font-weight: 900;
        line-height: 1;
    }
    .student-mini-stat .label {
        font-size: .78rem;
        font-weight: 700;
        opacity: .88;
        margin-top: 6px;
    }
    .dashboard-shell {
        margin-top: 18px;
    }
    .dashboard-panel {
        background: linear-gradient(180deg, rgba(255,255,255,.96), #ffffff);
        border-radius: 24px;
        border: 1px solid #e7ecff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
        padding: 18px;
    }
    .dashboard-panel + .dashboard-panel {
        margin-top: 16px;
    }
    .dashboard-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }
    .dashboard-panel-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        font-weight: 900;
        color: #0f172a;
    }
    .dashboard-panel-title .icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }
    .class-stack {
        display: grid;
        gap: 12px;
    }
    .class-card {
        display: block;
        padding: 16px;
        border-radius: 20px;
        border: 1px solid #e9edff;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        text-decoration: none;
        color: inherit;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .class-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 24px rgba(37, 99, 235, .12);
        border-color: #bfdbfe;
        color: inherit;
    }
    .class-card-top,
    .task-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }
    .class-card-name,
    .task-card-name {
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .class-card-meta,
    .task-card-meta {
        color: #64748b;
        font-size: .85rem;
        font-weight: 700;
    }
    .task-list {
        display: grid;
        gap: 12px;
    }
    .task-card {
        padding: 16px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #edf2ff;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .task-card:hover {
        transform: translateY(-2px);
        color: inherit;
    }
    .task-card.assignment:hover {
        border-color: #fdba74;
        box-shadow: 0 14px 24px rgba(249, 115, 22, .12);
    }
    .task-card.test:hover {
        border-color: #86efac;
        box-shadow: 0 14px 24px rgba(34, 197, 94, .12);
    }
    .task-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 900;
        white-space: nowrap;
    }
    .task-pill.deadline {
        background: #fff7ed;
        color: #c2410c;
    }
    .task-pill.duration {
        background: #ecfdf5;
        color: #15803d;
    }
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
    .quick-action {
        border-radius: 20px;
        padding: 16px;
        min-height: 120px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 14px 26px rgba(15, 23, 42, .06);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .quick-action:hover {
        transform: translateY(-3px);
        color: inherit;
    }
    .quick-action.study {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    .quick-action.work {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
    }
    .quick-action .icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        background: rgba(255, 255, 255, .72);
    }
    .empty-card {
        border: 1px dashed #cbd5e1;
        border-radius: 20px;
        padding: 22px 18px;
        text-align: center;
        color: #64748b;
        background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
    }
    .empty-card .icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 10px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 1.35rem;
    }
    @media (max-width: 767.98px) {
        .student-hero-grid,
        .quick-grid {
            grid-template-columns: 1fr;
        }
        .student-hero {
            padding: 20px 18px;
            border-radius: 24px;
        }
    }
</style>
@endpush

@section('content')
@php
    $classCount = $student->classes->count();
    $featuredClass = $student->classes->first();
@endphp

<section class="student-hero slide-up">
    <div class="student-hero-badge">
        <i class="bi bi-stars"></i>
        Khu vuc hoc tap cua ban
    </div>

    <div class="mt-3 d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3">
        <div>
            <h2 class="mb-2" style="font-size:1.8rem;font-weight:900;line-height:1.15;">Xin chao, {{ $student->full_name }}!</h2>
            <div style="font-size:.95rem;opacity:.92;max-width:520px;">
                Theo doi lop hoc, bai tap va bai kiem tra sap dien ra tai mot noi de hoc nhanh hon moi ngay.
            </div>
        </div>
        <div class="text-md-end">
            <div style="font-size:.78rem;font-weight:800;opacity:.8;text-transform:uppercase;letter-spacing:.4px;">Ma hoc vien</div>
            <div style="font-size:1.05rem;font-weight:900;">{{ $student->student_code }}</div>
        </div>
    </div>

    <div class="student-hero-grid">
        <div class="student-mini-stat pop-in">
            <div class="value">{{ $classCount }}</div>
            <div class="label">Lop dang hoc</div>
        </div>
        <div class="student-mini-stat pop-in" style="animation-delay:.08s;">
            <div class="value">{{ $assignmentCount ?? $upcomingAssignments->count() }}</div>
            <div class="label">Bai tap da giao</div>
        </div>
        <div class="student-mini-stat pop-in" style="animation-delay:.16s;">
            <div class="value">{{ $activeTests->count() }}</div>
            <div class="label">Bai kiem tra dang mo</div>
        </div>
    </div>
</section>

<div class="row g-4 dashboard-shell">
    <div class="col-lg-5">
        <section class="dashboard-panel slide-up" style="animation-delay:.05s;">
            <div class="dashboard-panel-header">
                <div class="dashboard-panel-title">
                    <span class="icon" style="background:#dbeafe;color:#1d4ed8;"><i class="bi bi-journal-richtext"></i></span>
                    Lop dang hoc
                </div>
                <span class="s-tag tag-blue">{{ $classCount }} lop</span>
            </div>

            <div class="class-stack">
                @forelse($student->classes as $class)
                    <a href="/classes/{{ $class->id }}" class="class-card">
                        <div class="class-card-top">
                            <div>
                                <div class="class-card-name">{{ $class->name }}</div>
                                <div class="class-card-meta">Giao vien: {{ $class->teacher->name ?? 'â€”' }}</div>
                            </div>
                            <span class="s-tag {{ $class->status === 'active' ? 'tag-green' : 'tag-purple' }}">
                                {{ $class->status === 'active' ? 'Dang hoc' : ucfirst($class->status) }}
                            </span>
                        </div>

                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="class-card-meta">
                                <i class="bi bi-calendar-week me-1"></i>
                                {{ $class->sessions->count() }} buoi hoc
                            </small>
                            <small style="font-weight:900;color:#2563eb;">Xem chi tiet <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </a>
                @empty
                    <div class="empty-card">
                        <div class="icon"><i class="bi bi-journal-x"></i></div>
                        <div class="fw-bold mb-1">Chua co lop hoc nao</div>
                        <div class="small">Khi duoc xep lop, thong tin hoc tap cua ban se hien thi tai day.</div>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="dashboard-panel slide-up" style="animation-delay:.1s;">
            <div class="dashboard-panel-header mb-3">
                <div class="dashboard-panel-title">
                    <span class="icon" style="background:#e0f2fe;color:#0891b2;"><i class="bi bi-lightning-charge-fill"></i></span>
                    Truy cap nhanh
                </div>
            </div>

            <div class="quick-grid">
                <a href="{{ $featuredClass ? '/classes/' . $featuredClass->id : '/' }}" class="quick-action study">
                    <span class="icon"><i class="bi bi-book-half"></i></span>
                    <div>
                        <div class="fw-bold mb-1">Vao lop hoc</div>
                        <div class="small text-muted">{{ $featuredClass ? 'Mo nhanh lop ' . $featuredClass->name : 'Xem tong quan hoc tap' }}</div>
                    </div>
                </a>

                <a href="{{ $upcomingAssignments->first() ? '/assignments/' . $upcomingAssignments->first()->id : '/' }}" class="quick-action work">
                    <span class="icon"><i class="bi bi-pencil-square"></i></span>
                    <div>
                        <div class="fw-bold mb-1">Mo bai tap</div>
                        <div class="small text-muted">{{ $upcomingAssignments->first() ? 'Mo bai tap duoc giao gan day' : 'Chua co bai tap nao duoc giao' }}</div>
                    </div>
                </a>
            </div>
        </section>
    </div>

    <div class="col-lg-7">
        <section class="dashboard-panel slide-up" style="animation-delay:.08s;">
            <div class="dashboard-panel-header">
                <div class="dashboard-panel-title">
                    <span class="icon" style="background:#ffedd5;color:#ea580c;"><i class="bi bi-alarm-fill"></i></span>
                    Bai tap gan day
                </div>
                <span class="s-tag tag-orange">Co the nop nhieu lan</span>
            </div>

            <div class="task-list">
                @forelse($upcomingAssignments as $assignment)
                    <a href="/assignments/{{ $assignment->id }}" class="task-card assignment">
                        <div class="task-card-top">
                            <div>
                                <div class="task-card-name">{{ $assignment->exercise->title }}</div>
                                <div class="task-card-meta">{{ $assignment->session->schoolClass->name ?? 'Chua ro lop' }}</div>
                            </div>
                            <span class="task-pill deadline">
                                <i class="bi bi-hourglass-split"></i>
                                {{ $assignment->due_date ? $assignment->due_date->format('d/m H:i') : 'Khong gioi han' }}
                            </span>
                        </div>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="task-card-meta">Nhan vao de lam bai. He thong luu 3 lan nop gan nhat.</small>
                            <small style="font-weight:900;color:#f97316;">Mo bai <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </a>
                @empty
                    <div class="empty-card">
                        <div class="icon"><i class="bi bi-check2-circle"></i></div>
                        <div class="fw-bold mb-1">Chua co bai tap nao duoc giao</div>
                        <div class="small">Khi giao vien giao bai, danh sach bai tap se hien thi tai day.</div>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="dashboard-panel slide-up" style="animation-delay:.12s;">
            <div class="dashboard-panel-header">
                <div class="dashboard-panel-title">
                    <span class="icon" style="background:#dcfce7;color:#15803d;"><i class="bi bi-patch-check-fill"></i></span>
                    Bai kiem tra dang mo
                </div>
                <span class="s-tag tag-green">San sang vao lam</span>
            </div>

            <div class="task-list">
                @forelse($activeTests as $test)
                    <a href="/tests/{{ $test->id }}" class="task-card test">
                        <div class="task-card-top">
                            <div>
                                <div class="task-card-name">{{ $test->test->title }}</div>
                                <div class="task-card-meta">{{ $test->schoolClass->name ?? 'Chua ro lop' }}</div>
                            </div>
                            <span class="task-pill duration">
                                <i class="bi bi-stopwatch-fill"></i>
                                {{ $test->effective_duration }} phut
                            </span>
                        </div>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="task-card-meta">Dong luc: {{ $test->ends_at->format('H:i d/m/Y') }}</small>
                            <small style="font-weight:900;color:#16a34a;">Vao kiem tra <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </a>
                @empty
                    <div class="empty-card">
                        <div class="icon"><i class="bi bi-emoji-smile"></i></div>
                        <div class="fw-bold mb-1">Hien chua co bai kiem tra nao mo</div>
                        <div class="small">Khi giao vien mo bai kiem tra, ban se thay ngay tai khu vuc nay.</div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection

