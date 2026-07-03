@extends('layouts.admin')
@section('title', 'Import kho câu hỏi')
@section('page-title', 'Import kho câu hỏi')
@section('page-actions')
    <a href="{{ route('admin.question-bank.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Trở về kho câu hỏi
    </a>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-info-circle me-2 text-primary"></i>Hướng dẫn định dạng file
            </div>
            <div class="card-body">
                <p class="text-muted">
                    File import dùng định dạng Excel <code>.xlsx</code>. Hệ thống cũng hỗ trợ <code>.xls</code> và <code>.csv</code>.
                    Dòng đầu tiên là tên cột tiếng Việt trong file mẫu, mỗi dòng tiếp theo là một câu hỏi.
                </p>

                <div class="alert alert-primary">
                    <div class="fw-semibold mb-1"><i class="bi bi-collection me-1"></i>Quy tắc nhóm câu hỏi đọc/nghe</div>
                    <div class="small">
                        Với câu hỏi đọc hiểu hoặc nghe, nhập cùng <strong>Mã nhóm</strong> cho các câu thuộc cùng một bài.
                        Dòng đầu của nhóm nên nhập <strong>Tiêu đề nhóm</strong> và <strong>Đoạn văn</strong> hoặc <strong>File audio</strong>.
                        Các dòng sau chỉ cần giữ cùng Mã nhóm, có thể để trống đoạn văn/audio.
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cột</th>
                                <th>Bắt buộc</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><strong>Mã nhóm</strong><div class="small text-muted">group_key</div></td><td>Với đọc/nghe</td><td>Mã dùng để gom nhiều câu vào cùng một bài đọc hoặc bài nghe. Ví dụ: <code>READING_UNIT_1</code>.</td></tr>
                            <tr><td><strong>Tiêu đề nhóm</strong><div class="small text-muted">group_title</div></td><td>Không</td><td>Tên bài đọc/bài nghe. Nếu trống, hệ thống dùng Mã nhóm.</td></tr>
                            <tr><td><strong>ID danh mục</strong><div class="small text-muted">category_id</div></td><td>Có</td><td>ID danh mục trong bảng bên phải.</td></tr>
                            <tr><td><strong>Tiêu đề</strong><div class="small text-muted">title</div></td><td>Không</td><td>Tiêu đề ngắn của câu hỏi.</td></tr>
                            <tr><td><strong>Nội dung câu hỏi</strong><div class="small text-muted">question_text</div></td><td>Có</td><td>Nội dung câu hỏi hiển thị cho học viên.</td></tr>
                            <tr><td><strong>Kiểu câu hỏi</strong><div class="small text-muted">question_type</div></td><td>Có</td><td><code>select</code>, <code>input</code>, <code>matching</code>, <code>ordering</code>.</td></tr>
                            <tr><td><strong>Ngữ cảnh</strong><div class="small text-muted">context_type</div></td><td>Có</td><td><code>normal</code>, <code>reading</code>, <code>listening</code>.</td></tr>
                            <tr><td><strong>Đáp án đúng</strong><div class="small text-muted">correct_answer</div></td><td>Tùy loại</td><td>Bắt buộc với câu nhập đáp án.</td></tr>
                            <tr><td><strong>Danh sách đáp án</strong><div class="small text-muted">options</div></td><td>Tùy loại</td><td>Dùng cho chọn đáp án, ngăn cách bằng <code>|</code>. Ví dụ: <code>am|is|are|be</code>.</td></tr>
                            <tr><td><strong>Vị trí đáp án đúng</strong><div class="small text-muted">correct_option</div></td><td>Tùy loại</td><td>Vị trí đáp án đúng, bắt đầu từ <code>0</code>.</td></tr>
                            <tr><td><strong>Thứ tự đáp án</strong><div class="small text-muted">ordering_items</div></td><td>Tùy loại</td><td>Dùng cho sắp xếp, nhập đúng thứ tự chuẩn và ngăn cách bằng <code>|</code>.</td></tr>
                            <tr><td><strong>Cặp nối đáp án</strong><div class="small text-muted">matching_pairs</div></td><td>Tùy loại</td><td>Dạng <code>Vế trái=&gt;Vế phải</code>, nhiều cặp ngăn cách bằng <code>|</code>. Nếu vế phải là ảnh dùng <code>image:/data/images/file.png</code>.</td></tr>
                            <tr><td><strong>Đoạn văn</strong><div class="small text-muted">passage</div></td><td>Với nhóm đọc</td><td>Nhập ở dòng đầu của nhóm <code>reading</code>.</td></tr>
                            <tr><td><strong>File audio</strong><div class="small text-muted">audio_url</div></td><td>Với nhóm nghe</td><td>Đường dẫn audio từ CKFinder, nhập ở dòng đầu của nhóm <code>listening</code>.</td></tr>
                            <tr><td><strong>Giải thích</strong><div class="small text-muted">explanation</div></td><td>Không</td><td>Giải thích đáp án nếu cần.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3 mb-3">
                    <div class="fw-semibold mb-2"><i class="bi bi-lightbulb me-1"></i>Ghi chú quan trọng</div>
                    <ul class="mb-0 ps-3">
                        <li>Không đổi tên các cột tiếng Việt trong dòng đầu của file mẫu.</li>
                        <li>Các danh sách nhiều giá trị dùng dấu <code>|</code> để ngăn cách.</li>
                        <li>File ảnh/audio không nhúng trực tiếp trong Excel. Upload lên CKFinder trước, sau đó copy đường dẫn vào file import.</li>
                        <li>Với đọc/nghe, các dòng cùng <code>Mã nhóm</code>, cùng danh mục và cùng ngữ cảnh sẽ được gom vào cùng một nhóm câu hỏi.</li>
                        <li>Nếu nhóm đã tồn tại, hệ thống dùng lại nhóm đó thay vì tạo trùng.</li>
                    </ul>
                </div>

                <div class="accordion mb-3" id="importExamples">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#exampleGroup">
                                Ví dụ nhóm bài đọc có nhiều câu
                            </button>
                        </h2>
                        <div id="exampleGroup" class="accordion-collapse collapse show" data-bs-parent="#importExamples">
                            <div class="accordion-body small">
                                <div>Dòng 1: <code>Mã nhóm = READING_UNIT_1</code>, <code>Ngữ cảnh = reading</code>, nhập <code>Đoạn văn</code>.</div>
                                <div>Dòng 2: giữ <code>Mã nhóm = READING_UNIT_1</code>, để trống <code>Đoạn văn</code>. Câu này vẫn dùng chung bài đọc của dòng 1.</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#exampleMatching">
                                Ví dụ nối chữ với ảnh
                            </button>
                        </h2>
                        <div id="exampleMatching" class="accordion-collapse collapse" data-bs-parent="#importExamples">
                            <div class="accordion-body small">
                                <div><code>matching_pairs</code>:</div>
                                <div><code>Cat=&gt;image:/data/images/cat.png|Dog=&gt;image:/data/images/dog.png</code></div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.question-bank.import-template') }}" class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i>Tải file mẫu Excel
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-upload me-2 text-success"></i>Import file
            </div>
            <div class="card-body">
                @error('import_file')
                    <div class="alert alert-danger" style="white-space: pre-line;">{{ $message }}</div>
                @enderror

                <form method="POST" action="{{ route('admin.question-bank.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn file Excel <span class="text-danger">*</span></label>
                        <input type="file" name="import_file" class="form-control @error('import_file') is-invalid @enderror" accept=".xlsx,.xls,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain" required>
                        <div class="form-text">Dung lượng tối đa 4MB. Hệ thống kiểm tra toàn bộ file trước khi ghi dữ liệu.</div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload me-1"></i>Import câu hỏi
                        </button>
                        <a href="{{ route('admin.question-bank.index') }}" class="btn btn-outline-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Danh mục hiện có</div>
            <div class="list-group list-group-flush" style="max-height: 520px; overflow-y:auto;">
                @forelse($categories as $category)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between gap-2">
                            <div>
                                <div class="fw-semibold">{{ $category->name }}</div>
                                <div class="small text-muted">Lớp {{ $category->grade_level }} - {{ ucfirst($category->skill_type) }}</div>
                            </div>
                            <span class="badge bg-light text-dark border">ID {{ $category->id }}</span>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-muted text-center">
                        Chưa có danh mục. Vui lòng tạo danh mục trước khi import.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
