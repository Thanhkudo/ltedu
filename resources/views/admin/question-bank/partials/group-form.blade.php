@php
    $group = $group ?? null;
    $type = old('type', $group->type ?? 'reading');
@endphp

<div class="mb-3">
    <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
    <select name="category_id" class="form-select" required>
        <option value="">-- Chọn danh mục --</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ (string) old('category_id', $group->category_id ?? '') === (string) $cat->id ? 'selected' : '' }}>
                Lớp {{ $cat->grade_level }} - {{ ucfirst($cat->skill_type) }} - {{ $cat->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="row g-2">
    <div class="col-md-12">
        <label class="form-label fw-semibold">Loại nhóm</label>
        <select name="type" class="form-select" data-group-type>
            <option value="reading" {{ $type === 'reading' ? 'selected' : '' }}>Bài đọc</option>
            <option value="listening" {{ $type === 'listening' ? 'selected' : '' }}>Bài nghe</option>
        </select>
    </div>
</div>

<div class="mt-3">
    <label class="form-label fw-semibold">Tiêu đề nhóm</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $group->title ?? '') }}" placeholder="Ví dụ: Unit 1 - Reading 1">
</div>

<div class="mt-3" data-group-reading>
    <label class="form-label fw-semibold">Đoạn văn</label>
    <textarea name="passage" rows="6" class="form-control">{{ old('passage', $group->passage ?? '') }}</textarea>
</div>

<div class="mt-3" data-group-listening>
    <label class="form-label fw-semibold">File audio</label>
    <div class="input-group">
        <input type="text" name="audio_url" class="form-control" value="{{ old('audio_url', $group->audio_url ?? '') }}" readonly>
        <button type="button" class="btn btn-outline-primary" onclick="chooseGroupAudio(this)">Chọn từ CKFinder</button>
    </div>
</div>

<div class="form-check mt-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active_{{ $group->id ?? 'new' }}" {{ old('is_active', $group->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active_{{ $group->id ?? 'new' }}" class="form-check-label">Đang sử dụng</label>
</div>
