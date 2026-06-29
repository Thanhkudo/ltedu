@extends('layouts.admin')
@section('title', 'Nhóm bài đọc/nghe')
@section('page-title', 'Nhóm bài đọc/nghe')
@section('page-actions')
    <a href="{{ route('admin.question-bank.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kho câu hỏi
    </a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Tạo nhóm mới</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.question-groups.store') }}">
                    @csrf
                    @include('admin.question-bank.partials.group-form', ['group' => null, 'categories' => $categories])
                    <button class="btn btn-primary w-100 mt-3"><i class="bi bi-check-lg me-1"></i>Lưu nhóm</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-6">
                        <select name="category_id" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                    Lớp {{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">Tất cả loại</option>
                            <option value="reading" {{ request('type') === 'reading' ? 'selected' : '' }}>Bài đọc</option>
                            <option value="listening" {{ request('type') === 'listening' ? 'selected' : '' }}>Bài nghe</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Lọc</button>
                        <a href="{{ route('admin.question-groups.index') }}" class="btn btn-outline-secondary btn-sm">Xóa</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nhóm</th>
                            <th>Danh mục</th>
                            <th>Loại</th>
                            <th>Câu hỏi</th>
                            <th>Trạng thái</th>
                            <th style="width:150px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $group->title ?: ($group->type === 'reading' ? 'Bài đọc' : 'Bài nghe') }}</div>
                                    <div class="small text-muted">
                                        @if($group->type === 'reading')
                                            {{ \Illuminate\Support\Str::limit($group->passage, 90) }}
                                        @else
                                            {{ $group->audio_url ?: '-' }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold">{{ $group->category->name ?? '-' }}</div>
                                    <div class="small text-muted">Lớp {{ $group->category->grade_level ?? '?' }} - {{ ucfirst($group->category->skill_type ?? '') }}</div>
                                </td>
                                <td>{{ $group->type === 'reading' ? 'Bài đọc' : 'Bài nghe' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $group->questions_count }}</span></td>
                                <td>
                                    <span class="badge {{ $group->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $group->is_active ? 'Đang dùng' : 'Tắt' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editGroup{{ $group->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.question-groups.destroy', $group->id) }}" class="d-inline" onsubmit="return confirm('Xóa nhóm này?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editGroup{{ $group->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.question-groups.update', $group->id) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Sửa nhóm câu hỏi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                @include('admin.question-bank.partials.group-form', ['group' => $group, 'categories' => $categories])
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button class="btn btn-primary">Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Chưa có nhóm bài đọc/nghe.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($groups->hasPages())
                <div class="card-footer bg-white">{{ $groups->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/ckfinder/ckfinder.js') }}"></script>
<script>CKFinder.config({ connectorPath: @json(route('ckfinder_connector')) });</script>
<script>
function chooseGroupAudio(button) {
    const input = button.closest('.input-group').querySelector('input');
    if (!input.id) input.id = 'group_audio_' + Math.random().toString(36).slice(2);
    CKFinder.popup({
        chooseFiles: true,
        resourceType: 'Audios',
        connectorPath: '{{ route('ckfinder_connector') }}',
        onInit: function(finder) {
            finder.on('files:choose', function(evt) {
                input.value = evt.data.files.first().getUrl();
            });
        }
    });
}

function toggleGroupForm(select) {
    const form = select.closest('form') || select.closest('.modal-content');
    if (!form) return;
    form.querySelectorAll('[data-group-reading]').forEach(el => el.classList.toggle('d-none', select.value !== 'reading'));
    form.querySelectorAll('[data-group-listening]').forEach(el => el.classList.toggle('d-none', select.value !== 'listening'));
}

document.querySelectorAll('[data-group-type]').forEach(select => {
    toggleGroupForm(select);
    select.addEventListener('change', () => toggleGroupForm(select));
});
</script>
@endpush
