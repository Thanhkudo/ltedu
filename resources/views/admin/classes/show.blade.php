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
<div class="row g-4 class-detail-page">
    {{-- Cot trai: Buổi học --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white class-card-header">
                <span class="fw-semibold"><i class="bi bi-calendar-week me-2 text-primary"></i>Buổi học</span>
                <a href="/admin/classes/{{ $class->id }}/sessions/create" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg me-1"></i>Thêm buổi học
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($class->sessions as $session)
                    <div class="session-card session-card-{{ $session->status }} border-bottom p-3">
                        <div class="session-head">
                            <div class="session-info">
                                <div class="session-title-row">
                                    <span class="badge bg-secondary">Buổi {{ $session->session_number }}</span>
                                    <strong>{{ $session->title }}</strong>
                                </div>
                                <div class="session-meta">
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i>{{ $session->session_date->format('d/m/Y H:i') }}</span>
                                    @if($session->completed_at)
                                        <span class="text-success small"><i class="bi bi-check2-circle me-1"></i>Hoàn thành: {{ $session->completed_at->format('d/m/Y H:i') }}</span>
                                    @endif
                                    @if($session->cancelled_at)
                                        <span class="text-danger small"><i class="bi bi-x-circle me-1"></i>Đã hủy: {{ $session->cancelled_at->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                                <span class="badge
                                    {{ $session->status === 'completed' ? 'bg-success' : ($session->status === 'cancelled' ? 'bg-danger' : 'bg-primary') }}">
                                    {{ ['scheduled'=>'Sắp học','completed'=>'Đã học','cancelled'=>'Đã hủy'][$session->status] ?? '' }}
                                </span>
                            </div>
                            <div class="session-actions">
                                @if($session->status === 'scheduled')
                                    <form method="POST" action="/admin/sessions/{{ $session->id }}/complete"
                                          onsubmit="return confirm('Đánh dấu buổi học này là đã học? Học viên vẫn có thể làm bài tập nếu bài còn mở.')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success" title="Đánh dấu đã học">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($session->status !== 'cancelled')
                                    <a href="/admin/assignments/create?session_id={{ $session->id }}"
                                                  class="btn btn-sm btn-outline-info" title="Cấu hình bài tập từ kho câu hỏi">
                                        <i class="bi bi-clipboard2-plus"></i>
                                    </a>
                                @endif
                                @if($session->status !== 'scheduled')
                                    <form method="POST" action="/admin/sessions/{{ $session->id }}/reopen"
                                          onsubmit="return confirm('Mở lại buổi học này về trạng thái sắp học?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-primary" title="Mở lại buổi học">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($session->status !== 'cancelled')
                                    <form method="POST" action="/admin/sessions/{{ $session->id }}/cancel"
                                          onsubmit="return confirm('Hủy buổi học này? Bài tập đã giao sẽ không bị xóa nhưng học viên sẽ thấy buổi này là đã hủy.')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-warning" title="Hủy buổi học">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="/admin/sessions/{{ $session->id }}"
                                      onsubmit="return confirm('Xóa buổi học này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        {{-- Bài tập trong buổi --}}
                        @if($session->assignments->isNotEmpty())
                            <div class="assignment-list mt-2 ps-3">
                                @foreach($session->assignments as $assignment)
                                    <div class="assignment-row border-top">
                                        <div class="assignment-main">
                                            <i class="bi bi-file-earmark-text text-muted"></i>
                                            <div class="assignment-text">
                                                <div class="small fw-semibold">{{ $assignment->exercise->title }}</div>
                                                <div class="assignment-badges">
                                                    <span class="badge bg-light text-dark small">{{ $assignment->exercise->type }}</span>
                                                    @if($assignment->isGeneratedFromQuestionBank())
                                                        <span class="badge bg-info text-dark small">Kho câu hỏi{{ $assignment->generated_question_count ? ' - ' . $assignment->generated_question_count . ' câu' : '' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="assignment-meta-actions">
                                            <span class="text-muted small">
                                                Hạn: {{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : 'Không giới hạn' }}
                                            </span>
                                            <div class="assignment-actions">
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
                                        </div>
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
                    <div class="card-header bg-white class-card-header">
                        <div class="fw-semibold"><i class="bi bi-people me-2 text-success"></i>Học viên ({{ $class->activeStudents->count() }})</div>
                        <span class="badge bg-light text-dark">Tổng: {{ $class->activeStudents->count() }}</span>
                    </div>
                    <div class="list-group list-group-flush" style="max-height:300px;overflow-y:auto">
                        @forelse($class->activeStudents as $student)
                            <div class="list-group-item student-row py-2">
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
                    <div class="card-header bg-white class-card-header">
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
                                    <label class="list-group-item list-group-item-action enroll-student-item">
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
        .class-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .session-head,
        .assignment-row,
        .student-row,
        .enroll-student-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .session-head {
            align-items: flex-start;
        }

        .session-info {
            min-width: 0;
            display: grid;
            gap: 6px;
        }

        .session-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 12px;
        }

        .session-card-cancelled {
            background: #f8fafc;
            opacity: .78;
        }

        .session-card-cancelled .assignment-list {
            filter: grayscale(.18);
        }

        .session-title-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            min-width: 0;
        }

        .session-title-row strong,
        .assignment-text {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .session-actions,
        .assignment-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .assignment-row {
            padding: 8px 0;
        }

        .assignment-main {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            min-width: 0;
        }

        .assignment-badges {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 4px;
        }

        .assignment-meta-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .student-row,
        .enroll-student-item {
            min-width: 0;
        }

        .enroll-student-item {
            cursor: pointer;
        }
        .enroll-student-item .student-checkbox {
            pointer-events: auto;
        }
        .enroll-student-item input[type="checkbox"] {
            margin-left: .75rem;
        }

        @media (max-width: 767.98px) {
            .class-detail-page {
                --bs-gutter-y: 14px;
            }

            .class-card-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .class-card-header .btn,
            .class-card-header .badge {
                align-self: flex-start;
            }

            .session-card {
                padding: 12px !important;
                background: #fff;
            }

            .session-head,
            .assignment-row,
            .student-row,
            .enroll-student-item {
                align-items: stretch;
                flex-direction: column;
            }

            .session-head {
                gap: 10px;
            }

            .session-info {
                width: 100%;
            }

            .session-title-row {
                align-items: flex-start;
            }

            .session-actions {
                width: auto;
                max-width: 100%;
                display: inline-flex;
                align-self: flex-start;
                flex-wrap: wrap;
                gap: 6px;
                padding: 6px;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                background: #f8fafc;
            }

            .session-actions form,
            .session-actions a {
                width: auto;
                margin: 0;
            }

            .session-actions button {
                width: 38px;
                height: 38px;
                min-height: 38px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
            }

            .session-actions a.btn {
                width: 38px;
                height: 38px;
                min-height: 38px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
            }

            .assignment-list {
                padding-left: 0 !important;
                display: grid;
                gap: 8px;
            }

            .assignment-row {
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px;
                padding: 10px;
                background: #f8fafc;
            }

            .assignment-meta-actions {
                width: 100%;
                align-items: stretch;
                flex-direction: column;
                gap: 8px;
            }

            .assignment-actions {
                display: grid;
                grid-template-columns: minmax(0, 1fr) 44px;
                width: 100%;
            }

            .assignment-actions .btn,
            .assignment-actions button {
                width: 100%;
                min-height: 36px;
            }

            .student-row form,
            .student-row button {
                width: 100%;
            }

            .enroll-student-list {
                max-height: 420px !important;
            }

            .enroll-student-item {
                align-items: flex-start;
            }

            .enroll-student-item .student-checkbox {
                margin-left: 0 !important;
                margin-top: 4px;
                width: 20px;
                height: 20px;
            }
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
