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
    .assignment-hero > * {
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
        grid-template-columns: repeat(3, minmax(0, 1fr));
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
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 18px;
        align-items: start;
    }
    .assignment-panel {
        background: linear-gradient(180deg, rgba(255,255,255,.96), #ffffff);
        border-radius: 24px;
        border: 1px solid #e7ecff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
        padding: 18px;
    }
    .assignment-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
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
        border-radius: 18px;
        padding: 14px 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eff6ff 100%);
        border: 1px solid #dbeafe;
        color: #1e3a8a;
        font-size: .9rem;
        font-weight: 700;
        margin-bottom: 14px;
    }
    .practice-card {
        border-radius: 22px;
        border: 1px solid #e9edff;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
        padding: 16px;
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
        border-radius: 18px;
        padding: 14px 16px;
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
        border-radius: 16px;
        padding: 12px 14px;
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
        min-height: 52px;
        border-radius: 18px;
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
        justify-content: space-between;
        gap: 12px;
        border: 1px solid #dbe4ff;
        border-radius: 16px;
        padding: 12px 14px;
        background: #fff;
        font-weight: 800;
        color: #334155;
    }
    .ordering-actions {
        display: inline-flex;
        gap: 6px;
        flex-shrink: 0;
    }
    .match-grid {
        display: grid;
        gap: 12px;
        margin-top: 12px;
    }
    .match-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(180px, 260px);
        gap: 10px;
        align-items: center;
        border: 1px solid #dbe4ff;
        border-radius: 16px;
        padding: 12px;
        background: #fff;
    }
    .match-left {
        font-weight: 900;
        color: #0f172a;
    }
    .match-option-image {
        max-width: 90px;
        max-height: 60px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        object-fit: cover;
        display: block;
        margin-top: 6px;
    }
    .practice-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 14px;
    }
    .assignment-sidebar {
        position: sticky;
        top: 82px;
    }
    .status-box {
        border-radius: 18px;
        padding: 14px 16px;
        margin-bottom: 12px;
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
        border-radius: 18px;
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
    }
    @media (max-width: 767.98px) {
        .assignment-meta-grid {
            grid-template-columns: 1fr;
        }
        .practice-head,
        .practice-nav {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
        <li class="breadcrumb-item">
            <a href="/classes/{{ $assignment->session->class_id }}">
                {{ $assignment->session->schoolClass->name ?? 'Lớp học' }}
            </a>
        </li>
        <li class="breadcrumb-item active">{{ $assignment->exercise->title }}</li>
    </ol>
</nav>

@php
    $isQuestionFlow = isset($generatedQuestions) && $generatedQuestions->isNotEmpty();
@endphp

<section class="assignment-hero slide-up">
    <div class="assignment-badge">
        <i class="bi bi-stars"></i>
        Khu vuc hoc tap cua ban
    </div>

    <div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div>
            <h2 class="mb-2" style="font-size:1.8rem;font-weight:900;line-height:1.15;">{{ $assignment->exercise->title }}</h2>
            <div style="font-size:.95rem;opacity:.92;max-width:560px;">
                {{ $assignment->exercise->description ?: 'Hoan thanh bai tap va nop bai dung han de theo doi tien do hoc tap.' }}
            </div>
        </div>
        <span class="s-tag tag-blue" style="background:rgba(255,255,255,.16);color:#fff;">{{ ucfirst($assignment->exercise->type) }}</span>
    </div>

    <div class="assignment-meta-grid">
        <div class="assignment-meta-card">
            <div class="value">{{ ucfirst($assignment->exercise->difficulty) }}</div>
            <div class="label">Do kho</div>
        </div>
        <div class="assignment-meta-card">
            <div class="value">{{ $assignment->due_date->format('d/m H:i') }}</div>
            <div class="label">Han nop</div>
        </div>
        <div class="assignment-meta-card">
            <div class="value">{{ $assignment->max_score }}</div>
            <div class="label">Diem toi da</div>
        </div>
    </div>
</section>

<div class="assignment-layout">
    <div class="assignment-panel slide-up" style="animation-delay:.05s;">
        <div class="assignment-panel-header">
            <div class="assignment-panel-title">
                <span class="icon"><i class="bi bi-journal-text"></i></span>
                Noi dung bai tap
            </div>
            @if($assignment->isGeneratedFromQuestionBank())
                <span class="s-tag tag-blue">{{ $assignment->generated_question_count ? $assignment->generated_question_count . ' cau hoi' : 'Sinh tu kho cau hoi' }}</span>
            @endif
        </div>

        @if($isQuestionFlow)
            <div class="question-flow-note">
                Bai luyen tap dang tung cau. Dung nut <strong>Tiep theo</strong> de chuyen cau va kiem tra dap an truoc khi nop bai.
            </div>

            <div id="questionCardsWrap">
                @foreach($generatedQuestions as $idx => $question)
                    <article class="practice-card question-card {{ $idx === 0 ? '' : 'd-none' }}" data-question-index="{{ $idx }}">
                        <div class="practice-head">
                            <div>
                                <div class="practice-index">
                                    <i class="bi bi-patch-question"></i>
                                    Cau {{ $idx + 1 }}
                                </div>
                                <div class="practice-title">{{ $question->question_text }}</div>
                            </div>
                            <span class="practice-type">
                                @if(($question->interaction_type ?? 'normal') === 'ordering')
                                    <i class="bi bi-sort-down"></i> Sap xep
                                @elseif(($question->interaction_type ?? 'normal') === 'matching')
                                    <i class="bi bi-diagram-3"></i> Noi dap an
                                @else
                                    <i class="bi {{ $question->answer_mode === 'select' ? 'bi-ui-radios-grid' : 'bi-pencil-square' }}"></i>
                                    {{ $question->answer_mode === 'select' ? 'Chon dap an' : 'Dien dap an' }}
                                @endif
                            </span>
                        </div>

                        @if($question->context_type === 'reading' && $question->passage)
                            <div class="context-box reading">
                                <strong>Doc hieu:</strong><br>
                                {!! nl2br(e($question->passage)) !!}
                            </div>
                        @endif

                        @if($question->context_type === 'listening' && $question->audio_url)
                            <div class="context-box listening">
                                <strong>Nghe:</strong>
                                <a href="{{ $question->audio_url }}" target="_blank" rel="noopener">Mo audio</a>
                            </div>
                        @endif

                        @if(($question->interaction_type ?? 'normal') === 'ordering')
                            @php
                                $items = collect(data_get($question->interaction_data, 'items', []))->values();
                                $savedOrder = array_values(array_filter(explode(',', (string) old('answers.' . $question->id, $submittedAnswers[(string)$question->id] ?? '')), fn ($v) => $v !== ''));
                                $order = !empty($savedOrder) ? collect($savedOrder)->map(fn ($v) => (int) $v)->filter(fn ($v) => $items->has($v))->values() : $items->keys()->shuffle()->values();
                                $missing = $items->keys()->diff($order);
                                $order = $order->merge($missing)->values();
                            @endphp
                            <input type="hidden" class="ordering-answer" name="answers[{{ $question->id }}]" form="assignmentSubmitForm" value="{{ $order->implode(',') }}">
                            <div class="ordering-list" data-ordering-list>
                                @foreach($order as $itemIndex)
                                    <div class="ordering-item" data-item-index="{{ $itemIndex }}">
                                        <span>{{ $items[$itemIndex] ?? '' }}</span>
                                        <span class="ordering-actions">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="moveOrderingItem(this, -1)"><i class="bi bi-arrow-up"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="moveOrderingItem(this, 1)"><i class="bi bi-arrow-down"></i></button>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(($question->interaction_type ?? 'normal') === 'matching')
                            @php
                                $pairs = collect(data_get($question->interaction_data, 'pairs', []))->values();
                                $rightOptions = $pairs->map(fn ($pair, $pairIndex) => array_merge($pair, ['match_index' => $pairIndex]))->shuffle()->values();
                                $savedMatches = json_decode((string) old('answers.' . $question->id, $submittedAnswers[(string)$question->id] ?? ''), true);
                                $savedMatches = is_array($savedMatches) ? $savedMatches : [];
                            @endphp
                            <div class="match-grid">
                                @foreach($pairs as $pairIndex => $pair)
                                    <div class="match-row">
                                        <div class="match-left">{{ $pair['left'] ?? '' }}</div>
                                        <select class="form-select" name="answers[{{ $question->id }}][{{ $pairIndex }}]" form="assignmentSubmitForm">
                                            <option value="">-- Chọn đáp án nối --</option>
                                            @foreach($rightOptions as $option)
                                                <option value="{{ $option['match_index'] }}" {{ (string) ($savedMatches[(string) $pairIndex] ?? '') === (string) $option['match_index'] ? 'selected' : '' }}>
                                                    {{ ($option['right_type'] ?? 'text') === 'image' ? 'Ảnh ' . ($loop->iteration) : ($option['right'] ?? '') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row g-2 mt-2">
                                @foreach($rightOptions as $option)
                                    @if(($option['right_type'] ?? 'text') === 'image')
                                        <div class="col-6 col-md-4">
                                            <div class="small fw-bold text-muted">Ảnh {{ $loop->iteration }}</div>
                                            <img src="{{ $option['right'] ?? '' }}" alt="Đáp án nối" class="match-option-image">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @elseif($question->answer_mode === 'select')
                            <div class="choice-grid">
                                @foreach($question->options as $opt)
                                    <label class="choice-label">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt->id }}" form="assignmentSubmitForm"
                                            {{ (string) old('answers.' . $question->id, $submittedAnswers[(string)$question->id] ?? '') === (string) $opt->id ? 'checked' : '' }}>
                                        <span>{{ $opt->option_text }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <input
                                type="text"
                                class="form-control answer-input"
                                name="answers[{{ $question->id }}]"
                                form="assignmentSubmitForm"
                                value="{{ old('answers.' . $question->id, $submittedAnswers[(string)$question->id] ?? '') }}"
                                placeholder="Nhap dap an cua ban"
                            >
                        @endif

                        @error('answers.' . $question->id)
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </article>
                @endforeach
            </div>

            <div class="practice-nav">
                <button type="button" id="btnPrevQuestion" class="btn-app btn-light-purple" onclick="showPrevQuestion()" disabled>
                    <i class="bi bi-arrow-left"></i>Cau truoc
                </button>
                <button type="button" id="btnNextQuestion" class="btn-app btn-purple" onclick="showNextQuestion()">
                    Cau tiep theo <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        @else
            <div class="plain-content">{!! nl2br(e($assignment->exercise->content)) !!}</div>
        @endif

        @if($assignment->instructions)
            <div class="status-box info mt-3">
                <strong><i class="bi bi-info-circle me-1"></i>Huong dan them:</strong>
                <p class="mb-0 mt-1">{{ $assignment->instructions }}</p>
            </div>
        @endif
    </div>

    <aside class="assignment-sidebar slide-up" style="animation-delay:.1s;">
        <div class="assignment-panel">
            <div class="assignment-panel-title mb-3">
                <span class="icon"><i class="bi bi-send"></i></span>
                {{ $submission ? 'Trang thai bai nop' : 'Nop bai' }}
            </div>

            <div class="d-grid gap-2 mb-3">
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Han nop:</span>
                    <span class="fw-bold text-primary">{{ $assignment->due_date->format('d/m/Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Diem toi da:</span>
                    <span class="fw-bold">{{ $assignment->max_score }}</span>
                </div>
            </div>

            <div class="status-box info">
                <div class="small">
                    Ban co the nop bai thoai mai. He thong se giu lai <strong>3 lan nop gan nhat</strong>.
                </div>
            </div>

            @if($submission)
                <div class="status-box {{ $submission->status === 'graded' ? 'success' : 'info' }}">
                    @if($submission->status === 'graded')
                        <strong>Diem: {{ $submission->score }}/{{ $assignment->max_score }}</strong>
                        @if($submission->feedback)
                            <p class="mb-0 mt-1">{{ $submission->feedback }}</p>
                        @endif
                    @else
                        <i class="bi bi-hourglass-split me-1"></i>Bai da nop, dang cho cham diem.
                    @endif
                </div>

                @if($isQuestionFlow && !empty($submissionResult))
                    <div class="status-box success">
                        <div class="fw-bold mb-1"><i class="bi bi-check2-circle me-1"></i>Ket qua nhanh</div>
                        <div class="small">
                            Ban lam dung <strong>{{ $submissionResult['correct'] }}</strong>/{{ $submissionResult['total'] }} cau.
                        </div>
                        <div class="small text-muted mt-1">
                            Da tra loi: {{ $submissionResult['attempted'] }}/{{ $submissionResult['total'] }} cau.
                        </div>
                    </div>
                @endif

                <p class="text-muted small mt-2">Ban co the nop lai bat ky luc nao. Lan nop moi se thay the lan cu hon (chi giu 3 lan gan nhat).</p>
            @endif

            <form method="POST" action="/assignments/{{ $assignment->id }}/submit" class="mt-3" id="assignmentSubmitForm">
                @csrf

                @if(!$isQuestionFlow)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bai lam cua ban</label>
                        <textarea name="content" rows="8" class="form-control answer-textarea @error('content') is-invalid @enderror"
                            placeholder="Viet bai lam cua ban tai day...">{{ old('content', $submission ? $submission->content : '') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <button type="submit" class="btn-app btn-green w-100 justify-content-center">
                    <i class="bi bi-send"></i>{{ $submission ? 'Nop lan moi' : 'Nop bai' }}
                </button>
            </form>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
@if($isQuestionFlow)
<script>
let currentQuestionIndex = 0;
const questionCards = Array.from(document.querySelectorAll('.question-card'));

function renderQuestionNav() {
    questionCards.forEach((card, idx) => {
        card.classList.toggle('d-none', idx !== currentQuestionIndex);
    });

    const btnPrev = document.getElementById('btnPrevQuestion');
    const btnNext = document.getElementById('btnNextQuestion');

    if (btnPrev) btnPrev.disabled = currentQuestionIndex === 0;
    if (btnNext) {
        if (currentQuestionIndex >= questionCards.length - 1) {
            btnNext.disabled = true;
            btnNext.classList.add('disabled');
            btnNext.innerHTML = 'Đang ở câu cuối';
        } else {
            btnNext.disabled = false;
            btnNext.classList.remove('disabled');
            btnNext.innerHTML = 'Câu tiếp theo <i class="bi bi-arrow-right"></i>';
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

function updateOrderingAnswer(list) {
    const card = list.closest('.practice-card');
    const hidden = card ? card.querySelector('.ordering-answer') : null;
    if (!hidden) return;

    hidden.value = Array.from(list.querySelectorAll('.ordering-item'))
        .map(item => item.dataset.itemIndex)
        .join(',');
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
}

document.querySelectorAll('[data-ordering-list]').forEach(updateOrderingAnswer);
renderQuestionNav();
</script>
@endif
@endpush
