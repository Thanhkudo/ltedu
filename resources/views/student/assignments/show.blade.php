@extends('layouts.app')
@section('title', $assignment->exercise->title)

@push('styles')
    <style>
        .assignment-hero {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            padding: 24px 22px;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 38%, #06b6d4 100%);
            color: #fff;
            box-shadow: 0 22px 50px rgba(37, 99, 235, .22);
            margin-bottom: 18px;
        }

        .assignment-hero::before,
        .assignment-hero::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
        }

        .assignment-hero::before {
            width: 180px;
            height: 180px;
            top: -60px;
            right: -30px;
        }

        .assignment-hero::after {
            width: 120px;
            height: 120px;
            bottom: -35px;
            left: -20px;
        }

        .assignment-hero>* {
            position: relative;
            z-index: 1;
        }

        .assignment-badge {
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

        .assignment-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .assignment-meta-card {
            border-radius: 20px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, .12);
            backdrop-filter: blur(8px);
        }

        .assignment-meta-card .value {
            font-size: 1.2rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .assignment-meta-card .label {
            font-size: .78rem;
            font-weight: 700;
            opacity: .88;
            margin-top: 6px;
        }

        .assignment-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 14px;
            align-items: start;
        }

        .assignment-layout.result-only {
            grid-template-columns: minmax(0, 360px);
            justify-content: end;
        }

        .assignment-panel {
            background: linear-gradient(180deg, rgba(255, 255, 255, .96), #ffffff);
            border-radius: 18px;
            border: 1px solid #e7ecff;
            box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
            padding: 16px;
        }

        .assignment-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .assignment-panel-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            font-weight: 900;
            color: #0f172a;
        }

        .assignment-panel-title .icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            background: #dbeafe;
            color: #1d4ed8;
        }

        .question-flow-note {
            border-radius: 14px;
            padding: 12px 14px;
            background: linear-gradient(135deg, #f8fbff 0%, #eff6ff 100%);
            border: 1px solid #dbeafe;
            color: #1e3a8a;
            font-size: .9rem;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .practice-card {
            border-radius: 16px;
            border: 1px solid #e9edff;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
            padding: 14px;
        }

        .practice-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .practice-index {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4f46e5;
            font-size: .74rem;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .practice-title {
            font-weight: 900;
            color: #0f172a;
            white-space: pre-line;
            line-height: 1.45;
        }

        .practice-type {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: .75rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .context-box {
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 12px;
            font-size: .92rem;
        }

        .context-box.reading {
            background: #fff7ed;
            border: 1px solid #fdba74;
            color: #9a3412;
        }

        .context-box.listening {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            color: #1d4ed8;
        }

        .context-audio {
            display: block;
            width: 100%;
            margin-top: 10px;
        }

        .choice-grid {
            display: grid;
            gap: 10px;
            margin-top: 12px;
        }

        .choice-label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: 1px solid #dbe4ff;
            border-radius: 14px;
            padding: 11px 12px;
            background: #fff;
            cursor: pointer;
            transition: all .15s;
            font-weight: 700;
            color: #334155;
        }

        .choice-label:hover {
            border-color: #60a5fa;
            background: #f8fbff;
        }

        .choice-label input {
            margin-top: 3px;
            accent-color: #2563eb;
        }

        .answer-input {
            margin-top: 12px;
            min-height: 48px;
            border-radius: 14px;
            border-color: #dbe4ff;
            padding: 14px 16px;
        }

        .answer-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        }

        .ordering-list {
            display: grid;
            gap: 10px;
            margin-top: 12px;
        }

        .ordering-item {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #dbe4ff;
            border-radius: 14px;
            padding: 10px 12px;
            background: #fff;
            font-weight: 800;
            color: #334155;
            cursor: grab;
            transition: transform .16s, box-shadow .16s, border-color .16s, background .16s;
            touch-action: none;
        }

        .ordering-item:hover {
            border-color: #93c5fd;
            box-shadow: 0 10px 22px rgba(37, 99, 235, .08);
            transform: translateY(-1px);
        }

        .ordering-item.dragging {
            opacity: .62;
            cursor: grabbing;
            border-color: #2563eb;
            background: #eff6ff;
        }

        .ordering-rank {
            width: 32px;
            height: 32px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            background: #eef2ff;
            color: #4f46e5;
            font-size: .8rem;
            font-weight: 900;
        }

        .ordering-text {
            flex: 1 1 auto;
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .ordering-actions {
            display: inline-flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .ordering-handle {
            color: #94a3b8;
            font-size: 1.15rem;
            flex: 0 0 auto;
        }

        .matching-board {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(240px, .75fr);
            gap: 12px;
            align-items: start;
            margin-top: 12px;
        }

        .match-grid {
            display: grid;
            gap: 10px;
        }

        .match-row {
            display: grid;
            gap: 10px;
            border: 1px solid #dbe4ff;
            border-radius: 14px;
            padding: 12px;
            background: #fff;
            transition: border-color .16s, box-shadow .16s, background .16s;
        }

        .match-row:focus-within {
            border-color: #60a5fa;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .08);
            background: #f8fbff;
        }

        .match-row.active {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .match-row.drop-target {
            border-color: #22c55e;
            background: #f0fdf4;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, .12);
        }

        .match-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 900;
            color: #0f172a;
            min-width: 0;
        }

        .match-left-index {
            width: 30px;
            height: 30px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            background: #e0f2fe;
            color: #0369a1;
            font-size: .78rem;
            font-weight: 900;
        }

        .match-left-text {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .match-drop {
            min-height: 48px;
            border-radius: 12px;
            border: 1px dashed #93c5fd;
            background: #f8fbff;
            color: #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            font-weight: 800;
        }

        .match-row.is-filled .match-drop {
            border-style: solid;
            border-color: #22c55e;
            background: #f0fdf4;
            color: #166534;
        }

        .match-drop-label {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .match-drop-icon {
            flex: 0 0 auto;
            color: #64748b;
        }

        .match-select {
            display: none;
        }

        .match-bank {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            position: sticky;
            top: 82px;
        }

        .match-bank-wrap {
            min-width: 0;
        }

        .match-bank-title {
            font-size: .78rem;
            font-weight: 900;
            color: #475569;
            margin-bottom: 2px;
        }

        .match-bank-card {
            border: 1px solid #dbe4ff;
            border-radius: 14px;
            padding: 10px;
            background: #fff;
            min-height: 74px;
            cursor: grab;
            min-width: 0;
            transition: border-color .16s, background .16s, box-shadow .16s;
        }

        .match-bank-card:hover {
            border-color: #60a5fa;
            box-shadow: 0 10px 20px rgba(37, 99, 235, .08);
        }

        .match-bank-card.selected {
            border-color: #22c55e;
            background: #f0fdf4;
            box-shadow: inset 0 0 0 1px rgba(34, 197, 94, .2);
        }

        .match-bank-card.dragging {
            opacity: .62;
            cursor: grabbing;
            border-color: #2563eb;
            background: #eff6ff;
        }

        .match-bank-card-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: .74rem;
            font-weight: 900;
            color: #64748b;
            margin-bottom: 6px;
        }

        .match-bank-card-used {
            display: none;
            color: #16a34a;
            font-size: .72rem;
            font-weight: 900;
        }

        .match-bank-card.selected .match-bank-card-used {
            display: inline;
        }

        .match-bank-card-body {
            font-weight: 850;
            color: #0f172a;
            overflow-wrap: anywhere;
            min-width: 0;
        }

        .match-option-image {
            max-width: 100%;
            max-height: 84px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            object-fit: cover;
            display: block;
            margin-top: 4px;
        }

        .practice-nav {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: center;
            gap: 12px;
            margin-top: 14px;
        }

        .practice-nav .btn-app {
            width: 100%;
            min-height: 46px;
            justify-content: center;
            white-space: nowrap;
            padding-left: 14px;
            padding-right: 14px;
        }

        .answer-check-feedback {
            border-radius: 16px;
            padding: 12px 14px;
            margin-top: 14px;
            font-size: .9rem;
            font-weight: 800;
        }

        .answer-check-feedback.success {
            background: #ecfdf5;
            border: 1px solid #86efac;
            color: #166534;
        }

        .answer-check-feedback.danger {
            background: #fff1f2;
            border: 1px solid #fda4af;
            color: #be123c;
        }

        .result-panel {
            margin-bottom: 18px;
        }

        .result-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin: 12px 0 14px;
        }

        .result-summary-card {
            border-radius: 14px;
            padding: 12px;
            background: #f8fbff;
            border: 1px solid #dbeafe;
        }

        .result-summary-card .value {
            font-weight: 900;
            font-size: 1.16rem;
            color: #0f172a;
        }

        .result-summary-card .label {
            font-size: .78rem;
            font-weight: 800;
            color: #64748b;
            margin-top: 4px;
        }

        .result-list {
            display: grid;
            gap: 10px;
        }

        .result-card {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 12px;
            background: #fff;
        }

        .result-card.correct {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .result-card.wrong {
            border-color: #fecdd3;
            background: #fff7f8;
        }

        .result-card-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .result-card-title {
            font-weight: 900;
            color: #0f172a;
            white-space: pre-line;
            line-height: 1.4;
        }

        .result-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: .75rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .result-pill.correct {
            background: #dcfce7;
            color: #166534;
        }

        .result-pill.wrong {
            background: #ffe4e6;
            color: #be123c;
        }

        .result-answer-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .result-answer-box {
            border-radius: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, .72);
            border: 1px solid rgba(148, 163, 184, .28);
        }

        .result-answer-box .label {
            font-size: .76rem;
            font-weight: 900;
            color: #64748b;
            margin-bottom: 6px;
        }

        .result-answer-box .text {
            font-weight: 800;
            color: #0f172a;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .assignment-sidebar {
            position: sticky;
            top: 82px;
        }

        .status-box {
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 10px;
        }

        .status-box.info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }

        .status-box.success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        }

        .status-box.danger {
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        }

        .plain-content {
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid #e9edff;
            white-space: pre-line;
        }

        .answer-textarea {
            min-height: 180px;
            border-radius: 14px;
            border-color: #dbe4ff;
            padding: 14px 16px;
        }

        .answer-textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        }

        @media (max-width: 991.98px) {
            .assignment-layout {
                grid-template-columns: 1fr;
            }

            .assignment-sidebar {
                position: static;
            }

            .matching-board {
                grid-template-columns: minmax(0, 1fr) minmax(220px, 280px);
            }
        }

        @media (max-width: 767.98px) {
            .assignment-hero {
                border-radius: 18px;
                padding: 16px;
                margin-bottom: 12px;
            }

            .assignment-badge {
                padding: 6px 10px;
                font-size: .68rem;
            }

            .assignment-hero h2 {
                font-size: 1.35rem !important;
            }

            .assignment-meta-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 8px;
                margin-top: 12px;
            }

            .assignment-meta-card {
                border-radius: 14px;
                padding: 10px 8px;
            }

            .assignment-meta-card .value {
                font-size: .94rem;
                word-break: break-word;
            }

            .assignment-meta-card .label {
                font-size: .68rem;
            }

            .assignment-panel {
                border-radius: 16px;
                padding: 12px;
            }

            .assignment-panel-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }

            .assignment-panel-title .icon {
                width: 36px;
                height: 36px;
                border-radius: 12px;
            }

            .question-flow-note {
                display: none;
            }

            .practice-card {
                padding: 12px;
            }

            .practice-index,
            .practice-type,
            .result-pill {
                white-space: normal;
                line-height: 1.25;
            }

            .practice-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                margin-bottom: 10px;
            }

            .matching-board {
                grid-template-columns: 1fr;
            }

            .match-row {
                grid-template-columns: 1fr;
            }

            .match-bank {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                position: static;
            }

            .match-bank-title {
                margin-top: 2px;
            }

            .ordering-item {
                align-items: flex-start;
                gap: 8px;
                padding: 10px;
            }

            .ordering-actions {
                margin-left: auto;
            }

            .practice-nav {
                gap: 8px;
            }

            .practice-nav .btn-app {
                min-height: 44px;
                padding-left: 10px;
                padding-right: 10px;
                font-size: .86rem;
            }

            .result-summary,
            .result-answer-grid {
                grid-template-columns: 1fr;
            }

            .result-summary {
                gap: 8px;
            }

            .result-summary-card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .result-summary-card .label {
                margin-top: 0;
            }

            .result-card-head {
                flex-direction: column;
            }

        }

        @media (max-width: 420px) {
            .practice-nav {
                grid-template-columns: 1fr;
            }

            .match-bank {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $practiceMode = $practiceMode ?? false;
        $isQuestionFlow = isset($generatedQuestions) && $generatedQuestions->isNotEmpty();
        $questionChecks = [];
        $publicFileUrl = function ($path) {
            $path = trim((string) $path);

            if ($path === '' || preg_match('#^https?://#i', $path)) {
                return $path;
            }

            $baseUrl = rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/');

            return $baseUrl . '/' . ltrim($path, '/');
        };

        if ($isQuestionFlow) {
            foreach ($generatedQuestions as $checkQuestion) {
                $interactionType = $checkQuestion->interaction_type ?? 'normal';
                $check = [
                    'type' => $interactionType === 'normal' ? $checkQuestion->answer_mode : $interactionType,
                    'expected' => '',
                ];

                if ($interactionType === 'ordering') {
                    $check['expected'] = collect(data_get($checkQuestion->interaction_data, 'items', []))
                        ->keys()
                        ->map(fn($idx) => (string) $idx)
                        ->implode(',');
                } elseif ($interactionType === 'matching') {
                    $expectedPairs = [];
                    foreach (data_get($checkQuestion->interaction_data, 'pairs', []) as $pairIndex => $pair) {
                        $expectedPairs[(string) $pairIndex] = (string) $pairIndex;
                    }
                    $check['expected'] = $expectedPairs;
                } elseif ($checkQuestion->answer_mode === 'select') {
                    $correctOptionId = optional($checkQuestion->options->firstWhere('is_correct', true))->id;
                    $check['expected'] = $correctOptionId !== null ? (string) $correctOptionId : '';
                } else {
                    $check['expected'] = mb_strtolower(trim((string) ($checkQuestion->correct_answer ?? '')));
                }

                $questionChecks[(string) $checkQuestion->id] = $check;
            }
        }
    @endphp

    <section class="assignment-hero slide-up">
        <div class="assignment-badge">
            <i class="bi bi-stars"></i>
            Khu vuc hoc tap cua ban
        </div>

        <div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
            <div>
                <h2 class="mb-2" style="font-size:1.8rem;font-weight:900;line-height:1.15;">
                    {{ $assignment->exercise->title }}</h2>
                {{-- <div style="font-size:.95rem;opacity:.92;max-width:560px;">
                {{ $assignment->exercise->description ?: __('ui.assignment_intro') }}
            </div> --}}
            </div>
            {{-- <span style="color:#fff;">{{ ucfirst($assignment->exercise->type) }}</span> --}}
        </div>

        <div class="assignment-meta-grid">
            <div class="assignment-meta-card">
                <div class="value">
                    {{ $assignment->due_date ? $assignment->due_date->format('d/m H:i') : __('ui.no_deadline') }}</div>
                <div class="label">{{ __('ui.due_date') }}</div>
            </div>
            <div class="assignment-meta-card">
                <div class="value">{{ $assignment->max_score }}</div>
                <div class="label">{{ __('ui.max_score') }}</div>
            </div>
        </div>
    </section>

    @if (!$practiceMode && $isQuestionFlow && $submission && !empty($submissionResult))
        <section class="assignment-panel result-panel slide-up">
            <div class="assignment-panel-header">
                <div class="assignment-panel-title">
                    <span class="icon"><i class="bi bi-clipboard-check"></i></span>
                    {{ __('ui.assignment_result') }}
                </div>
                <div class="d-flex flex-wrap align-items-center w-100 gap-2" style="justify-content:space-between;">
                    <span>{{ data_get($submissionResult, 'checked_at') ?: optional($submission->submitted_at)->format('d/m/Y H:i') }}</span>
                    <a href="{{ route('student.assignments.practice', $assignment->id) }}" class="btn-app btn-purple text-decoration-none pull-right    ">
                        <i class="bi bi-play-circle"></i>{{ __('ui.retry_assignment') }}
                    </a>
                </div>
            </div>

            <div class="result-summary">
                <div class="result-summary-card">
                    <div class="value">
                        {{ data_get($submissionResult, 'score', $submission->score ?? 0) }}/{{ data_get($submissionResult, 'max_score', $assignment->max_score) }}
                    </div>
                    <div class="label">{{ __('ui.score') }}</div>
                </div>
                <div class="result-summary-card">
                    <div class="value">
                        {{ data_get($submissionResult, 'correct', 0) }}/{{ data_get($submissionResult, 'total', 0) }}</div>
                    <div class="label">{{ __('ui.correct_answers') }}</div>
                </div>
                <div class="result-summary-card">
                    <div class="value">
                        {{ data_get($submissionResult, 'attempted', 0) }}/{{ data_get($submissionResult, 'total', 0) }}
                    </div>
                    <div class="label">{{ __('ui.answered_questions') }}</div>
                </div>
            </div>

            <div class="result-list">
                @foreach (data_get($submissionResult, 'items', []) as $item)
                    @php
                        $isCorrectItem = (bool) data_get($item, 'is_correct');
                        $studentAnswerText = data_get($item, 'answer_text');
                    @endphp
                    <article class="result-card {{ $isCorrectItem ? 'correct' : 'wrong' }}">
                        <div class="result-card-head">
                            <div>
                                <div class="practice-index">
                                    <i class="bi bi-patch-question"></i>
                                    {{ __('ui.question_number', ['number' => $loop->iteration]) }} - {{ data_get($item, 'type_label', __('ui.question')) }}
                                </div>
                                <div class="result-card-title">{{ data_get($item, 'question_text') }}</div>
                            </div>
                            <span class="result-pill {{ $isCorrectItem ? 'correct' : 'wrong' }}">
                                <i class="bi {{ $isCorrectItem ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                {{ $isCorrectItem ? __('ui.correct') : __('ui.incorrect') }} -
                                {{ data_get($item, 'score', 0) }}/{{ data_get($item, 'max_score', 0) }} {{ __('ui.points') }}
                            </span>
                        </div>
                        <div class="result-answer-grid">
                            <div class="result-answer-box">
                                <div class="label">{{ __('ui.student_answer') }}</div>
                                <div class="text">{{ $studentAnswerText ?: __('ui.unanswered') }}</div>
                            </div>
                            <div class="result-answer-box">
                                <div class="label">{{ __('ui.correct_answer') }}</div>
                                <div class="text">{!! nl2br(e(data_get($item, 'expected_text', ''))) !!}</div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if (!$practiceMode && !($isQuestionFlow && $submission && !empty($submissionResult)))
        <section class="assignment-panel result-panel slide-up">
            <div class="assignment-panel-header">
                <div class="assignment-panel-title">
                    <span class="icon"><i class="bi bi-clipboard-check"></i></span>
                    {{ $submission ? __('ui.submission_status') : __('ui.no_submission_yet') }}
                </div>
                <a href="{{ route('student.assignments.practice', $assignment->id) }}" class="btn-app btn-purple text-decoration-none">
                    <i class="bi bi-play-circle"></i>{{ $submission ? __('ui.retry_assignment') : __('ui.open_practice_page') }}
                </a>
            </div>

            @if ($submission)
                <div class="status-box {{ $submission->status === 'graded' ? 'success' : 'info' }} mb-0">
                    @if ($submission->status === 'graded')
                        <strong>{{ __('ui.score') }}: {{ $submission->score }}/{{ $assignment->max_score }}</strong>
                        @if ($submission->feedback)
                            <p class="mb-0 mt-1">{{ $submission->feedback }}</p>
                        @endif
                    @else
                        <i class="bi bi-hourglass-split me-1"></i>{{ __('ui.submitted_pending_grading') }}
                    @endif
                </div>
            @else
                <div class="status-box info mb-0">
                    <i class="bi bi-info-circle me-1"></i>{{ __('ui.no_submission_hint') }}
                </div>
            @endif
        </section>
    @endif

    @if ($practiceMode)
    <div id="assignmentWorkLayout" class="assignment-layout">
        <div id="assignmentPracticePanel" class="assignment-panel slide-up"
            style="animation-delay:.05s;">
            <div class="assignment-panel-header">
                <div class="assignment-panel-title">
                    <span class="icon"><i class="bi bi-journal-text"></i></span>
                    {{ __('ui.assignment_content') }}
                </div>
                @if ($assignment->isGeneratedFromQuestionBank())
                    <span>{{ $assignment->generated_question_count ? __('ui.generated_questions', ['count' => $assignment->generated_question_count]) : __('ui.generated_question_bank') }}</span>
                @endif
            </div>

            @if ($isQuestionFlow)
                <div id="questionCardsWrap">
                    @foreach ($generatedQuestions as $idx => $question)
                        @php
                            $questionGroup = $question->group;
                            $questionContextType = optional($questionGroup)->type ?: $question->context_type;
                            $questionPassage = optional($questionGroup)->passage ?: $question->passage;
                            $questionAudioUrl = optional($questionGroup)->audio_url ?: $question->audio_url;
                        @endphp
                        <article class="practice-card question-card {{ $idx === 0 ? '' : 'd-none' }}"
                            data-question-index="{{ $idx }}" data-question-id="{{ $question->id }}"
                            data-group-id="{{ $question->group_id ?: '' }}">
                            <div class="practice-head">
                                <div class="d-flex flex-wrap justify-content-between gap-2">
                                    <div class="practice-index">
                                        <i class="bi bi-patch-question"></i>
                                        {{ __('ui.exercise_number', ['number' => $idx + 1]) }}
                                    </div>
                                    <span class="practice-type">
                                        @if (($question->interaction_type ?? 'normal') === 'ordering')
                                            <i class="bi bi-sort-down"></i> {{ __('ui.ordering') }}
                                        @elseif(($question->interaction_type ?? 'normal') === 'matching')
                                            <i class="bi bi-diagram-3"></i> {{ __('ui.matching') }}
                                        @else
                                            <i class="bi {{ $question->answer_mode === 'select' ? 'bi-ui-radios-grid' : 'bi-pencil-square' }}"></i>
                                            {{ $question->answer_mode === 'select' ? __('ui.select_answer') : __('ui.enter_answer') }}
                                        @endif
                                    </span>
                                    @if($questionGroup)
                                        <span class="practice-type">
                                            <i class="bi bi-collection"></i>
                                            {{ $questionGroup->title ?: ($questionGroup->type === 'reading' ? __('ui.reading') : __('ui.listening')) }}
                                        </span>
                                    @endif
                                    <div class="practice-title w-100">{{ $question->question_text }}</div>
                                </div>

                            </div>

                            @if ($questionContextType === 'reading' && $questionPassage)
                                <div class="context-box reading">
                                    <strong>{{ __('ui.reading') }}:</strong><br>
                                    {!! nl2br(e($questionPassage)) !!}
                                </div>
                            @endif

                            @if ($questionContextType === 'listening' && $questionAudioUrl)
                                <div class="context-box listening">
                                    <strong>{{ __('ui.listening') }}:</strong>
                                    <audio class="context-audio" controls preload="metadata"
                                        src="{{ $publicFileUrl($questionAudioUrl) }}">
                                        {{ __('ui.audio_not_supported') }}
                                    </audio>
                                </div>
                            @endif

                            @if (($question->interaction_type ?? 'normal') === 'ordering')
                                @php
                                    $items = collect(data_get($question->interaction_data, 'items', []))->values();
                                    $savedOrder = array_values(
                                        array_filter(
                                            explode(
                                                ',',
                                                (string) old(
                                                    'answers.' . $question->id,
                                                    $submittedAnswers[(string) $question->id] ?? '',
                                                ),
                                            ),
                                            fn($v) => $v !== '',
                                        ),
                                    );
                                    $order = !empty($savedOrder)
                                        ? collect($savedOrder)
                                            ->map(fn($v) => (int) $v)
                                            ->filter(fn($v) => $items->has($v))
                                            ->values()
                                        : $items->keys()->shuffle()->values();
                                    $missing = $items->keys()->diff($order);
                                    $order = $order->merge($missing)->values();
                                @endphp
                                <input type="hidden" class="ordering-answer" name="answers[{{ $question->id }}]"
                                    form="assignmentSubmitForm" value="{{ $order->implode(',') }}">
                                <div class="ordering-list" data-ordering-list>
                                    @foreach ($order as $itemIndex)
                                        <div class="ordering-item" data-item-index="{{ $itemIndex }}" draggable="true">
                                            <span class="ordering-handle"><i class="bi bi-grip-vertical"></i></span>
                                            <span class="ordering-rank">{{ $loop->iteration }}</span>
                                            <span class="ordering-text">{{ $items[$itemIndex] ?? '' }}</span>
                                            <span class="ordering-actions">
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    onclick="moveOrderingItem(this, -1)"><i
                                                        class="bi bi-arrow-up"></i></button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    onclick="moveOrderingItem(this, 1)"><i
                                                        class="bi bi-arrow-down"></i></button>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(($question->interaction_type ?? 'normal') === 'matching')
                                @php
                                    $pairs = collect(data_get($question->interaction_data, 'pairs', []))->values();
                                    $rightOptions = $pairs
                                        ->map(
                                            fn($pair, $pairIndex) => array_merge($pair, ['match_index' => $pairIndex]),
                                        )
                                        ->shuffle()
                                        ->values();
                                    $savedMatches = json_decode(
                                        (string) old(
                                            'answers.' . $question->id,
                                            $submittedAnswers[(string) $question->id] ?? '',
                                        ),
                                        true,
                                    );
                                    $savedMatches = is_array($savedMatches) ? $savedMatches : [];
                                @endphp
                                <div class="matching-board">
                                    <div class="match-grid">
                                        @foreach ($pairs as $pairIndex => $pair)
                                            <div class="match-row">
                                                <div class="match-left">
                                                    <span class="match-left-index">{{ $pairIndex + 1 }}</span>
                                                    <span class="match-left-text">{{ $pair['left'] ?? '' }}</span>
                                                </div>
                                                <div class="match-drop">
                                                    <span class="match-drop-label">{{ __('ui.drop_answer') }}</span>
                                                    <span class="match-drop-icon"><i class="bi bi-arrow-down-circle"></i></span>
                                                </div>
                                                <select class="form-select match-select"
                                                    name="answers[{{ $question->id }}][{{ $pairIndex }}]"
                                                    form="assignmentSubmitForm" data-match-select>
                                                    <option value="">{{ __('ui.choose_matching_answer') }}</option>
                                                    @foreach ($rightOptions as $option)
                                                        <option value="{{ $option['match_index'] }}"
                                                            {{ (string) ($savedMatches[(string) $pairIndex] ?? '') === (string) $option['match_index'] ? 'selected' : '' }}>
                                                            {{ ($option['right_type'] ?? 'text') === 'image' ? __('ui.image_number', ['number' => $loop->iteration]) : $option['right'] ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="match-bank-wrap">
                                        <div class="match-bank-title">{{ __('ui.drag_answer') }}</div>
                                        <div class="match-bank" data-match-bank>
                                            @foreach ($rightOptions as $option)
                                                <div class="match-bank-card" data-match-option="{{ $option['match_index'] }}"
                                                    draggable="true">
                                                    <div class="match-bank-card-title">
                                                        <span>{{ ($option['right_type'] ?? 'text') === 'image' ? __('ui.image_number', ['number' => $loop->iteration]) : __('ui.answer_number', ['number' => $loop->iteration]) }}</span>
                                                        <span class="match-bank-card-used">{{ __('ui.selected') }}</span>
                                                    </div>
                                                    <div class="match-bank-card-body">
                                                        @if (($option['right_type'] ?? 'text') === 'image')
                                                            <img src="{{ $publicFileUrl($option['right'] ?? '') }}"
                                                                alt="{{ __('ui.matching_answer_image') }}" class="match-option-image">
                                                        @else
                                                            {{ $option['right'] ?? '' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @elseif($question->answer_mode === 'select')
                                <div class="choice-grid">
                                    @foreach ($question->options as $opt)
                                        <label class="choice-label">
                                            <input type="radio" name="answers[{{ $question->id }}]"
                                                value="{{ $opt->id }}" form="assignmentSubmitForm"
                                                {{ (string) old('answers.' . $question->id, $submittedAnswers[(string) $question->id] ?? '') === (string) $opt->id ? 'checked' : '' }}>
                                            <span>{{ $opt->option_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <input type="text" class="form-control answer-input"
                                    name="answers[{{ $question->id }}]" form="assignmentSubmitForm"
                                    value="{{ old('answers.' . $question->id, $submittedAnswers[(string) $question->id] ?? '') }}"
                                    placeholder="{{ __('ui.answer_placeholder') }}">
                            @endif

                            <div class="answer-check-feedback d-none" data-answer-feedback></div>

                            @error('answers.' . $question->id)
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </article>
                    @endforeach
                </div>

                <div class="practice-nav">
                    <button type="button" id="btnPrevQuestion" class="btn-app btn-light-purple"
                        onclick="showPrevQuestion()" disabled>
                        <i class="bi bi-arrow-left"></i>{{ __('ui.back') }}
                    </button>
                    <button type="button" id="btnCheckQuestion" class="btn-app btn-green"
                        onclick="checkCurrentQuestion()">
                        <i class="bi bi-check2-circle"></i>{{ __('ui.check') }}
                    </button>
                    <button type="button" id="btnNextQuestion" class="btn-app btn-purple" onclick="showNextQuestion()">
                        {{ __('ui.next') }} <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            @else
                <div class="plain-content">{!! nl2br(e($assignment->exercise->content)) !!}</div>
            @endif

            @if ($assignment->instructions)
                <div class="status-box info mt-3">
                    <strong><i class="bi bi-info-circle me-1"></i>{{ __('ui.additional_instructions') }}:</strong>
                    <p class="mb-0 mt-1">{{ $assignment->instructions }}</p>
                </div>
            @endif
        </div>

        <aside class="assignment-sidebar slide-up" style="animation-delay:.1s;">
            <div class="assignment-panel">
                <div class="assignment-panel-title mb-3">
                    <span class="icon"><i class="bi bi-send"></i></span>
                    {{ $submission ? __('ui.submission_status') : __('ui.submit_assignment') }}
                </div>

                <div class="d-grid gap-2 mb-3">
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">{{ __('ui.due_date') }}:</span>
                        <span
                            class="fw-bold text-primary">{{ $assignment->due_date ? $assignment->due_date->format('d/m/Y H:i') : __('ui.no_deadline') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">{{ __('ui.max_score') }}:</span>
                        <span class="fw-bold">{{ $assignment->max_score }}</span>
                    </div>
                </div>

                <div class="status-box info">
                    <div class="small">
                        {{ __('ui.submission_history_hint') }}
                    </div>
                </div>

                @if ($submission)
                    <div class="status-box {{ $submission->status === 'graded' ? 'success' : 'info' }}">
                        @if ($submission->status === 'graded')
                            <strong>{{ __('ui.score') }}: {{ $submission->score }}/{{ $assignment->max_score }}</strong>
                            @if ($submission->feedback)
                                <p class="mb-0 mt-1">{{ $submission->feedback }}</p>
                            @endif
                        @elseif($isQuestionFlow && !empty($submissionResult))
                            <strong>{{ __('ui.score') }}:
                                {{ data_get($submissionResult, 'score', $submission->score ?? 0) }}/{{ data_get($submissionResult, 'max_score', $assignment->max_score) }}</strong>
                            <p class="mb-0 mt-1">{{ __('ui.automatically_graded') }}</p>
                        @else
                            <i class="bi bi-hourglass-split me-1"></i>{{ __('ui.submitted_pending_grading') }}
                        @endif
                    </div>

                    @if ($isQuestionFlow && !empty($submissionResult))
                        <div class="status-box success">
                            <div class="fw-bold mb-1"><i class="bi bi-check2-circle me-1"></i>{{ __('ui.quick_result') }}</div>
                            <div class="small">
                                Ban lam dung
                                {{ __('ui.correct_count', ['correct' => $submissionResult['correct'], 'total' => $submissionResult['total']]) }}
                            </div>
                            <div class="small text-muted mt-1">
                                {{ __('ui.answered_count', ['answered' => $submissionResult['attempted'], 'total' => $submissionResult['total']]) }}
                            </div>
                        </div>
                    @endif

                    <p class="text-muted small mt-2">Ban co the nop lai bat ky luc nao. Lan nop moi se thay the lan cu hon
                        (chi giu 3 lan gan nhat).</p>
                @endif

                @if ($isQuestionFlow)
                    <a href="{{ route('student.assignments.show', $assignment->id) }}"
                        class="btn-app btn-light-purple w-100 justify-content-center mt-2 text-decoration-none">
                        <i class="bi bi-arrow-left"></i>{{ __('ui.back_to_result') }}
                    </a>
                    <button type="button" id="btnStartAssignment"
                        class="btn-app btn-purple w-100 justify-content-center mt-2" onclick="startAssignmentPractice()">
                        <i class="bi bi-play-circle"></i>{{ $submission ? __('ui.retry_assignment') : __('ui.do_assignment') }}
                    </button>
                @endif

                <form method="POST" action="/assignments/{{ $assignment->id }}/submit" class="mt-3"
                    id="assignmentSubmitForm">
                    @csrf

                    @if (!$isQuestionFlow)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('ui.your_work') }}</label>
                            <textarea name="content" rows="8" class="form-control answer-textarea @error('content') is-invalid @enderror"
                                placeholder="{{ __('ui.your_work_placeholder') }}">{{ old('content', '') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <button type="submit" id="btnSubmitAssignment"
                        class="btn-app btn-green w-100 justify-content-center {{ $isQuestionFlow ? 'd-none' : '' }}">
                        <i class="bi bi-send"></i>{{ $submission ? __('ui.submit_new_attempt') : __('ui.submit_assignment') }}
                    </button>
                </form>
            </div>
        </aside>
    </div>
    @endif
@endsection

@push('scripts')
    @if ($practiceMode && $isQuestionFlow)
        <script>
            let currentQuestionIndex = 0;
            const questionCards = Array.from(document.querySelectorAll('.question-card'));
            const questionChecks = @json($questionChecks, JSON_UNESCAPED_UNICODE);
            const uiText = @json([
                'answerRequired' => __('ui.answer_required_feedback'),
                'answerCorrect' => __('ui.answer_correct_feedback'),
                'answerIncorrect' => __('ui.answer_incorrect_feedback'),
                'dropAnswer' => __('ui.drop_answer'),
                'lastQuestion' => __('ui.last_question'),
                'next' => __('ui.next'),
            ], JSON_UNESCAPED_UNICODE);
            const checkedState = questionCards.map(() => false);

            function renderQuestionNav() {
                questionCards.forEach((card, idx) => {
                    card.classList.toggle('d-none', idx !== currentQuestionIndex);
                });

                const btnPrev = document.getElementById('btnPrevQuestion');
                const btnNext = document.getElementById('btnNextQuestion');
                const btnSubmit = document.getElementById('btnSubmitAssignment');
                const isLastQuestion = currentQuestionIndex >= questionCards.length - 1;

                if (btnPrev) btnPrev.disabled = currentQuestionIndex === 0;
                if (btnSubmit) btnSubmit.classList.toggle('d-none', !isLastQuestion);
                if (btnNext) {
                    if (isLastQuestion) {
                        btnNext.disabled = true;
                        btnNext.classList.add('disabled');
                        btnNext.innerHTML = uiText.lastQuestion;
                    } else {
                        btnNext.disabled = false;
                        btnNext.classList.remove('disabled');
                        btnNext.innerHTML = uiText.next + ' <i class="bi bi-arrow-right"></i>';
                    }
                }
            }

            function showPrevQuestion() {
                if (currentQuestionIndex > 0) {
                    currentQuestionIndex--;
                    renderQuestionNav();
                }
            }

            function showNextQuestion() {
                if (currentQuestionIndex < questionCards.length - 1) {
                    currentQuestionIndex++;
                    renderQuestionNav();
                }
            }

            function startAssignmentPractice() {
                const layout = document.getElementById('assignmentWorkLayout');
                const panel = document.getElementById('assignmentPracticePanel');

                if (panel) panel.classList.remove('d-none');
                if (layout) layout.classList.remove('result-only');

                currentQuestionIndex = 0;
                renderQuestionNav();

                const wrap = document.getElementById('questionCardsWrap') || panel;
                if (wrap) {
                    wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            function updateOrderingAnswer(list) {
                const card = list.closest('.practice-card');
                const hidden = card ? card.querySelector('.ordering-answer') : null;
                if (!hidden) return;

                const items = Array.from(list.querySelectorAll('.ordering-item'));
                hidden.value = items.map(item => item.dataset.itemIndex).join(',');
                items.forEach((item, index) => {
                    const rank = item.querySelector('.ordering-rank');
                    if (rank) rank.textContent = index + 1;
                });
            }

            function moveOrderingItem(button, direction) {
                const item = button.closest('.ordering-item');
                const list = button.closest('[data-ordering-list]');
                if (!item || !list) return;

                if (direction < 0 && item.previousElementSibling) {
                    list.insertBefore(item, item.previousElementSibling);
                }

                if (direction > 0 && item.nextElementSibling) {
                    list.insertBefore(item.nextElementSibling, item);
                }

                updateOrderingAnswer(list);
                resetQuestionCheck(list.closest('.practice-card'));
            }

            function getDragAfterElement(container, y) {
                const items = [...container.querySelectorAll('.ordering-item:not(.dragging)')];

                return items.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;

                    if (offset < 0 && offset > closest.offset) {
                        return {
                            offset,
                            element: child
                        };
                    }

                    return closest;
                }, {
                    offset: Number.NEGATIVE_INFINITY,
                    element: null
                }).element;
            }

            function initOrderingDragDrop() {
                document.querySelectorAll('[data-ordering-list]').forEach(list => {
                    list.querySelectorAll('.ordering-item').forEach(item => {
                        item.addEventListener('dragstart', () => {
                            item.classList.add('dragging');
                        });

                        item.addEventListener('dragend', () => {
                            item.classList.remove('dragging');
                            updateOrderingAnswer(list);
                            resetQuestionCheck(list.closest('.practice-card'));
                        });
                    });

                    list.addEventListener('dragover', event => {
                        event.preventDefault();
                        const draggingItem = list.querySelector('.dragging');
                        if (!draggingItem) return;

                        const afterElement = getDragAfterElement(list, event.clientY);
                        if (afterElement == null) {
                            list.appendChild(draggingItem);
                        } else {
                            list.insertBefore(draggingItem, afterElement);
                        }
                    });
                });
            }

            function updateMatchingUi(card) {
                if (!card) return;

                const selects = Array.from(card.querySelectorAll('[data-match-select]'));
                const selectedValues = selects.map(select => select.value).filter(Boolean);

                selects.forEach(select => {
                    Array.from(select.options).forEach(option => {
                        if (!option.value) return;
                        option.disabled = option.value !== select.value && selectedValues.includes(option.value);
                    });

                    const row = select.closest('.match-row');
                    const label = row?.querySelector('.match-drop-label');
                    const selectedOption = select.selectedOptions && select.selectedOptions[0] ? select.selectedOptions[0] : null;
                    const hasValue = Boolean(select.value);

                    row?.classList.toggle('is-filled', hasValue);
                    if (label) {
                        label.textContent = hasValue && selectedOption ? selectedOption.textContent.trim() : uiText.dropAnswer;
                    }
                });

                card.querySelectorAll('[data-match-bank] [data-match-option]').forEach(optionCard => {
                    optionCard.classList.toggle('selected', selectedValues.includes(optionCard.dataset.matchOption));
                });
            }

            function assignMatchOptionToSelect(optionValue, targetSelect) {
                const card = targetSelect.closest('.practice-card');
                if (!card || !optionValue) return;

                card.querySelectorAll('[data-match-select]').forEach(select => {
                    if (select !== targetSelect && select.value === optionValue) {
                        select.value = '';
                    }
                });

                targetSelect.value = optionValue;
                setActiveMatchRow(targetSelect);
                updateMatchingUi(card);
                resetQuestionCheck(card);
            }

            function setActiveMatchRow(select) {
                const card = select.closest('.practice-card');
                if (!card) return;

                card.querySelectorAll('.match-row').forEach(row => row.classList.remove('active'));
                select.closest('.match-row')?.classList.add('active');
                card.dataset.activeMatchName = select.name;
            }

            function chooseMatchOption(optionCard) {
                const card = optionCard.closest('.practice-card');
                if (!card) return;

                const selects = Array.from(card.querySelectorAll('[data-match-select]'));
                const activeSelect = selects.find(select => select.name === card.dataset.activeMatchName);
                const targetSelect = activeSelect || selects.find(select => !select.value) || selects[0];

                if (targetSelect) {
                    assignMatchOptionToSelect(optionCard.dataset.matchOption, targetSelect);
                }
            }

            function initMatchingDragDrop() {
                document.querySelectorAll('.practice-card').forEach(card => {
                    card.querySelectorAll('[data-match-bank] [data-match-option]').forEach(optionCard => {
                        optionCard.addEventListener('dragstart', event => {
                            optionCard.classList.add('dragging');
                            event.dataTransfer.effectAllowed = 'move';
                            event.dataTransfer.setData('text/plain', optionCard.dataset.matchOption);
                        });

                        optionCard.addEventListener('dragend', () => {
                            optionCard.classList.remove('dragging');
                        });
                    });

                    card.querySelectorAll('.match-row').forEach(row => {
                        row.addEventListener('click', () => {
                            const targetSelect = row.querySelector('[data-match-select]');
                            if (targetSelect) setActiveMatchRow(targetSelect);
                        });

                        row.addEventListener('dragover', event => {
                            event.preventDefault();
                            row.classList.add('drop-target');
                            event.dataTransfer.dropEffect = 'move';
                        });

                        row.addEventListener('dragleave', () => {
                            row.classList.remove('drop-target');
                        });

                        row.addEventListener('drop', event => {
                            event.preventDefault();
                            row.classList.remove('drop-target');

                            const optionValue = event.dataTransfer.getData('text/plain');
                            const targetSelect = row.querySelector('[data-match-select]');
                            if (targetSelect) assignMatchOptionToSelect(optionValue, targetSelect);
                        });
                    });
                });
            }

            function normalizeText(value) {
                return String(value || '').trim().toLocaleLowerCase('vi-VN');
            }

            function normalizeMatching(value) {
                const normalized = {};
                Object.keys(value || {}).sort().forEach(key => {
                    normalized[String(key)] = String(value[key] ?? '');
                });

                return JSON.stringify(normalized);
            }

            function getCurrentAnswer(card, check) {
                if (!card || !check) return '';

                if (check.type === 'ordering') {
                    const list = card.querySelector('[data-ordering-list]');
                    if (list) updateOrderingAnswer(list);
                    return card.querySelector('.ordering-answer')?.value || '';
                }

                if (check.type === 'matching') {
                    const answer = {};
                    card.querySelectorAll('select[name^="answers["]').forEach(select => {
                        const match = select.name.match(/\[(\d+)\]$/);
                        if (match) answer[String(match[1])] = select.value;
                    });

                    return answer;
                }

                if (check.type === 'select') {
                    return card.querySelector('input[type="radio"]:checked')?.value || '';
                }

                return card.querySelector('.answer-input')?.value || '';
            }

            function hasAnswer(answer, check) {
                if (check.type === 'matching') {
                    const expectedCount = Object.keys(check.expected || {}).length;
                    const values = Object.values(answer || {});
                    return values.length === expectedCount && values.every(value => String(value || '') !== '');
                }

                return String(answer || '').trim() !== '';
            }

            function isCorrectAnswer(answer, check) {
                if (check.type === 'matching') {
                    return normalizeMatching(answer) === normalizeMatching(check.expected || {});
                }

                if (check.type === 'input') {
                    return normalizeText(answer) === normalizeText(check.expected);
                }

                return String(answer || '') === String(check.expected || '');
            }

            function setQuestionFeedback(card, isCorrect, message) {
                const feedback = card ? card.querySelector('[data-answer-feedback]') : null;
                if (!feedback) return;

                feedback.textContent = message;
                feedback.classList.remove('d-none', 'success', 'danger');
                feedback.classList.add(isCorrect ? 'success' : 'danger');
            }

            function resetQuestionCheck(card) {
                if (!card) return;

                const index = Number(card.dataset.questionIndex);
                checkedState[index] = false;

                const feedback = card.querySelector('[data-answer-feedback]');
                if (feedback) {
                    feedback.classList.add('d-none');
                    feedback.classList.remove('success', 'danger');
                    feedback.textContent = '';
                }

                renderQuestionNav();
            }

            function checkCurrentQuestion() {
                const card = questionCards[currentQuestionIndex];
                const questionId = card ? card.dataset.questionId : null;
                const check = questionId ? questionChecks[questionId] : null;
                const answer = getCurrentAnswer(card, check);

                if (!check || !hasAnswer(answer, check)) {
                    checkedState[currentQuestionIndex] = false;
                    setQuestionFeedback(card, false, uiText.answerRequired);
                    renderQuestionNav();
                    return {
                        hasAnswer: false,
                        isCorrect: false
                    };
                }

                const isCorrect = isCorrectAnswer(answer, check);
                checkedState[currentQuestionIndex] = isCorrect;
                setQuestionFeedback(card, isCorrect, isCorrect ? uiText.answerCorrect : uiText.answerIncorrect);

                renderQuestionNav();
                return {
                    hasAnswer: true,
                    isCorrect
                };
            }

            questionCards.forEach(card => {
                card.querySelectorAll('input[type="radio"], .answer-input, select').forEach(field => {
                    if (field.matches('[data-match-select]')) {
                        field.addEventListener('focus', () => setActiveMatchRow(field));
                        field.addEventListener('click', () => setActiveMatchRow(field));
                    }

                    field.addEventListener('change', () => {
                        if (field.matches('[data-match-select]')) {
                            setActiveMatchRow(field);
                            updateMatchingUi(card);
                        }
                        resetQuestionCheck(card);
                    });
                    field.addEventListener('input', () => resetQuestionCheck(card));
                });

                card.querySelectorAll('[data-match-bank] [data-match-option]').forEach(optionCard => {
                    optionCard.addEventListener('click', () => chooseMatchOption(optionCard));
                });

                updateMatchingUi(card);
            });

            document.querySelectorAll('[data-ordering-list]').forEach(updateOrderingAnswer);
            initOrderingDragDrop();
            initMatchingDragDrop();
            renderQuestionNav();
        </script>
    @endif
@endpush
