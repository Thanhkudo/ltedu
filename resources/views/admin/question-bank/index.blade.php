@extends('layouts.admin')
@section('title', 'Kho câu hỏi')
@section('page-title', 'Kho câu hỏi')
@section('page-actions')
    <a href="{{ route('admin.question-categories.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-tags me-1"></i>Danh mục
    </a>
    <a href="{{ route('admin.question-bank.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm câu hỏi
    </a>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm nội dung..." value="{{ request('keyword') }}">
            </div>
            <div class="col-md-2">
                <select name="grade_level" class="form-select">
                    <option value="">Trình độ</option>
                    @for($g=6; $g<=9; $g++)
                        <option value="{{ $g }}" {{ (string) request('grade_level') === (string) $g ? 'selected' : '' }}>Lớp {{ $g }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="skill_type" class="form-select">
                    <option value="">Loại</option>
                    @foreach(['listening'=>'Nghe','speaking'=>'Nói','reading'=>'Đọc','writing'=>'Viết','grammar'=>'Ngữ pháp','vocabulary'=>'Từ vựng'] as $k => $lbl)
                        <option value="{{ $k }}" {{ request('skill_type') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>
                            L{{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="answer_mode" class="form-select">
                    <option value="">Kiểu trả lời</option>
                    <option value="select" {{ request('answer_mode') === 'select' ? 'selected' : '' }}>Chọn đáp án</option>
                    <option value="input" {{ request('answer_mode') === 'input' ? 'selected' : '' }}>Nhập đáp án</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="context_type" class="form-select">
                    <option value="">Ngữ cảnh</option>
                    <option value="normal" {{ request('context_type') === 'normal' ? 'selected' : '' }}>Thường</option>
                    <option value="reading" {{ request('context_type') === 'reading' ? 'selected' : '' }}>Đọc hiểu</option>
                    <option value="listening" {{ request('context_type') === 'listening' ? 'selected' : '' }}>Nghe</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Lọc</button>
                <a href="{{ route('admin.question-bank.index') }}" class="btn btn-outline-secondary btn-sm">Xóa</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 35%">Câu hỏi</th>
                    <th>Danh mục</th>
                    <th>Kiểu</th>
                    <th>Ngữ cảnh</th>
                    <th>Độ khó</th>
                    <th style="width: 130px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $question)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($question->question_text, 120) }}</div>
                            @if($question->answer_mode === 'select')
                                <small class="text-muted">{{ $question->options->count() }} lựa chọn</small>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $question->category->name ?? '—' }}</div>
                            <div class="small text-muted">L{{ $question->category->grade_level ?? '?' }} - {{ ucfirst($question->category->skill_type ?? '') }}</div>
                        </td>
                        <td>{{ $question->answer_mode === 'select' ? 'Chọn đáp án' : 'Nhập đáp án' }}</td>
                        <td>
                            @if($question->context_type === 'reading') Đọc hiểu
                            @elseif($question->context_type === 'listening') Nghe
                            @else Bình thường
                            @endif
                        </td>
                        <td>{{ ucfirst($question->difficulty) }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.question-bank.edit', $question->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                            <form method="POST" action="{{ route('admin.question-bank.destroy', $question->id) }}" class="d-inline" onsubmit="return confirm('Xóa câu hỏi này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có câu hỏi trong kho.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($questions->hasPages())
        <div class="card-footer bg-white">{{ $questions->links() }}</div>
    @endif
</div>
@endsection
