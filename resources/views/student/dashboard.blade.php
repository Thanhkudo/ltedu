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
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }
    .student-mini-stat {
        border-radius: 20px;
        padding: 14px 16px;
        min-height: 104px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: rgba(255, 255, 255, .12);
        backdrop-filter: blur(8px);
    }
    .student-mini-stat.clickable {
        display: block;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        transition: transform .2s ease, background .2s ease;
    }
    .student-mini-stat.clickable:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, .2);
    }
    .student-mini-stat .value {
        font-size: 1.25rem;
        font-weight: 900;
        line-height: 1.2;
        word-break: break-word;
        overflow-wrap: anywhere;
    }
    .student-mini-stat .label {
        font-size: .72rem;
        font-weight: 700;
        opacity: .88;
        margin-top: 6px;
        letter-spacing: .15px;
    }
    .student-mini-stat .subtext {
        font-size: .68rem;
        font-weight: 700;
        opacity: .82;
        margin-top: 4px;
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
        .student-mini-stat {
            min-height: 92px;
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
        {{ __('ui.learning_area') }}
    </div>

    <div class="mt-3 d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3">
        <div>
            <h2 class="mb-2" style="font-size:1.8rem;font-weight:900;line-height:1.15;">{{ __('ui.welcome_student', ['name' => $student->full_name]) }}</h2>
            <div style="font-size:.95rem;opacity:.92;max-width:520px;">
                {{ __('ui.learning_summary') }}
            </div>
        </div>
        <div class="text-md-end">
            <div style="font-size:.78rem;font-weight:800;opacity:.8;text-transform:uppercase;letter-spacing:.4px;">{{ __('ui.student_code') }}</div>
            <div style="font-size:1.05rem;font-weight:900;">{{ $student->student_code }}</div>
        </div>
    </div>

    <div class="student-hero-grid">
        @if($featuredClass)
            <a href="/classes/{{ $featuredClass->id }}" class="student-mini-stat clickable pop-in">
                <div class="label">{{ __('ui.current_class') }}</div>
                <div class="value">{{ $featuredClass->name }}</div>
                <div class="subtext">{{ __('ui.view_details') }}</div>
            </a>
        @else
            <div class="student-mini-stat pop-in">
                <div class="value">{{ $classCount }}</div>
                <div class="label">{{ __('ui.active_classes') }}</div>
            </div>
        @endif
        <div class="student-mini-stat pop-in" style="animation-delay:.08s;">
            <div class="label">{{ __('ui.assignment_summary') }}</div>
            <div class="value">{{ $assignmentCount ?? $upcomingAssignments->count() }} / {{ $completedAssignmentCount ?? 0 }}</div>
            <div class="subtext">{{ __('ui.completed_assignments') }}</div>
        </div>
    </div>
</section>

<div class="row g-4 dashboard-shell">
    <div class="col-lg-5">
        <section class="dashboard-panel slide-up" style="animation-delay:.1s;">
            <div class="dashboard-panel-header mb-3">
                <div class="dashboard-panel-title">
                    <span class="icon" style="background:#e0f2fe;color:#0891b2;"><i class="bi bi-lightning-charge-fill"></i></span>
                    {{ __('ui.quick_access') }}
                </div>
            </div>

            <div class="quick-grid">
                <a href="{{ $featuredClass ? '/classes/' . $featuredClass->id : '/' }}" class="quick-action study">
                    <span class="icon"><i class="bi bi-book-half"></i></span>
                    <div>
                        <div class="fw-bold mb-1">{{ __('ui.open_class') }}</div>
                        <div class="small text-muted">{{ $featuredClass ? __('ui.open_class_named', ['class' => $featuredClass->name]) : __('ui.learning_overview') }}</div>
                    </div>
                </a>

                @php
                    $quickAssignment = $upcomingAssignments->first();
                    $quickSubmission = $quickAssignment ? $quickAssignment->submissions->first() : null;
                @endphp
                <a href="{{ $quickAssignment ? ($quickSubmission ? route('student.assignments.show', $quickAssignment->id) : route('student.assignments.practice', $quickAssignment->id)) : '/' }}" class="quick-action work">
                    <span class="icon"><i class="bi bi-pencil-square"></i></span>
                    <div>
                        <div class="fw-bold mb-1">{{ __('ui.open_assignment') }}</div>
                        <div class="small text-muted">{{ $upcomingAssignments->first() ? __('ui.open_recent_assignment') : __('ui.no_assignments_short') }}</div>
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
                    {{ __('ui.recent_assignments') }}
                </div>
                <span class="s-tag tag-orange">{{ __('ui.resubmission_allowed') }}</span>
            </div>

            <div class="task-list">
                @forelse($upcomingAssignments as $assignment)
                    @php $submission = $assignment->submissions->first(); @endphp
                    <a href="{{ $submission ? route('student.assignments.show', $assignment->id) : route('student.assignments.practice', $assignment->id) }}" class="task-card assignment">
                        <div class="task-card-top">
                            <div>
                                <div class="task-card-name">{{ $assignment->exercise->title }}</div>
                                <div class="task-card-meta">{{ $assignment->session->schoolClass->name ?? __('ui.unknown_class') }}</div>
                            </div>
                            <span class="task-pill deadline">
                                <i class="bi bi-hourglass-split"></i>
                                {{ $assignment->due_date ? $assignment->due_date->format('d/m H:i') : __('ui.no_deadline') }}
                            </span>
                        </div>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="task-card-meta">{{ __('ui.assignment_submission_hint') }}</small>
                            <small style="font-weight:900;color:#f97316;">{{ __('ui.start_assignment') }} <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </a>
                @empty
                    <div class="empty-card">
                        <div class="icon"><i class="bi bi-check2-circle"></i></div>
                        <div class="fw-bold mb-1">{{ __('ui.no_assignments') }}</div>
                        <div class="small">{{ __('ui.no_assignments_hint') }}</div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
