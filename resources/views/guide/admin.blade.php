@extends('layouts.admin')
@section('title', __('ui.guide.title'))
@section('page-title', __('ui.guide.title'))
@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>{{ __('ui.guide.back_to_dashboard') }}
    </a>
@endsection

@section('content')
    @include('guide.partials.content')
@endsection
