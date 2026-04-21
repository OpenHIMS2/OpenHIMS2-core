@extends('layouts.clinical')
@section('title', $pageTitle ?? 'GI - Nurse View')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="rounded-3 p-3" style="background:rgba(236,72,153,.1);">
                <i class="bi bi-heart-pulse-fill fs-3" style="color:#ec4899;"></i>
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
            <strong>GI Nurse View</strong> — This page is under development.
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-layout-text-window-reverse" style="font-size:4rem; opacity:.2;"></i>
                <h5 class="mt-3">General Inward — Nurse View</h5>
            </div>
        </div>
    </div>
</div>
@endsection
