@extends('layouts.admin')
@section('title', 'Thu vien bai tap')
@section('page-title', 'Thu vien bai tap')
@section('page-actions')
    <a href="/admin/exercises/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Thêm bai tap
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:220px"
                   placeholder="Tim kiem..." value="{{ request('search') }}">
            <select name="type" class="form-select form-select-sm" style="max-width:150px">
                <option value="">Tất cả loai</option>
                @foreach(['reading','writing','listening','speaking','grammar','vocabulary'] as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            <select name="difficulty" class="form-select form-select-sm" style="max-width:130px">
                <option value="">Độ khó</option>
                @foreach(['easy','medium','hard'] as $d)
                    <option value="{{ $d }}" {{ request('difficulty') == $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            @if(request()->hasAny(['search','type','difficulty']))
                <a href="/admin/exercises" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Tieu de</th><th>Loai</th><th>Độ khó</th><th>Nguoi tao</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($exercises as $exercise)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $exercise->title }}</div>
                            @if($exercise->description)
                                <small class="text-muted">{{ Str::limit($exercise->description, 60) }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-info text-dark">{{ ucfirst($exercise->type) }}</span></td>
                        <td>
                            <span class="badge
                                {{ ['easy'=>'bg-success','medium'=>'bg-warning text-dark','hard'=>'bg-danger'][$exercise->difficulty] ?? 'bg-secondary' }}">
                                {{ ucfirst($exercise->difficulty) }}
                            </span>
                        </td>
                        <td>{{ $exercise->creator->name ?? '-' }}</td>
                        <td class="text-end">
                            <a href="/admin/exercises/{{ $exercise->id }}/edit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/admin/exercises/{{ $exercise->id }}" class="d-inline"
                                  onsubmit="return confirm('Xóa bài tập này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có bai tap nao.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($exercises, 'links'))
        <div class="card-footer bg-white">{{ $exercises->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

