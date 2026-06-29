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
    {{-- Cot trai: Buổi học --}}
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
                                    {{ ['scheduled'=>'Sắp học','completed'=>'Đã học','cancelled'=>'Đã hủy'][$session->status] ?? '' }}
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
                                      onsubmit="return confirm('Xóa buổi học này?')">
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
                                            Hạn: {{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : 'Không giới hạn' }}
                                        </span>
                                        <a href="/admin/assignments/{{ $assignment->id }}/submissions"
                                           class="btn btn-xs btn-sm btn-outline-secondary py-0 px-2">
                                            Bài nộp
                                        </a>
                                        <form method="POST" action="/admin/assignments/{{ $assignment->id }}" class="m-0"
                                              onsubmit="return confirm('Xóa bài tập này?')">
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

    {{-- Cot phai: Học viên --}}
    <div class="col-lg-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div class="fw-semibold"><i class="bi bi-people me-2 text-success"></i>Học viên ({{ $class->activeStudents->count() }})</div>
                        <span class="badge bg-light text-dark">Tổng: {{ $class->activeStudents->count() }}</span>
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
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div class="fw-semibold"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Thêm học viên vào lớp</div>
                        <span id="selectedCount" class="badge bg-secondary">0 đã chọn</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="studentSearch" class="form-label small fw-semibold">Tìm kiếm học viên</label>
                            <input id="studentSearch" type="search" class="form-control form-control-sm" placeholder="Nhập tên hoặc mã học viên">
                        </div>
                        <form method="POST" action="/admin/classes/{{ $class->id }}/enroll" id="enrollForm">
                            @csrf
                            <div class="list-group enroll-student-list" id="studentList" style="max-height:300px;overflow-y:auto">
                                @foreach($students as $student)
                                    <label class="list-group-item list-group-item-action d-flex justify-content-between align-items-center enroll-student-item">
                                        <div>
                                            <div class="fw-semibold">{{ $student->full_name }}</div>
                                            <div class="text-muted small">{{ $student->student_code }}</div>
                                        </div>
                                        <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $student->id }}">
                                    </label>
                                @endforeach
                            </div>
                            <div class="text-center text-muted small mt-2 d-none" id="noStudentResult">Không tìm thấy học viên.</div>
                            <div class="mt-3 d-grid">
                                <button type="submit" class="btn btn-success btn-sm" id="enrollSubmit" disabled>
                                    <i class="bi bi-person-plus me-1"></i>Thêm vào lớp
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .enroll-student-item {
            cursor: pointer;
        }
        .enroll-student-item .student-checkbox {
            pointer-events: auto;
        }
        .enroll-student-item input[type="checkbox"] {
            margin-left: .75rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const studentSearch = document.getElementById('studentSearch');
            const studentList = document.getElementById('studentList');
            const studentCheckboxes = Array.from(document.querySelectorAll('.student-checkbox'));
            const selectedCount = document.getElementById('selectedCount');
            const enrollSubmit = document.getElementById('enrollSubmit');
            const noStudentResult = document.getElementById('noStudentResult');

            function updateSelectedCount() {
                const selected = studentCheckboxes.filter(cb => cb.checked).length;
                selectedCount.textContent = selected + ' đã chọn';
                enrollSubmit.disabled = selected === 0;
            }

            function filterStudents() {
                const query = studentSearch.value.trim().toLowerCase();
                let visible = 0;
                studentCheckboxes.forEach((checkbox) => {
                    const item = checkbox.closest('.enroll-student-item');
                    if (!item) return;

                    const text = item.textContent.toLowerCase();
                    const match = !query || text.includes(query);
                    item.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                noStudentResult.classList.toggle('d-none', visible > 0);
            }

            studentSearch.addEventListener('input', filterStudents);
            studentCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateSelectedCount);
                const item = checkbox.closest('.enroll-student-item');
                if (item) {
                    item.addEventListener('click', function (event) {
                        if (event.target === checkbox) return;
                        checkbox.checked = !checkbox.checked;
                        updateSelectedCount();
                    });
                }
            });

            updateSelectedCount();
        });
    </script>
@endpush

