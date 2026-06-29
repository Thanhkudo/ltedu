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
                    File import dùng định dạng Excel <code>.xlsx</code>. Hệ thống cũng hỗ trợ đọc <code>.xls</code> và <code>.csv</code>.
                    Dòng đầu tiên là tên cột, mỗi dòng tiếp theo là một câu hỏi. Nên tải file mẫu rồi nhập dữ liệu theo đúng cấu trúc.
                </p>

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
                            <tr><td><strong>ID danh mục</strong><div class="small text-muted">category_id</div></td><td>Có</td><td>ID danh mục trong bảng bên phải.</td></tr>
                            <tr><td><strong>Tiêu đề</strong><div class="small text-muted">title</div></td><td>Không</td><td>Tiêu đề ngắn của câu hỏi.</td></tr>
                            <tr><td><strong>Nội dung câu hỏi</strong><div class="small text-muted">question_text</div></td><td>Có</td><td>Nội dung câu hỏi.</td></tr>
                            <tr><td><strong>Kiểu câu hỏi</strong><div class="small text-muted">question_type</div></td><td>Có</td><td><code>select</code>, <code>input</code>, <code>matching</code>, <code>ordering</code>.</td></tr>
                            <tr><td><strong>Ngữ cảnh</strong><div class="small text-muted">context_type</div></td><td>Có</td><td><code>normal</code>, <code>reading</code>, <code>listening</code>.</td></tr>
                            <tr><td><strong>Đáp án đúng</strong><div class="small text-muted">correct_answer</div></td><td>Tùy loại</td><td>Bắt buộc với câu nhập đáp án.</td></tr>
                            <tr><td><strong>Danh sách đáp án</strong><div class="small text-muted">options</div></td><td>Tùy loại</td><td>Dùng cho chọn đáp án, ngăn cách bằng dấu <code>|</code>. Ví dụ: <code>am|is|are|be</code>.</td></tr>
                            <tr><td><strong>Vị trí đáp án đúng</strong><div class="small text-muted">correct_option</div></td><td>Tùy loại</td><td>Vị trí đáp án đúng, bắt đầu từ <code>0</code>. Ví dụ đáp án đầu tiên là <code>0</code>.</td></tr>
                            <tr><td><strong>Thứ tự đáp án</strong><div class="small text-muted">ordering_items</div></td><td>Tùy loại</td><td>Dùng cho sắp xếp, ghi đúng thứ tự và ngăn cách bằng <code>|</code>.</td></tr>
                            <tr><td><strong>Cặp nối đáp án</strong><div class="small text-muted">matching_pairs</div></td><td>Tùy loại</td><td>Dùng cho nối đáp án. Ví dụ: <code>Cat=&gt;Con mèo|Dog=&gt;Con chó</code>. Nếu nối ảnh dùng <code>Cat=&gt;image:/data/images/cat.png</code>.</td></tr>
                            <tr><td><strong>Đoạn văn</strong><div class="small text-muted">passage</div></td><td>Không</td><td>Nội dung đoạn văn khi <code>context_type</code> là <code>reading</code>.</td></tr>
                            <tr><td><strong>File audio</strong><div class="small text-muted">audio_url</div></td><td>Không</td><td>Đường dẫn audio từ CKFinder khi <code>context_type</code> là <code>listening</code>.</td></tr>
                            <tr><td><strong>Giải thích</strong><div class="small text-muted">explanation</div></td><td>Không</td><td>Giải thích đáp án.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3 mb-3">
                    <div class="fw-semibold mb-2"><i class="bi bi-lightbulb me-1"></i>Ghi chú quan trọng</div>
                    <ul class="mb-0 ps-3">
                        <li>Mỗi dòng trong file Excel tương ứng với một câu hỏi.</li>
                        <li>Không đổi tên các cột tiếng Việt trong dòng đầu tiên của file mẫu.</li>
                        <li>Các danh sách nhiều giá trị dùng dấu <code>|</code> để ngăn cách.</li>
                        <li>File ảnh và audio không nhúng trực tiếp trong Excel. Cần upload lên CKFinder trước, sau đó copy đường dẫn vào file import.</li>
                        <li>Nếu một ô có dấu phẩy, xuống dòng hoặc ký tự đặc biệt, nên giữ nguyên định dạng Excel <code>.xlsx</code> thay vì lưu sang CSV.</li>
                    </ul>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-light-subtle">
                            <div class="fw-semibold mb-2"><i class="bi bi-volume-up me-1 text-primary"></i>Câu hỏi có audio</div>
                            <div class="small text-muted mb-2">
                                Dùng khi câu hỏi là dạng nghe. Upload file audio lên CKFinder trước rồi điền đường dẫn vào cột <code>audio_url</code>.
                            </div>
                            <div class="small">
                                <div><code>context_type</code>: <code>listening</code></div>
                                <div><code>audio_url</code>: <code>/data/audios/listening-lesson-1.mp3</code></div>
                            </div>
                            <div class="small text-muted mt-2">
                                Nếu <code>context_type</code> không phải <code>listening</code>, hệ thống sẽ bỏ qua cột <code>audio_url</code>.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-light-subtle">
                            <div class="fw-semibold mb-2"><i class="bi bi-image me-1 text-success"></i>Câu nối đáp án có ảnh</div>
                            <div class="small text-muted mb-2">
                                Dùng cột <code>matching_pairs</code>. Nếu vế phải là ảnh, thêm tiền tố <code>image:</code> trước đường dẫn ảnh CKFinder.
                            </div>
                            <div class="small">
                                <div><code>question_type</code>: <code>matching</code></div>
                                <div><code>matching_pairs</code>:</div>
                                <div><code>Cat=&gt;image:/data/images/cat.png|Dog=&gt;image:/data/images/dog.png</code></div>
                            </div>
                            <div class="small text-muted mt-2">
                                Nếu nối chữ với chữ thì không dùng <code>image:</code>, ví dụ <code>Cat=&gt;Con mèo|Dog=&gt;Con chó</code>.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion mb-3" id="importExamples">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="exampleSelectHead">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#exampleSelect">
                                Ví dụ câu chọn đáp án
                            </button>
                        </h2>
                        <div id="exampleSelect" class="accordion-collapse collapse" data-bs-parent="#importExamples">
                            <div class="accordion-body small">
                                <div><code>question_type</code>: <code>select</code></div>
                                <div><code>options</code>: <code>am|is|are|be</code></div>
                                <div><code>correct_option</code>: <code>0</code> nếu đáp án đúng là <code>am</code>.</div>
                                <div class="text-muted mt-2">Vị trí đáp án bắt đầu từ 0: đáp án 1 là 0, đáp án 2 là 1, đáp án 3 là 2.</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="exampleInputHead">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#exampleInput">
                                Ví dụ câu nhập đáp án
                            </button>
                        </h2>
                        <div id="exampleInput" class="accordion-collapse collapse" data-bs-parent="#importExamples">
                            <div class="accordion-body small">
                                <div><code>question_type</code>: <code>input</code></div>
                                <div><code>correct_answer</code>: <code>goes</code></div>
                                <div class="text-muted mt-2">Không cần điền <code>options</code> hoặc <code>correct_option</code>.</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="exampleOrderingHead">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#exampleOrdering">
                                Ví dụ câu sắp xếp đáp án
                            </button>
                        </h2>
                        <div id="exampleOrdering" class="accordion-collapse collapse" data-bs-parent="#importExamples">
                            <div class="accordion-body small">
                                <div><code>question_type</code>: <code>ordering</code></div>
                                <div><code>ordering_items</code>: <code>I|am|a student</code></div>
                                <div class="text-muted mt-2">Nhập các mục theo đúng thứ tự đáp án chuẩn. Hệ thống sẽ tự đảo thứ tự khi học viên làm bài.</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="exampleMatchingHead">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#exampleMatching">
                                Ví dụ câu nối đáp án
                            </button>
                        </h2>
                        <div id="exampleMatching" class="accordion-collapse collapse" data-bs-parent="#importExamples">
                            <div class="accordion-body small">
                                <div class="mb-2">Nối chữ với chữ:</div>
                                <div><code>matching_pairs</code>: <code>Cat=&gt;Con mèo|Dog=&gt;Con chó</code></div>
                                <div class="mt-3 mb-2">Nối chữ với ảnh:</div>
                                <div><code>matching_pairs</code>: <code>Cat=&gt;image:/data/images/cat.png|Dog=&gt;image:/data/images/dog.png</code></div>
                                <div class="text-muted mt-2">Mỗi cặp dùng dạng <code>Vế trái=&gt;Vế phải</code>, các cặp ngăn cách bằng dấu <code>|</code>.</div>
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
                        <div class="form-text">Dung lượng tối đa 4MB. Hệ thống sẽ kiểm tra toàn bộ file trước khi ghi dữ liệu.</div>
                    </div>
                    <div class="d-flex gap-2">
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
