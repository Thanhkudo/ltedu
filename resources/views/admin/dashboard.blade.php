@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $cards = [
            ['label'=>'Lớp học',      'value'=>$stats['classes'],     'icon'=>'journal-bookmark-fill', 'color'=>'primary'],
            ['label'=>'Học viên',     'value'=>$stats['students'],    'icon'=>'people-fill',           'color'=>'success'],
            ['label'=>'Bài tập',      'value'=>$stats['exercises'],   'icon'=>'file-earmark-text-fill','color'=>'info'],            ['label'=>'Chờ chấm bài', 'value'=>$stats['submissions'], 'icon'=>'hourglass-split',       'color'=>'danger'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card text-center">
                <div class="card-body py-3">
                    <i class="bi bi-{{ $card['icon'] }} text-{{ $card['color'] }} fs-3"></i>
                    <div class="fs-3 fw-bold mt-1">{{ $card['value'] }}</div>
                    <div class="text-muted small">{{ $card['label'] }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between">
                <span class="fw-semibold"><i class="bi bi-journal-bookmark me-2 text-primary"></i>Lớp học gần đây</span>
                <a href="/admin/classes" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="list-group list-group-flush">
                @foreach($recentClasses as $class)
                    <a href="/admin/classes/{{ $class->id }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <div>
                            <div class="fw-semibold">{{ $class->name }}</div>
                            <small class="text-muted">GV: {{ $class->teacher->name ?? '—' }}</small>
                        </div>
                        <span class="badge rounded-pill bg-{{ $class->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($class->status) }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between">
                <span class="fw-semibold"><i class="bi bi-people me-2 text-success"></i>Học viên gần đây</span>
                <a href="/admin/students" class="btn btn-sm btn-outline-success">Xem tất cả</a>
            </div>
            <div class="list-group list-group-flush">
                @foreach($recentStudents as $student)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $student->full_name }}</div>
                            <small class="text-muted">{{ $student->email }}</small>
                        </div>
                        <span class="badge bg-light text-dark">{{ $student->student_code }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
