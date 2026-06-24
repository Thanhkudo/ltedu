@extends('layouts.admin')
@section('title', $class->name)
@section('page-title', $class->name)
@section('page-actions')
    <a href="/admin/classes/{{ $class->id }}/edit" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-pencil me-1"></i>Sửa
    </a>
    <a href="/admin/classes" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
@endsection

@section('content')
<div class="row g-4">
    {{-- Cột trái: Buổi học --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-calendar-week me-2 text-primary"></i>Buổi học</span>
                <a href="/admin/classes/{{ $class->id }}/sessions/create" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg me-1"></i>Thêm buổi học
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($class->sessions as $session)
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-secondary me-2">Buổi {{ $session->session_number }}</span>
                                <strong>{{ $session->title }}</strong>
                                <span class="text-muted ms-2 small">{{ $session->session_date->format('d/m/Y H:i') }}</span>
                                <span class="badge ms-1
                                    {{ $session->status === 'completed' ? 'bg-success' : ($session->status === 'cancelled' ? 'bg-danger' : 'bg-primary') }}">
                                    {{ ['scheduled'=>'Sắp học','completed'=>'Đã học','cancelled'=>'Đã huỷ'][$session->status] ?? '' }}
                                </span>
                            </div>
                            <div class="d-flex gap-1">
                                @if($session->status === 'scheduled')
                                    <form method="POST" action="/admin/sessions/{{ $session->id }}/complete">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success" title="Đánh dấu đã học">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="/admin/assignments/create?session_id={{ $session->id }}"
                                              class="btn btn-sm btn-outline-info" title="Cấu hình bài tập từ kho câu hỏi">
                                    <i class="bi bi-clipboard2-plus"></i>
                                </a>
                                <form method="POST" action="/admin/sessions/{{ $session->id }}"
                                      onsubmit="return confirm('Xoá buổi học này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        {{-- Bài tập trong buổi --}}
                        @if($session->assignments->isNotEmpty())
                            <div class="mt-2 ps-3">
                                @foreach($session->assignments as $assignment)
                                    <div class="d-flex align-items-center gap-2 py-1 border-top">
                                        <i class="bi bi-file-earmark-text text-muted"></i>
                                        <span class="small fw-semibold">{{ $assignment->exercise->title }}</span>
                                        <span class="badge bg-light text-dark small">{{ $assignment->exercise->type }}</span>
                                        @if($assignment->isGeneratedFromQuestionBank())
                                            <span class="badge bg-info text-dark small">Kho câu hỏi{{ $assignment->generated_question_count ? ' - ' . $assignment->generated_question_count . ' câu' : '' }}</span>
                                        @endif
                                        <span class="text-muted small ms-auto">
                                            Hạn: {{ $assignment->due_date->format('d/m/Y') }}
                                        </span>
                                        <a href="/admin/assignments/{{ $assignment->id }}/submissions"
                                           class="btn btn-xs btn-sm btn-outline-secondary py-0 px-2">
                                            Bài nộp
                                        </a>
                                        <form method="POST" action="/admin/assignments/{{ $assignment->id }}" class="m-0"
                                              onsubmit="return confirm('Xoá bài tập này?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger py-0 px-2">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">Chưa có buổi học nào.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Cột phải: Học viên --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-people me-2 text-success"></i>Học viên ({{ $class->activeStudents->count() }})
            </div>
            <div class="list-group list-group-flush" style="max-height:300px;overflow-y:auto">
                @forelse($class->activeStudents as $student)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <div class="fw-semibold small">{{ $student->full_name }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ $student->student_code }}</div>
                        </div>
                        <form method="POST" action="/admin/classes/{{ $class->id }}/drop/{{ $student->id }}"
                              onsubmit="return confirm('Cho học viên rời lớp?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Rời lớp">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="list-group-item text-muted text-center small">Chưa có học viên.</div>
                @endforelse
            </div>
            <div class="card-footer bg-white">
                <form method="POST" action="/admin/classes/{{ $class->id }}/enroll"
                      class="d-flex flex-column gap-2">
                    @csrf
                    <label class="form-label small fw-semibold mb-1">Thêm học viên vào lớp</label>
                    <select name="student_ids[]" class="form-select form-select-sm" multiple size="4">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-person-plus me-1"></i>Thêm vào lớp
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
