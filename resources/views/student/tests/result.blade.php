@extends('layouts.app')
@section('title', 'Káº¿t quáº£ bÃ i kiá»ƒm tra')

@push('styles')
<style>
    .result-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 24px 22px;
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 38%, #06b6d4 100%);
        color: #fff;
        box-shadow: 0 22px 50px rgba(37, 99, 235, .22);
    }
    .result-hero::before,
    .result-hero::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
    }
    .result-hero::before {
        width: 180px;
        height: 180px;
        top: -60px;
        right: -30px;
    }
    .result-hero::after {
        width: 120px;
        height: 120px;
        bottom: -35px;
        left: -20px;
    }
    .result-hero > * {
        position: relative;
        z-index: 1;
    }
    .result-badge {
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
    .result-score-box {
        min-width: 180px;
        border-radius: 24px;
        padding: 18px 20px;
        background: rgba(255, 255, 255, .14);
        backdrop-filter: blur(8px);
        text-align: center;
    }
    .result-score-box .score {
        font-size: 2.2rem;
        font-weight: 900;
        line-height: 1;
    }
    .result-score-box .meta {
        margin-top: 8px;
        font-size: .82rem;
        font-weight: 700;
        opacity: .9;
    }
    .result-shell {
        margin-top: 18px;
    }
    .result-panel {
        background: linear-gradient(180deg, rgba(255,255,255,.96), #ffffff);
        border-radius: 24px;
        border: 1px solid #e7ecff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
        padding: 18px;
    }
    .result-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }
    .result-panel-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        font-weight: 900;
        color: #0f172a;
    }
    .result-panel-title .icon {
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
    .result-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }
    .result-stat {
        border-radius: 20px;
        padding: 14px 16px;
        background: rgba(255, 255, 255, .12);
        backdrop-filter: blur(8px);
    }
    .result-stat .value {
        font-size: 1.75rem;
        font-weight: 900;
        line-height: 1;
    }
    .result-stat .label {
        margin-top: 6px;
        font-size: .78rem;
        font-weight: 700;
        opacity: .88;
    }
    .answer-list {
        display: grid;
        gap: 14px;
    }
    .answer-card {
        border-radius: 22px;
        border: 1px solid #e9edff;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
        padding: 16px;
    }
    .answer-card.correct {
        border-color: #86efac;
        box-shadow: 0 14px 24px rgba(34, 197, 94, .10);
    }
    .answer-card.incorrect {
        border-color: #fca5a5;
        box-shadow: 0 14px 24px rgba(239, 68, 68, .10);
    }
    .answer-card.pending {
        border-color: #fde68a;
        box-shadow: 0 14px 24px rgba(245, 158, 11, .10);
    }
    .answer-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }
    .answer-index {
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
    .answer-question {
        font-weight: 900;
        color: #0f172a;
        white-space: pre-line;
    }
    .answer-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 900;
        white-space: nowrap;
    }
    .answer-status.correct {
        background: #dcfce7;
        color: #166534;
    }
    .answer-status.incorrect {
        background: #fee2e2;
        color: #b91c1c;
    }
    .answer-status.pending {
        background: #fef3c7;
        color: #92400e;
    }
    .answer-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 12px;
    }
    .answer-box {
        border-radius: 18px;
        padding: 14px;
        background: #fff;
        border: 1px solid #edf2ff;
    }
    .answer-box-label {
        font-size: .74rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: #64748b;
        margin-bottom: 8px;
    }
    .answer-box-value {
        font-size: .92rem;
        font-weight: 700;
        color: #0f172a;
        white-space: pre-line;
    }
    .answer-box-value.muted {
        color: #94a3b8;
        font-style: italic;
    }
    .result-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 18px;
        flex-wrap: wrap;
    }
    @media (max-width: 767.98px) {
        .result-stats,
        .answer-grid {
            grid-template-columns: 1fr;
        }
        .answer-head,
        .result-hero-main {
            flex-direction: column;
            align-items: flex-start;
        }
        .result-score-box {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $test  = $submission->test;
    $score = $submission->total_score ?? 0;
    $pct   = $test->total_score > 0 ? round($score / $test->total_score * 100) : 0;
    $pass  = $pct >= 50;
    $sortedQuestions = $test->questions->sortBy('order_index')->values();
    $gradedAnswers = $submission->answers->filter(fn ($answer) => $answer->is_correct !== null);
    $correctCount = $gradedAnswers->where('is_correct', true)->count();
    $pendingCount = $submission->answers->where('is_correct', null)->count();
@endphp

<section class="result-hero slide-up">
    <div class="result-badge">
        <i class="bi bi-stars"></i>
        Ket qua bai kiem tra
    </div>

    <div class="result-hero-main mt-3 d-flex justify-content-between align-items-md-end gap-3">
        <div>
            <div style="font-size:2.4rem;line-height:1;">{{ $pass ? 'ðŸŽ‰' : 'ðŸ’ª' }}</div>
            <h2 class="mb-2 mt-2" style="font-size:1.8rem;font-weight:900;line-height:1.15;">{{ $pass ? 'Chuc mung, ban da hoan thanh bai kiem tra!' : 'Ban da hoan thanh bai kiem tra!' }}</h2>
            <div style="font-size:.95rem;opacity:.92;max-width:560px;">
                {{ $test->title }}
            </div>
        </div>

        <div class="result-score-box">
            <div class="score">{{ number_format($score, 1) }}/{{ $test->total_score }}</div>
            <div class="meta">Ty le dat duoc: {{ $pct }}%</div>
        </div>
    </div>

    <div class="result-stats">
        <div class="result-stat">
            <div class="value">{{ $sortedQuestions->count() }}</div>
            <div class="label">Tong so cau</div>
        </div>
        <div class="result-stat">
            <div class="value">{{ $correctCount }}</div>
            <div class="label">Cau dung da cham</div>
        </div>
        <div class="result-stat">
            <div class="value">{{ $pendingCount }}</div>
            <div class="label">Cau dang cho cham</div>
        </div>
    </div>
</section>

<div class="result-shell">
    <section class="result-panel slide-up" style="animation-delay:.08s;">
        <div class="result-panel-header">
            <div class="result-panel-title">
                <span class="icon"><i class="bi bi-list-check"></i></span>
                Chi tiet tung cau
            </div>
            <span class="s-tag {{ $pass ? 'tag-green' : 'tag-orange' }}">{{ $pass ? 'Dat yeu cau' : 'Can co gang them' }}</span>
        </div>

        <div class="answer-list">
            @foreach($sortedQuestions as $idx => $question)
                @php
                    $answer = $submission->answers->firstWhere('question_id', $question->id);
                    $statusClass = 'pending';
                    $statusLabel = 'Cho cham';

                    if ($answer && $answer->is_correct === true) {
                        $statusClass = 'correct';
                        $statusLabel = 'Chinh xac';
                    } elseif ($answer && $answer->is_correct === false) {
                        $statusClass = 'incorrect';
                        $statusLabel = 'Chua dung';
                    }

                    $answerText = 'Khong tra loi';
                    if ($answer) {
                        if ($answer->selectedOption) {
                            $answerText = $answer->selectedOption->option_text;
                        } elseif ($answer->answer_text) {
                            $answerText = $answer->answer_text;
                        }
                    }
                @endphp

                <article class="answer-card {{ $statusClass }}">
                    <div class="answer-head">
                        <div>
                            <div class="answer-index">
                                <i class="bi bi-patch-question"></i>
                                Cau {{ $idx + 1 }}
                            </div>
                            <div class="answer-question">{{ $question->question_text }}</div>
                        </div>

                        <span class="answer-status {{ $statusClass }}">
                            <i class="bi {{ $statusClass === 'correct' ? 'bi-check-circle-fill' : ($statusClass === 'incorrect' ? 'bi-x-circle-fill' : 'bi-hourglass-split') }}"></i>
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="answer-grid">
                        <div class="answer-box">
                            <div class="answer-box-label">Cau tra loi cua ban</div>
                            <div class="answer-box-value {{ $answerText === 'Khong tra loi' ? 'muted' : '' }}">{{ $answerText }}</div>
                        </div>
                        <div class="answer-box">
                            <div class="answer-box-label">Diem nhan duoc</div>
                            <div class="answer-box-value">{{ $answer ? number_format($answer->score ?? 0, 1) : '0' }}/{{ $question->score }}</div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div class="result-actions">
        <a href="/" class="btn-app btn-light-purple"><i class="bi bi-house"></i>Ve dashboard</a>
        <a href="/classes/{{ optional($submission->testSession)->class_id ?: $test->class_id }}" class="btn-app btn-purple"><i class="bi bi-journal-bookmark"></i>Ve lop hoc</a>
    </div>
</div>
@endsection

