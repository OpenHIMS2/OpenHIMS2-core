@extends('layouts.clinical')
@section('title', $pageTitle ?? 'Office - Doctor View')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                <i class="bi bi-person-badge-fill text-primary fs-3"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0">{{ $viewTemplate->name }}</h3>
                <p class="text-muted mb-0">
                    <i class="bi bi-building me-1"></i>{{ $unit->name }}
                    &nbsp;&bull;&nbsp;
                    <i class="bi bi-geo-alt me-1"></i>{{ $unit->institution->name }}
                </p>
            </div>
        </div>
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Office Doctor View</strong> — This page is under development.
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-layout-text-window-reverse" style="font-size:4rem; opacity:.2;"></i>
                <h5 class="mt-3">Office — Doctor View</h5>
                <p class="mb-0 small">Medical officer tasks and clinical records will appear here.</p>
            </div>
        </div>
    </div>
</div>
@endsection
