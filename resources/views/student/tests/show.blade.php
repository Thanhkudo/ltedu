@extends('layouts.app')
@section('title', $test->title . ' â€” LÃ m bÃ i')

@push('styles')
<style>
    .test-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 24px 22px;
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 38%, #06b6d4 100%);
        color: #fff;
        box-shadow: 0 22px 50px rgba(37, 99, 235, .22);
        margin-bottom: 18px;
    }
    .test-hero::before,
    .test-hero::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
    }
    .test-hero::before {
        width: 180px;
        height: 180px;
        top: -60px;
        right: -30px;
    }
    .test-hero::after {
        width: 120px;
        height: 120px;
        bottom: -35px;
        left: -20px;
    }
    .test-hero > * {
        position: relative;
        z-index: 1;
    }
    .test-badge {
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
    .test-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }
    .test-meta-card {
        border-radius: 20px;
        padding: 14px 16px;
        background: rgba(255, 255, 255, .12);
        backdrop-filter: blur(8px);
    }
    .test-meta-card .value {
        font-size: 1.75rem;
        font-weight: 900;
        line-height: 1;
    }
    .test-meta-card .label {
        font-size: .78rem;
        font-weight: 700;
        opacity: .88;
        margin-top: 6px;
    }
    .test-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 18px;
        align-items: start;
    }
    .test-panel {
        background: linear-gradient(180deg, rgba(255,255,255,.96), #ffffff);
        border-radius: 24px;
        border: 1px solid #e7ecff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
        padding: 18px;
    }
    .test-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }
    .test-panel-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        font-weight: 900;
        color: #0f172a;
    }
    .test-panel-title .icon {
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
    .question-list {
        display: grid;
        gap: 14px;
    }
    .question-card {
        border-radius: 22px;
        border: 1px solid #e9edff;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
        padding: 16px;
    }
    .question-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }
    .question-index {
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
    .question-title {
        font-weight: 900;
        color: #0f172a;
        white-space: pre-line;
    }
    .question-score {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #15803d;
        font-size: .75rem;
        font-weight: 900;
        white-space: nowrap;
    }
    .option-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 12px;
    }
    .option-input {
        display: none;
    }
    .option-label {
        cursor: pointer;
        padding: 12px 14px;
        border: 1px solid #dbe4ff;
        border-radius: 16px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        transition: all .15s;
        background: #fff;
        font-weight: 700;
        color: #334155;
        min-height: 100%;
    }
    .option-label:hover {
        border-color: #60a5fa;
        background: #f8fbff;
    }
    .option-bullet {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4f46e5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .78rem;
        font-weight: 900;
        flex-shrink: 0;
    }
    .option-input:checked + .option-label {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 12px 20px rgba(37, 99, 235, .10);
    }
    .answer-textarea {
        margin-top: 12px;
        min-height: 130px;
        border-radius: 18px;
        border-color: #dbe4ff;
        padding: 14px 16px;
    }
    .answer-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
    }
    .test-sidebar {
        position: sticky;
        top: 82px;
    }
    .timer-box {
        border-radius: 20px;
        padding: 16px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        text-align: center;
    }
    #timer {
        font-size: 2rem;
        font-weight: 900;
        color: #1d4ed8;
        line-height: 1;
    }
    #timer.warning {
        color: #dc2626;
        animation: blink 1s step-start infinite;
    }
    @keyframes blink { 50% { opacity: 0; } }
    @media (max-width: 991.98px) {
        .test-layout {
            grid-template-columns: 1fr;
        }
        .test-sidebar {
            position: static;
        }
    }
    @media (max-width: 767.98px) {
        .test-meta-grid,
        .option-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<section class="test-hero slide-up">
    <div class="test-badge">
        <i class="bi bi-stars"></i>
        Khu vuc hoc tap cua ban
    </div>

    <div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div>
            <h2 class="mb-2" style="font-size:1.8rem;font-weight:900;line-height:1.15;">{{ $test->title }}</h2>
            <div style="font-size:.95rem;opacity:.92;max-width:560px;">
                Lam bai binh tinh, kiem tra lai dap an truoc khi nop bai. Dong ho se tu dong dem nguoc.
            </div>
        </div>
        <span class="s-tag tag-blue" style="background:rgba(255,255,255,.16);color:#fff;">Bai kiem tra dang mo</span>
    </div>

    <div class="test-meta-grid">
        <div class="test-meta-card">
            <div class="value">{{ $test->questions->count() }}</div>
            <div class="label">Tong so cau</div>
        </div>
        <div class="test-meta-card">
            <div class="value">{{ $test->total_score }}</div>
            <div class="label">Tong diem</div>
        </div>
        <div class="test-meta-card">
            <div class="value">{{ $testSession->effective_duration }}</div>
            <div class="label">Phut lam bai</div>
        </div>
    </div>
</section>

<form method="POST" action="/tests/{{ $testSession->id }}/submit" id="test-form">
    @csrf
    <div class="test-layout">
        <div class="test-panel slide-up" style="animation-delay:.05s;">
            <div class="test-panel-header">
                <div class="test-panel-title">
                    <span class="icon"><i class="bi bi-list-check"></i></span>
                    Cau hoi bai kiem tra
                </div>
                <span class="s-tag tag-blue">Tra loi tung cau</span>
            </div>

            <div class="question-list">
                @foreach($test->questions->sortBy('order_index') as $idx => $question)
                    <article class="question-card">
                        <div class="question-head">
                            <div>
                                <div class="question-index">
                                    <i class="bi bi-patch-question"></i>
                                    Cau {{ $idx + 1 }}
                                </div>
                                <div class="question-title">{{ $question->question_text }}</div>
                            </div>
                            <span class="question-score">
                                <i class="bi bi-award"></i>
                                {{ $question->score }} diem
                            </span>
                        </div>

                        @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                            <div class="option-grid">
                                @foreach($question->options as $optionIndex => $option)
                                    <div>
                                        <input type="radio"
                                               name="answer_{{ $question->id }}"
                                               id="opt_{{ $option->id }}"
                                               value="{{ $option->id }}"
                                               class="option-input"
                                               {{ old('answer_' . $question->id) == $option->id ? 'checked' : '' }}>
                                        <label for="opt_{{ $option->id }}" class="option-label">
                                            <span class="option-bullet">{{ chr(65 + $optionIndex) }}</span>
                                            <span>{{ $option->option_text }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <textarea name="answer_{{ $question->id }}" rows="4"
                                      class="form-control answer-textarea"
                                      placeholder="Nhap cau tra loi cua ban...">{{ old('answer_' . $question->id) }}</textarea>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>

        <aside class="test-sidebar slide-up" style="animation-delay:.1s;">
            <div class="test-panel">
                <div class="test-panel-title mb-3">
                    <span class="icon"><i class="bi bi-stopwatch"></i></span>
                    Trang thai lam bai
                </div>

                <div class="timer-box mb-3">
                    <div class="small text-muted fw-bold mb-2">Thoi gian con lai</div>
                    <div id="timer">{{ $testSession->effective_duration }}:00</div>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">So cau:</span>
                        <span class="fw-bold">{{ $test->questions->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Tong diem:</span>
                        <span class="fw-bold">{{ $test->total_score }}</span>
                    </div>
                </div>

                <button type="submit" class="btn-app btn-green w-100 justify-content-center" onclick="return confirm('Ban co chac muon nop bai khong?')">
                    <i class="bi bi-send"></i>Nop bai
                </button>

                <a href="/" class="btn-app btn-light-purple w-100 justify-content-center mt-2">
                    <i class="bi bi-arrow-left"></i>Quay lai
                </a>
            </div>
        </aside>
    </div>
</form>

@push('scripts')
<script>
    // Äá»“ng há»“ Ä‘áº¿m ngÆ°á»£c
    const totalSeconds = {{ $testSession->effective_duration }} * 60;
    let remaining = totalSeconds;
    const timerEl = document.getElementById('timer');

    function formatTime(s) {
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
    }

    const interval = setInterval(function () {
        remaining--;
        timerEl.textContent = formatTime(remaining);
        if (remaining <= 60) timerEl.classList.add('warning');
        if (remaining <= 0) {
            clearInterval(interval);
            document.getElementById('test-form').submit();
        }
    }, 1000);
</script>
@endpush
@endsection

