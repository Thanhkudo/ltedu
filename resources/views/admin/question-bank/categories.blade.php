@extends('layouts.admin')
@section('title', 'Danh mục câu hỏi')
@section('page-title', 'Quản lý danh mục câu hỏi')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Thêm danh mục</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.question-categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tên danh mục</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Trình độ</label>
                        <select name="grade_level" class="form-select" required>
                            <option value="6">Lớp 6</option>
                            <option value="7">Lớp 7</option>
                            <option value="8">Lớp 8</option>
                            <option value="9">Lớp 9</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Loại</label>
                        <select name="skill_type" class="form-select" required>
                            <option value="listening">Nghe</option>
                            <option value="speaking">Nói</option>
                            <option value="reading">Đọc</option>
                            <option value="writing">Viết</option>
                            <option value="grammar">Ngữ pháp</option>
                            <option value="vocabulary">Từ vựng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Dạng bài</label>
                        <input type="text" name="topic" class="form-control" placeholder="Ví dụ: thì hiện tại đơn, chia động từ...">
                    </div>
                    <button class="btn btn-primary w-100">Lưu danh mục</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Tên danh mục</th>
                        <th>Trình độ</th>
                        <th>Loại</th>
                        <th>Dạng</th>
                        <th style="width:190px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td>{{ $cat->name }}</td>
                            <td>Lớp {{ $cat->grade_level }}</td>
                            <td>{{ ucfirst($cat->skill_type) }}</td>
                            <td>{{ $cat->topic ?: '-' }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCat{{ $cat->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.question-categories.destroy', $cat->id) }}" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editCat{{ $cat->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title">Sửa danh mục</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.question-categories.update', $cat->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label small">Tên</label>
                                                <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small">Trình độ</label>
                                                    <select name="grade_level" class="form-select" required>
                                                        @for($g = 6; $g <= 9; $g++)
                                                            <option value="{{ $g }}" {{ (int) $cat->grade_level === $g ? 'selected' : '' }}>Lớp {{ $g }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small">Loại</label>
                                                    <select name="skill_type" class="form-select" required>
                                                        @foreach(['listening'=>'Nghe','speaking'=>'Nói','reading'=>'Đọc','writing'=>'Viết','grammar'=>'Ngữ pháp','vocabulary'=>'Từ vựng'] as $k => $lbl)
                                                            <option value="{{ $k }}" {{ $cat->skill_type === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <label class="form-label small">Dạng bài</label>
                                                <input type="text" name="topic" class="form-control" value="{{ $cat->topic }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                            <button class="btn btn-primary">Lưu</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Chưa có danh mục nào.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
                <div class="card-footer bg-white">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

