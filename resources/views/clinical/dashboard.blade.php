@extends('layouts.clinical')
@section('title', 'My Views')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h4 class="fw-bold mb-1">Welcome, {{ Auth::user()->name }}</h4>
        <p class="text-muted mb-4">Select a clinical view to open.</p>

        @if($assignedViews->isEmpty())
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                No clinical views are assigned to your account. Contact your administrator.
            </div>
        @else
            <div class="row g-3">
                @foreach($assignedViews as $uv)
                <div class="col-sm-6">
                    <a href="{{ route('clinical.show', $uv->id) }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body d-flex align-items-start gap-3 p-4">
                            <div class="rounded-3 bg-success bg-opacity-10 p-3 flex-shrink-0">
                                <i class="bi bi-window-fullscreen text-success fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $uv->name }}</div>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-building me-1"></i>{{ $uv->unit->name }}
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $uv->unit->institution->name }}
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success mt-2">
                                    {{ $uv->viewTemplate->name }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
