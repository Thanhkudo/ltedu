@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $cards = [
            ['label'=>__('ui.classes'), 'value'=>$stats['classes'], 'icon'=>'journal-bookmark-fill', 'color'=>'primary'],
            ['label'=>__('ui.students'), 'value'=>$stats['students'], 'icon'=>'people-fill', 'color'=>'success'],
            ['label'=>__('ui.assignments'), 'value'=>$stats['exercises'], 'icon'=>'file-earmark-text-fill','color'=>'info'],
            ['label'=>__('ui.pending_grading'), 'value'=>$stats['submissions'], 'icon'=>'hourglass-split', 'color'=>'danger'],
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
                <span class="fw-semibold"><i class="bi bi-journal-bookmark me-2 text-primary"></i>{{ __('ui.recent_classes') }}</span>
                <a href="/admin/classes" class="btn btn-sm btn-outline-primary">{{ __('ui.view_all') }}</a>
            </div>
            <div class="list-group list-group-flush">
                @foreach($recentClasses as $class)
                    <a href="/admin/classes/{{ $class->id }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <div>
                            <div class="fw-semibold">{{ $class->name }}</div>
                            <small class="text-muted">{{ __('ui.teacher') }}: {{ $class->teacher->name ?? '-' }}</small>
                        </div>
                        <span class="text-{{ $class->status === 'active' ? 'success' : 'secondary' }} fw-semibold">
                            {{ ['active' => 'Đang hoạt động', 'inactive' => 'Tạm dừng', 'completed' => 'Đã hoàn thành'][$class->status] ?? ucfirst($class->status) }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between">
                <span class="fw-semibold"><i class="bi bi-people me-2 text-success"></i>{{ __('ui.recent_students') }}</span>
                <a href="/admin/students" class="btn btn-sm btn-outline-success">{{ __('ui.view_all') }}</a>
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
