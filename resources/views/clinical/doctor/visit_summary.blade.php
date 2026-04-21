@extends('layouts.clinical')
@section('title', $pageTitle ?? 'Visit Summary')

@push('styles')
<style>
    .summary-label {
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        font-weight: 600;
        margin-bottom: .35rem;
    }
    .tag-badge {
        display: inline-block;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: .4rem;
        padding: .2rem .55rem;
        font-size: .78rem;
        color: #334155;
        margin: .15rem .15rem .15rem 0;
    }
    .section-divider {
        border-top: 1px solid #f1f5f9;
        margin: 1.25rem 0;
    }
    .summary-section-title {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .07em;
        font-weight: 700;
        color: #64748b;
        margin-bottom: .75rem;
    }
    .empty-val { color: #cbd5e1; font-style: italic; font-size: .82rem; }
</style>
@endpush

@section('content')
@php
$catLabels = [
    'opd'                    => ['OPD Patient',            'bi-hospital',          'primary'],
    'new_clinic_visit'       => ['New Clinic Visit',       'bi-person-plus-fill',  'success'],
    'recurrent_clinic_visit' => ['Recurrent Clinic Visit', 'bi-arrow-repeat',      'info'],
    'urgent'                 => ['Urgent Patient',         'bi-person-badge-fill', 'danger'],
];
[$catLabel, $catIcon, $catColor] = $catLabels[$visit->category] ?? ['—', 'bi-dash', 'secondary'];
$note = $visit->note;

$histSections = [
    ['key'=>'presenting_complaints',  'label'=>'Presenting Complaints'],
    ['key'=>'complaint_durations',    'label'=>'Duration of Complaints'],
    ['key'=>'past_medical_history',   'label'=>'Past Medical History'],
    ['key'=>'past_surgical_history',  'label'=>'Past Surgical History'],
    ['key'=>'social_history',         'label'=>'Social History'],
    ['key'=>'menstrual_history',      'label'=>'Menstrual History'],
];
$examSections = [
    ['key'=>'general_looking',        'label'=>'General Looking'],
    ['key'=>'cardiology_findings',    'label'=>'Cardiovascular'],
    ['key'=>'respiratory_findings',   'label'=>'Respiratory'],
    ['key'=>'abdominal_findings',     'label'=>'Abdominal'],
    ['key'=>'neurological_findings',  'label'=>'Neurological'],
    ['key'=>'dermatological_findings','label'=>'Dermatological'],
];
@endphp

<div class="row justify-content-center">
<div class="col-lg-10 col-xl-9">

    {{-- ── Back + header ───────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('clinical.doctor.patient-history', [$unitView->id, $visit->patient_id]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="fw-bold mb-0">Visit Summary</h5>
            <p class="text-muted mb-0 small">{{ $unitView->unit->name }} &bull; {{ $unitView->unit->institution->name }}</p>
        </div>
    </div>

    {{-- ── Visit info strip ────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-secondary flex-shrink-0"
                         style="width:2.8rem;height:2.8rem;font-size:1rem;">#{{ $visit->visit_number }}</div>
                </div>
                <div class="col">
                    <div class="fw-semibold">{{ $visit->patient->name }}</div>
                    <div class="text-muted small">
                        {{ $visit->patient->phn ?? '—' }}
                        &nbsp;&bull;&nbsp;{{ $visit->patient->computed_age ?? '—' }} yrs
                        &nbsp;&bull;&nbsp;{{ ucfirst($visit->patient->gender) }}
                    </div>
                </div>
                <div class="col-auto text-end">
                    <div class="fw-semibold">{{ $visit->visit_date->format('d M Y') }}</div>
                    <div class="small">
                        <i class="bi {{ $catIcon }} me-1 text-{{ $catColor }}"></i>{{ $catLabel }}
                    </div>
                </div>
                <div class="col-auto">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        <i class="bi bi-check-circle-fill me-1"></i>Visited
                    </span>
                </div>
            </div>
            @if($visit->height || $visit->weight || $visit->bp_systolic || $visit->clinic_number)
            <hr class="my-2">
            <div class="row g-3" style="font-size:.82rem;">
                @if($visit->clinic_number)
                <div class="col-6 col-sm-3">
                    <div class="summary-label">Clinic No.</div>
                    <span class="fw-semibold">{{ $visit->clinic_number }}</span>
                </div>
                @endif
                @if($visit->height)
                <div class="col-6 col-sm-3">
                    <div class="summary-label">Height</div>
                    <span class="fw-semibold">{{ $visit->height }} cm</span>
                </div>
                @endif
                @if($visit->weight)
                <div class="col-6 col-sm-3">
                    <div class="summary-label">Weight</div>
                    <span class="fw-semibold">{{ $visit->weight }} kg</span>
                </div>
                @endif
                @if($visit->bp_systolic && $visit->bp_diastolic)
                <div class="col-6 col-sm-3">
                    <div class="summary-label">BP (Admission)</div>
                    <span class="fw-semibold">{{ $visit->bp_systolic }}/{{ $visit->bp_diastolic }} mmHg</span>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- ── History ──────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>History</span>
        </div>
        <div class="card-body p-4">
            @php $hasAny = false; @endphp
            @foreach($histSections as $sec)
                @php $vals = $note ? ($note->{$sec['key']} ?? []) : []; @endphp
                @if(count($vals))
                    @php $hasAny = true; @endphp
                    <div class="mb-3">
                        <div class="summary-label">{{ $sec['label'] }}</div>
                        @foreach($vals as $v)
                            <span class="tag-badge">{{ $v }}</span>
                        @endforeach
                    </div>
                @endif
            @endforeach
            @if(!$hasAny)
                <span class="empty-val">No history recorded for this visit.</span>
            @endif
        </div>
    </div>

    {{-- ── Examination ──────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold"><i class="bi bi-stethoscope me-2 text-success"></i>Examination</span>
        </div>
        <div class="card-body p-4">
            @php $hasExam = false; @endphp

            {{-- Pulse rate --}}
            @if($note?->pulse_rate)
                @php $hasExam = true; @endphp
                <div class="mb-3">
                    <div class="summary-label">Pulse Rate</div>
                    <span class="tag-badge"><i class="bi bi-heart-pulse me-1 text-danger"></i>{{ $note->pulse_rate }} bpm</span>
                </div>
            @endif

            {{-- BP readings from doctor --}}
            @if($visit->bpReadings->isNotEmpty())
                @php $hasExam = true; @endphp
                <div class="mb-3">
                    <div class="summary-label">Blood Pressure</div>
                    @foreach($visit->bpReadings as $bp)
                        <span class="tag-badge">
                            <i class="bi bi-activity me-1 text-danger"></i>
                            {{ $bp->systolic }}/{{ $bp->diastolic }} mmHg
                            <span class="text-muted ms-1" style="font-size:.72rem;">{{ $bp->recorded_at->format('d M H:i') }}</span>
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Examination sections --}}
            @foreach($examSections as $sec)
                @php $vals = $note ? ($note->{$sec['key']} ?? []) : []; @endphp
                @if(count($vals))
                    @php $hasExam = true; @endphp
                    <div class="mb-3">
                        <div class="summary-label">{{ $sec['label'] }}</div>
                        @foreach($vals as $v)
                            <span class="tag-badge">{{ $v }}</span>
                        @endforeach
                    </div>
                @endif
            @endforeach

            @if(!$hasExam)
                <span class="empty-val">No examination findings recorded.</span>
            @endif
        </div>
    </div>

    {{-- ── Investigations ───────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold"><i class="bi bi-eyedropper me-2 text-warning"></i>Investigations</span>
        </div>
        <div class="card-body p-4">
            @if($visit->investigations->isEmpty())
                <span class="empty-val">No investigations recorded.</span>
            @else
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Investigation</th>
                            <th style="width:15%">Value</th>
                            <th style="width:20%">Date / Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visit->investigations as $inv)
                        <tr>
                            <td>{{ $inv->name }}</td>
                            <td class="fw-semibold">{{ $inv->value }}</td>
                            <td class="text-muted small">{{ $inv->recorded_at->format('d M Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ── Clinic Drugs ─────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold"><i class="bi bi-capsule me-2 text-info"></i>Clinic Drugs</span>
        </div>
        <div class="card-body p-4">
            @if($clinicDrugs->isEmpty())
                <span class="empty-val">No clinic drugs prescribed.</span>
            @else
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:8%">Route</th>
                            <th>Drug</th>
                            <th style="width:8%">Dose</th>
                            <th style="width:7%">Unit</th>
                            <th style="width:9%">Freq.</th>
                            <th style="width:10%">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clinicDrugs as $drug)
                        <tr>
                            <td>{{ $drug->type }}</td>
                            <td class="fw-medium">{{ $drug->name }}</td>
                            <td>{{ $drug->dose }}</td>
                            <td>{{ $drug->unit }}</td>
                            <td>{{ $drug->frequency }}</td>
                            <td>{{ $drug->duration ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ── Management ───────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold"><i class="bi bi-clipboard2-check me-2 text-primary"></i>Management</span>
        </div>
        <div class="card-body p-4">

            {{-- Management drugs --}}
            @if($managementDrugs->isNotEmpty())
            <div class="summary-section-title">Drugs</div>
            <table class="table table-sm table-bordered mb-4">
                <thead class="table-light">
                    <tr>
                        <th style="width:8%">Route</th>
                        <th>Drug</th>
                        <th style="width:8%">Dose</th>
                        <th style="width:7%">Unit</th>
                        <th style="width:9%">Freq.</th>
                        <th style="width:10%">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($managementDrugs as $drug)
                    <tr>
                        <td>{{ $drug->type }}</td>
                        <td class="fw-medium">{{ $drug->name }}</td>
                        <td>{{ $drug->dose }}</td>
                        <td>{{ $drug->unit }}</td>
                        <td>{{ $drug->frequency }}</td>
                        <td>{{ $drug->duration ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            {{-- Instructions --}}
            @php $instrs = $note?->management_instruction ?? []; @endphp
            @if(count($instrs))
            <div class="summary-section-title">Instructions</div>
            @foreach($instrs as $instr)
                <span class="tag-badge">{{ $instr }}</span>
            @endforeach
            @endif

            @if($managementDrugs->isEmpty() && !count($instrs))
                <span class="empty-val">No management recorded.</span>
            @endif
        </div>
    </div>

</div>
</div>
@endsection
