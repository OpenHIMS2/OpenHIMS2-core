@extends('layouts.clinical')
@section('title', $pageTitle ?? "Today's Visit")

@push('styles')
<style>
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.25} }
    .blink-dot { animation: blink 1s ease-in-out infinite; display:inline-block; }
    .info-chip { background:#f1f5f9; border-radius:.5rem; padding:.35rem .75rem; font-size:.8rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9 col-xl-8">

    {{-- Back link --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('clinical.doctor.patient-history', [$unitView->id, $visit->patient_id]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Today's Visit</h4>
            <p class="text-muted mb-0 small">
                {{ $unitView->unit->name }} &bull; {{ $unitView->unit->institution->name }}
            </p>
        </div>
    </div>

    {{-- Patient + visit header card --}}
    @php
    $catLabels = [
        'opd'                    => ['OPD Patient',             'bi-hospital',         'primary'],
        'new_clinic_visit'       => ['New Clinic Visit',        'bi-person-plus-fill', 'success'],
        'recurrent_clinic_visit' => ['Recurrent Clinic Visit',  'bi-arrow-repeat',     'info'],
        'urgent'                 => ['Urgent Patient',          'bi-person-badge-fill','danger'],
    ];
    [$catLabel, $catIcon, $catColor] = $catLabels[$visit->category] ?? ['—', 'bi-dash', 'secondary'];
    @endphp

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-primary flex-shrink-0"
                     style="width:3rem;height:3rem;font-size:1.1rem;">
                    {{ $visit->visit_number }}
                </div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-0">{{ $visit->patient->name }}</h5>
                    <div class="text-muted small">
                        {{ $visit->patient->phn ?? '—' }}
                        <span class="mx-1">·</span>
                        {{ $visit->patient->computed_age ?? '—' }} yrs /
                        {{ ucfirst($visit->patient->gender) }}
                    </div>
                </div>
                @if($visit->status === 'in_progress')
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-6 py-2 px-3">
                        <span class="blink-dot me-1">●</span>In Progress
                    </span>
                @elseif($visit->status === 'waiting')
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 py-2 px-3">
                        Waiting
                    </span>
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="info-chip">
                    <i class="bi bi-calendar3 me-1 text-muted"></i>{{ $visit->visit_date->format('d F Y') }}
                </span>
                <span class="info-chip">
                    <i class="bi {{ $catIcon }} me-1 text-{{ $catColor }}"></i>{{ $catLabel }}
                </span>
                @if($visit->patient->nic)
                    <span class="info-chip">
                        <i class="bi bi-credit-card-2-front me-1 text-muted"></i>{{ $visit->patient->nic }}
                    </span>
                @endif
                @if($visit->patient->mobile)
                    <span class="info-chip">
                        <i class="bi bi-telephone me-1 text-muted"></i>{{ $visit->patient->mobile }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Clinical content placeholder ──────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <span class="fw-semibold"><i class="bi bi-clipboard2-pulse me-2 text-primary"></i>Clinical Notes</span>
        </div>
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-clipboard2-pulse" style="font-size:3rem;opacity:.15;"></i>
            <h6 class="mt-3 fw-normal text-muted">Clinical content coming soon</h6>
            <p class="small mb-0">Examination findings, prescriptions, and notes will appear here.</p>
        </div>
    </div>

    {{-- ── End visit button ────────────────────────────────────────────────── --}}
    @if($visit->status !== 'visited')
        <div class="d-flex justify-content-end">
            <form id="end-visit-form" method="POST"
                  action="{{ route('clinical.doctor.end-visit', [$unitView->id, $visit->id]) }}">
                @csrf
                <button type="button" class="btn btn-success btn-lg px-5"
                        onclick="confirmDialog({title:'End Visit', body:'Close this visit? This will mark the patient as Visited.', confirmText:'End Visit', confirmClass:'btn-success', icon:'bi-check-circle-fill text-success'}, () => document.getElementById('end-visit-form').submit())">
                    <i class="bi bi-check-circle-fill me-2"></i>End Visit
                </button>
            </form>
        </div>
    @else
        <div class="d-flex justify-content-end">
            <span class="badge bg-success fs-6 py-2 px-4">
                <i class="bi bi-check-circle-fill me-1"></i>Visit Completed
            </span>
        </div>
    @endif

</div>
</div>
@endsection
