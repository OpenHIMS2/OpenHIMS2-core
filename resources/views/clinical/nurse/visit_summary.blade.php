@extends('layouts.clinical')
@section('title', $pageTitle ?? 'Visit Summary')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9 col-xl-8">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('clinical.nurse.patient-history', [$unitView->id, $visit->patient_id]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Visit Summary</h4>
            <p class="text-muted mb-0 small">
                {{ $unitView->unit->name }} &bull; {{ $unitView->unit->institution->name }}
            </p>
        </div>
    </div>

    {{-- Visit info strip --}}
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
        <div class="card-body py-3 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-secondary flex-shrink-0"
                     style="width:2.8rem;height:2.8rem;font-size:1rem;">
                    #{{ $visit->visit_number }}
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $visit->patient->name }}</div>
                    <div class="text-muted small">
                        {{ $visit->patient->phn ?? '—' }}
                        <span class="mx-1">·</span>
                        {{ $visit->visit_date->format('d F Y') }}
                        <span class="mx-1">·</span>
                        <i class="bi {{ $catIcon }} me-1 text-{{ $catColor }}"></i>{{ $catLabel }}
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle">Visited</span>
            </div>
        </div>
    </div>

    {{-- Placeholder --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-medical" style="font-size:3rem;opacity:.15;"></i>
            <h6 class="mt-3 fw-normal text-muted">Visit summary under development</h6>
            <p class="small mb-0">Clinical notes, prescriptions, and examination findings from this visit will be shown here.</p>
        </div>
    </div>

</div>
</div>
@endsection
