@extends('layouts.admin')
@section('title', $test->title)
@section('page-title', $test->title)
@section('page-actions')
    <div class="d-flex gap-2">
        @if($test->status !== 'closed')
            <a href="/admin/tests/{{ $test->id }}/edit" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-pencil-square me-1"></i>Sá»­a
            </a>
        @endif
        @if($test->status !== 'published')
            <form method="POST" action="/admin/tests/{{ $test->id }}/publish">
                @csrf
                <button class="btn btn-success btn-sm">
                    <i class="bi bi-send me-1"></i>Xuáº¥t báº£n
                </button>
            </form>
        @endif
        <a href="{{ route('admin.test-sessions.create', ['test_id' => $test->id]) }}" class="btn btn-sm btn-success"><i class="bi bi-calendar-plus me-1"></i>Tạo phiên</a>
        <a href="/admin/tests/{{ $test->id }}/submissions" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-journal-text me-1"></i>Xem bÃ i ná»™p ({{ $test->submissions->count() }})
        </a>
        <a href="/admin/tests" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Trá»Ÿ vá»</a>
    </div>
@endsection

@section('content')
<div class="row g-4">
    {{-- Info Card --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">ThÃ´ng tin</div>
            <div class="card-body">
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Lá»›p</dt>
                    <dd class="col-7">{{ $test->schoolClass->name ?? 'â€”' }}</dd>
                    <dt class="col-5 text-muted">Tráº¡ng thÃ¡i</dt>
                    <dd class="col-7">
                        @if($test->status === 'published')
                            <span class="badge bg-success">ÄÃ£ xuáº¥t báº£n</span>
                        @elseif($test->status === 'closed')
                            <span class="badge bg-secondary">ÄÃ£ Ä‘Ã³ng</span>
                        @else
                            <span class="badge bg-warning text-dark">NhÃ¡p</span>
                        @endif
                    </dd>
                    <dt class="col-5 text-muted">Thá»i gian</dt>
                    <dd class="col-7">{{ $test->duration }} phÃºt</dd>
                    <dt class="col-5 text-muted">Äiá»ƒm tá»•ng</dt>
                    <dd class="col-7">{{ $test->total_score }}</dd>
                    <dt class="col-5 text-muted">Báº¯t Ä‘áº§u</dt>
                    <dd class="col-7">{{ $test->starts_at ? \Carbon\Carbon::parse($test->starts_at)->format('d/m/Y H:i') : 'â€”' }}</dd>
                    <dt class="col-5 text-muted">Káº¿t thÃºc</dt>
                    <dd class="col-7">{{ $test->ends_at ? \Carbon\Carbon::parse($test->ends_at)->format('d/m/Y H:i') : 'â€”' }}</dd>
                    <dt class="col-5 text-muted">MÃ´ táº£</dt>
                    <dd class="col-7">{{ $test->description ?: 'â€”' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Questions + Add Form --}}
    <div class="col-lg-8">
        {{-- Question List --}}
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Danh sÃ¡ch cÃ¢u há»i ({{ $test->questions->count() }})</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse($test->questions as $i => $question)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="flex-grow-1">
                                <span class="badge bg-secondary me-2">#{{ $i + 1 }}</span>
                                <span class="badge {{ ['multiple_choice'=>'bg-primary','true_false'=>'bg-info text-dark','essay'=>'bg-light text-dark border'][$question->question_type] ?? 'bg-secondary' }} me-2 small">
                                    {{ ['multiple_choice'=>'Tráº¯c nghiá»‡m','true_false'=>'ÄÃºng/Sai','essay'=>'Tá»± luáº­n'][$question->question_type] ?? $question->question_type }}
                                </span>
                                <span class="fw-semibold">{{ $question->question_text }}</span>
                                <span class="text-muted ms-2 small">({{ $question->score }} Ä‘iá»ƒm)</span>
                                @if($question->question_type !== 'essay')
                                    <div class="mt-1 ms-4">
                                        @foreach($question->options as $opt)
                                            <span class="me-3 small {{ $opt->is_correct ? 'text-success fw-bold' : 'text-muted' }}">
                                                {{ $opt->is_correct ? 'âœ”' : 'â—‹' }} {{ $opt->option_text }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <form method="POST" action="/admin/questions/{{ $question->id }}/delete"
                                  onsubmit="return confirm('XoÃ¡ cÃ¢u há»i nÃ y?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-3">ChÆ°a cÃ³ cÃ¢u há»i nÃ o.</div>
                @endforelse
            </div>
        </div>

        {{-- Add Question Form --}}
        @if($test->status !== 'closed')
        <div class="card">
            <div class="card-header bg-white fw-semibold">ThÃªm cÃ¢u há»i má»›i</div>
            <div class="card-body p-4">
                <form method="POST" action="/admin/tests/{{ $test->id }}/questions" id="addQuestionForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Loáº¡i cÃ¢u há»i</label>
                        <select name="question_type" id="questionType" class="form-select" onchange="toggleOptions()">
                            <option value="multiple_choice">Tráº¯c nghiá»‡m (4 lá»±a chá»n)</option>
                            <option value="true_false">ÄÃºng / Sai</option>
                            <option value="essay">Tá»± luáº­n</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">CÃ¢u há»i <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Äiá»ƒm</label>
                            <input type="number" name="score" min="0" step="0.5" class="form-control" value="10">
                        </div>
                    </div>

                    {{-- MC Options --}}
                    <div id="mcOptions">
                        <label class="form-label fw-semibold">CÃ¡c lá»±a chá»n <small class="text-muted">(tick Ä‘Ã¡p Ã¡n Ä‘Ãºng)</small></label>
                        @for($i = 0; $i < 4; $i++)
                            <div class="input-group mb-2">
                                <div class="input-group-text">
                                    <input type="radio" name="correct_option" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}>
                                </div>
                                <input type="text" name="options[]" class="form-control" placeholder="Lá»±a chá»n {{ $i + 1 }}" required>
                            </div>
                        @endfor
                    </div>

                    {{-- True/False --}}
                    <div id="tfOptions" class="d-none">
                        <label class="form-label fw-semibold">ÄÃ¡p Ã¡n Ä‘Ãºng</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_option" value="0" id="tfTrue">
                                <label class="form-check-label" for="tfTrue">ÄÃºng (True)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_option" value="1" id="tfFalse">
                                <label class="form-check-label" for="tfFalse">Sai (False)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>ThÃªm cÃ¢u há»i</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleOptions() {
    const type = document.getElementById('questionType').value;
    const mcEl = document.getElementById('mcOptions');
    const tfEl = document.getElementById('tfOptions');
    const mcInputs = mcEl.querySelectorAll('input[name="options[]"]');

    if (type === 'multiple_choice') {
        mcEl.classList.remove('d-none');
        tfEl.classList.add('d-none');
        mcInputs.forEach(i => i.required = true);
    } else if (type === 'true_false') {
        mcEl.classList.add('d-none');
        tfEl.classList.remove('d-none');
        mcInputs.forEach(i => i.required = false);
    } else {
        mcEl.classList.add('d-none');
        tfEl.classList.add('d-none');
        mcInputs.forEach(i => i.required = false);
    }
}
</script>
@endpush
@endsection

