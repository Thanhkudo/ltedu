@extends('layouts.admin')
@section('title', 'Quản lý lớp học')
@section('page-title', 'Quản lý lớp học')
@section('page-actions')
    <a href="/admin/classes/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Thêm lớp học
    </a>
@endsection

@section('content')
<div class="card class-index-card">
    <div class="table-responsive class-table-wrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã lớp</th><th>Tên lớp</th><th>Giáo viên</th>
                    <th>Bắt đầu</th><th>Trạng thái</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $class)
                    <tr>
                        <td><span class="badge bg-light text-dark">{{ $class->class_code }}</span></td>
                        <td class="fw-semibold">{{ $class->name }}</td>
                        <td>{{ $class->teacher->name ?? '-' }}</td>
                        <td>{{ $class->start_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $class->status }}">{{ ['active' => 'Đang hoạt động', 'inactive' => 'Tạm dừng', 'completed' => 'Đã hoàn thành'][$class->status] ?? ucfirst($class->status) }}</span>
                        </td>
                        <td class="text-end">
                            <a href="/admin/classes/{{ $class->id }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="/admin/classes/{{ $class->id }}/edit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/admin/classes/{{ $class->id }}" class="d-inline"
                                  onsubmit="return confirm('Xóa lớp học này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có lớp học nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="class-mobile-list">
        @forelse($classes as $class)
            <article class="class-mobile-card">
                <div class="class-mobile-head">
                    <div>
                        <span class="badge bg-light text-dark mb-2">{{ $class->class_code }}</span>
                        <div class="class-mobile-title">{{ $class->name }}</div>
                    </div>
                    <span class="badge badge-{{ $class->status }}">
                        {{ ['active' => 'Đang hoạt động', 'inactive' => 'Tạm dừng', 'completed' => 'Đã hoàn thành'][$class->status] ?? ucfirst($class->status) }}
                    </span>
                </div>

                <div class="class-mobile-meta">
                    <div>
                        <span>Giáo viên</span>
                        <strong>{{ $class->teacher->name ?? '-' }}</strong>
                    </div>
                    <div>
                        <span>Bắt đầu</span>
                        <strong>{{ $class->start_date->format('d/m/Y') }}</strong>
                    </div>
                </div>

                <div class="class-mobile-actions">
                    <a href="/admin/classes/{{ $class->id }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye me-1"></i>Xem
                    </a>
                    <a href="/admin/classes/{{ $class->id }}/edit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Sửa
                    </a>
                    <form method="POST" action="/admin/classes/{{ $class->id }}"
                          onsubmit="return confirm('Xóa lớp học này?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Xóa
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="class-mobile-empty">Chưa có lớp học nào.</div>
        @endforelse
    </div>

    @if(method_exists($classes, 'links'))
        <div class="card-footer bg-white">{{ $classes->links() }}</div>
    @endif
</div>
@endsection

@push('styles')
    <style>
        .class-mobile-list {
            display: none;
        }

        @media (max-width: 767.98px) {
            .class-index-card {
                background: transparent;
                box-shadow: none;
            }

            .class-table-wrap {
                display: none;
            }

            .class-mobile-list {
                display: grid;
                gap: 10px;
            }

            .class-mobile-card {
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 12px;
                background: #fff;
                box-shadow: 0 8px 18px rgba(15, 23, 42, .05);
            }

            .class-mobile-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 10px;
            }

            .class-mobile-title {
                font-weight: 800;
                color: #0f172a;
                overflow-wrap: anywhere;
            }

            .class-mobile-meta {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
                margin-top: 12px;
            }

            .class-mobile-meta div {
                border-radius: 10px;
                padding: 9px 10px;
                background: #f8fafc;
                border: 1px solid #edf2f7;
            }

            .class-mobile-meta span {
                display: block;
                margin-bottom: 3px;
                font-size: .72rem;
                font-weight: 800;
                color: #64748b;
            }

            .class-mobile-meta strong {
                display: block;
                font-size: .86rem;
                color: #0f172a;
                overflow-wrap: anywhere;
            }

            .class-mobile-actions {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
                margin-top: 12px;
                align-items: stretch;
            }

            .class-mobile-actions form {
                display: block;
                height: 100%;
                margin: 0;
            }

            .class-mobile-actions .btn,
            .class-mobile-actions button {
                width: 100%;
                height: 40px;
                min-height: 40px;
                max-height: 40px;
            }

            .class-mobile-actions .btn,
            .class-mobile-actions button {
                padding: 0 8px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                font-size: .8rem;
                font-weight: 800;
                line-height: 1;
                white-space: nowrap;
                vertical-align: middle;
            }

            .class-mobile-actions i {
                margin-right: 0 !important;
                font-size: .95rem;
            }

            .class-mobile-empty {
                border: 1px dashed #cbd5e1;
                border-radius: 14px;
                padding: 18px;
                background: #fff;
                text-align: center;
                color: #64748b;
            }
        }

        @media (max-width: 380px) {
            .class-mobile-meta {
                grid-template-columns: 1fr;
            }

            .class-mobile-actions {
                grid-template-columns: repeat(3, 1fr);
            }

            .class-mobile-actions .btn,
            .class-mobile-actions button {
                font-size: 0;
                min-height: 38px;
            }

            .class-mobile-actions i {
                font-size: 1rem;
            }
        }
    </style>
@endpush

