@extends('layouts.clinical')
@section('title', $pageTitle ?? "Today's Visit")

@push('styles')
<style>
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.25} }
    .blink-dot { animation: blink 1s ease-in-out infinite; display:inline-block; }
    .info-chip { background:#f1f5f9; border-radius:.5rem; padding:.35rem .75rem; font-size:.8rem; }
    .allergy-card { background:#fff0f3; border:1px solid #ffc0c0; }
    .tag-pill { display:inline-flex; align-items:center; gap:.3rem; background:#e2e8f0;
                border:1px solid #cbd5e1; border-radius:1rem; padding:.2rem .65rem;
                font-size:.8rem; color:#334155; }
    .tag-pill .remove-tag { cursor:pointer; font-size:.85rem; color:#94a3b8; line-height:1; }
    .tag-pill .remove-tag:hover { color:#ef4444; }
    .allergy-pill { background:#ffe4e6; border-color:#fca5a5; color:#9f1239; }
    .tag-input-wrap { position:relative; }
    .tag-dropdown { position:absolute; top:100%; left:0; right:0; z-index:50;
                    background:#fff; border:1px solid #dee2e6; border-radius:.375rem;
                    max-height:200px; overflow-y:auto; display:none; box-shadow:0 4px 12px rgba(0,0,0,.08); }
    .tag-dropdown .dd-item { padding:.5rem .75rem; cursor:pointer; font-size:.875rem; }
    .tag-dropdown .dd-item:hover { background:#f1f5f9; }
    .save-indicator { font-size:.75rem; color:#16a34a; opacity:0; transition:opacity .3s; }
    .save-indicator.show { opacity:1; }
    .history-section + .history-section,
    .exam-tag-section + .exam-tag-section { border-top:1px solid #e2e8f0; padding-top:1rem; margin-top:1rem; }
    .section-label { font-size:.75rem; font-weight:600; text-transform:uppercase;
                     letter-spacing:.05em; color:#64748b; margin-bottom:.5rem; }
    .tags-container { display:flex; flex-wrap:wrap; gap:.35rem; min-height:1.5rem; }
    /* BP table */
    .bp-table td { vertical-align:middle; font-size:.875rem; }
    .bp-time-edit { font-size:.8rem; padding:.2rem .4rem; }
    /* Drug autofill */
    @keyframes ai-fill { 0%{background:#dcfce7} 100%{background:transparent} }
    .ai-filled { animation: ai-fill 1.4s ease-out forwards; }
    .ai-badge { font-size:.65rem; vertical-align:middle; }
    .drug-name-wrap { position:relative; }
    .drug-ai-spinner { position:absolute; right:.5rem; top:50%; transform:translateY(-50%);
                       display:none; color:#16a34a; font-size:.8rem; }
    /* Clinic-loaded rows in management table */
    .clinic-loaded-row { background-color:#fef9c3 !important; }
    .clinic-loaded-row td { color:#713f12; }
    .clinic-loaded-badge { font-size:.6rem; vertical-align:middle; background:#fde68a;
                           color:#92400e; border:1px solid #fbbf24; border-radius:.25rem;
                           padding:.1rem .3rem; margin-left:.3rem; font-weight:600; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9 col-xl-8">

    {{-- Back nav + header --}}
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

    {{-- Patient header card --}}
    @php
    $catLabels = [
        'opd'                    => ['OPD Patient',            'bi-hospital',         'primary'],
        'new_clinic_visit'       => ['New Clinic Visit',       'bi-person-plus-fill', 'success'],
        'recurrent_clinic_visit' => ['Recurrent Clinic Visit', 'bi-arrow-repeat',     'info'],
        'urgent'                 => ['Urgent Patient',         'bi-person-badge-fill','danger'],
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
                @if($visit->clinic_number)
                    <span class="info-chip">
                        <i class="bi bi-hash me-1 text-muted"></i>{{ $visit->clinic_number }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── A. Demographics (new clinic visits only) ─────────────────────── --}}
    @if($visit->category === 'new_clinic_visit')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <span class="fw-semibold">
                <i class="bi bi-person-vcard me-2 text-primary"></i>Patient Information
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-2">
                <div class="col-6 col-md-4">
                    <div class="info-chip d-block">
                        <span class="text-muted d-block" style="font-size:.7rem;">Full Name</span>
                        <span class="fw-semibold">{{ $visit->patient->name ?? '—' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="info-chip d-block">
                        <span class="text-muted d-block" style="font-size:.7rem;">Age</span>
                        <span class="fw-semibold">{{ $visit->patient->computed_age ? $visit->patient->computed_age . ' yrs' : '—' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="info-chip d-block">
                        <span class="text-muted d-block" style="font-size:.7rem;">Sex</span>
                        <span class="fw-semibold">{{ $visit->patient->gender ? ucfirst($visit->patient->gender) : '—' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="info-chip d-block">
                        <span class="text-muted d-block" style="font-size:.7rem;">PHN Number</span>
                        <span class="fw-semibold">{{ $visit->patient->phn ?? '—' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="info-chip d-block">
                        <span class="text-muted d-block" style="font-size:.7rem;">Clinic Number</span>
                        <span class="fw-semibold">{{ $visit->clinic_number ?? '—' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="info-chip d-block">
                        <span class="text-muted d-block" style="font-size:.7rem;">Height (cm)</span>
                        <span class="fw-semibold">{{ $visit->height ?? '—' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="info-chip d-block">
                        <span class="text-muted d-block" style="font-size:.7rem;">Weight (kg)</span>
                        <span class="fw-semibold">{{ $visit->weight ?? '—' }}</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="info-chip d-block">
                        <span class="text-muted d-block" style="font-size:.7rem;">Contact</span>
                        <span class="fw-semibold">{{ $visit->patient->mobile ?? '—' }}</span>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="info-chip d-block">
                        <span class="text-muted d-block" style="font-size:.7rem;">Address</span>
                        <span class="fw-semibold">{{ $visit->patient->address ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── B. Allergies card ─────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4 allergy-card">
        <div class="card-header border-bottom" style="background:#fff0f3; border-color:#ffc0c0 !important;">
            <span class="fw-semibold">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Allergies
            </span>
        </div>
        <div class="card-body p-4">
            {{-- Input --}}
            <div class="tag-input-wrap mb-3">
                <div class="input-group">
                    <input type="text" id="allergy-input" class="form-control"
                           placeholder="Type drug or allergen name…" autocomplete="off">
                    <button class="btn btn-danger" id="allergy-add-btn" type="button">
                        <i class="bi bi-plus-lg"></i> Add
                    </button>
                </div>
                <div class="tag-dropdown" id="allergy-dropdown"></div>
            </div>
            {{-- Existing tags --}}
            <div id="allergy-tags" class="tags-container">
                @forelse($visit->patient->allergies as $allergy)
                    <span class="tag-pill allergy-pill"
                          data-id="{{ $allergy->id }}">
                        {{ $allergy->allergen }}
                        <span class="remove-tag" data-allergy-id="{{ $allergy->id }}">&times;</span>
                    </span>
                @empty
                    <span class="text-muted small" id="no-allergies-msg">No known allergies recorded</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── C. History card ───────────────────────────────────────────────── --}}
    @php
    $note = $visit->note;
    $sections = [
        ['key' => 'social_history', 'label' => 'Social History', 'category' => 'social_history'],
    ];
    if ($visit->patient->gender === 'female') {
        $sections[] = ['key' => 'menstrual_history', 'label' => 'Menstrual History', 'category' => 'menstrual_history'];
    }
    @endphp

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-semibold">
                <i class="bi bi-clipboard2-pulse me-2 text-primary"></i>Clinical History
            </span>
            <span class="save-indicator"><i class="bi bi-check-circle-fill me-1"></i>Saved</span>
        </div>
        <div class="card-body p-4">

            {{-- Presenting Complaints (complaint × duration combined) --}}
            <div class="history-section" id="sec-presenting_complaints" data-section-key="presenting_complaints">
                <div class="section-label">Presenting Complaints</div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-sm-5">
                        <div class="tag-input-wrap">
                            <input type="text" id="pc-complaint-input" class="form-control form-control-sm"
                                   placeholder="Complaint…" autocomplete="off">
                            <div class="tag-dropdown" id="pc-complaint-dropdown"></div>
                        </div>
                    </div>
                    <div class="col-auto px-0">
                        <span class="text-muted small fw-semibold">×</span>
                    </div>
                    <div class="col-sm-3">
                        <div class="tag-input-wrap">
                            <input type="text" id="pc-duration-input" class="form-control form-control-sm"
                                   placeholder="e.g. 5 days" autocomplete="off">
                            <div class="tag-dropdown" id="pc-duration-dropdown"></div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="button" id="pc-add-btn" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="tags-container history-tags"
                     data-initial="@json($note ? ($note->presenting_complaints ?? []) : [])">
                </div>
            </div>

            {{-- Past Medical History (condition for X years) --}}
            <div class="history-section" id="sec-past_medical_history" data-section-key="past_medical_history">
                <div class="section-label">Past Medical History</div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-sm-5">
                        <div class="tag-input-wrap">
                            <input type="text" id="pmh-condition-input" class="form-control form-control-sm"
                                   placeholder="Condition…" autocomplete="off">
                            <div class="tag-dropdown" id="pmh-condition-dropdown"></div>
                        </div>
                    </div>
                    <div class="col-auto px-1">
                        <span class="text-muted small fw-semibold">for</span>
                    </div>
                    <div class="col-sm-3">
                        <div class="tag-input-wrap">
                            <input type="text" id="pmh-years-input" class="form-control form-control-sm"
                                   placeholder="e.g. 5 years" autocomplete="off">
                            <div class="tag-dropdown" id="pmh-duration-dropdown"></div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="button" id="pmh-add-btn" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="tags-container history-tags"
                     data-initial="@json($note ? ($note->past_medical_history ?? []) : [])">
                </div>
            </div>

            {{-- Past Surgical History (procedure on YEAR) --}}
            <div class="history-section" id="sec-past_surgical_history" data-section-key="past_surgical_history">
                <div class="section-label">Past Surgical History</div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-sm-5">
                        <div class="tag-input-wrap">
                            <input type="text" id="psh-procedure-input" class="form-control form-control-sm"
                                   placeholder="Procedure…" autocomplete="off">
                            <div class="tag-dropdown" id="psh-procedure-dropdown"></div>
                        </div>
                    </div>
                    <div class="col-auto px-1">
                        <span class="text-muted small fw-semibold">on</span>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" id="psh-when-input" class="form-control form-control-sm"
                               placeholder="e.g. 2010" autocomplete="off">
                    </div>
                    <div class="col-auto">
                        <button type="button" id="psh-add-btn" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="tags-container history-tags"
                     data-initial="@json($note ? ($note->past_surgical_history ?? []) : [])">
                </div>
            </div>

            {{-- Social History / Menstrual History (standard tag sections) --}}
            @foreach($sections as $i => $sec)
            <div class="history-section" id="sec-{{ $sec['key'] }}"
                 data-section-key="{{ $sec['key'] }}">
                <div class="section-label">{{ $sec['label'] }}</div>
                <div class="tag-input-wrap mb-2">
                    <input type="text" class="form-control form-control-sm history-input"
                           data-category="{{ $sec['category'] }}"
                           placeholder="Type and press Enter…" autocomplete="off">
                    <div class="tag-dropdown history-dropdown"></div>
                </div>
                <div class="tags-container history-tags"
                     data-initial="@json($note ? ($note->{$sec['key']} ?? []) : [])">
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── D. Examination card ─────────────────────────────────────────────── --}}
    @php
    $note      = $note ?? $visit->note;
    $examSecs  = [
        ['key'=>'cardiology_findings',    'label'=>'Cardiology Examination Findings',  'cat'=>'cardiology_findings'],
        ['key'=>'respiratory_findings',   'label'=>'Respiratory Examination Findings', 'cat'=>'respiratory_findings'],
        ['key'=>'abdominal_findings',     'label'=>'Abdominal Examination Findings',   'cat'=>'abdominal_findings'],
        ['key'=>'neurological_findings',  'label'=>'Neurological Examination',         'cat'=>'neurological_findings'],
        ['key'=>'dermatological_findings','label'=>'Dermatological Findings',          'cat'=>'dermatological_findings'],
    ];
    @endphp
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-semibold">
                <i class="bi bi-activity me-2 text-success"></i>Examination
            </span>
            <span class="save-indicator" id="exam-save-indicator">
                <i class="bi bi-check-circle-fill me-1"></i>Saved
            </span>
        </div>
        <div class="card-body p-4">

            {{-- General Looking --}}
            <div class="exam-tag-section" id="sec-general_looking" data-section-key="general_looking">
                <div class="section-label">General Examination</div>
                <div class="tag-input-wrap mb-2">
                    <input type="text" class="form-control form-control-sm exam-input"
                           data-category="general_looking" placeholder="Type and press Enter…" autocomplete="off">
                    <div class="tag-dropdown exam-dropdown"></div>
                </div>
                <div class="tags-container exam-tags"
                     data-initial="@json($note?->general_looking ?? [])"></div>
            </div>

            {{-- Pulse Rate --}}
            <div class="border-top pt-3 mt-3 mb-3">
                <div class="section-label">Pulse Rate</div>
                <div class="input-group" style="max-width:180px;">
                    <input type="number" id="pulse-rate-input" class="form-control form-control-sm"
                           min="0" max="350" placeholder="bpm"
                           value="{{ $note?->pulse_rate ?? '' }}">
                    <span class="input-group-text text-muted small">bpm</span>
                </div>
            </div>

            {{-- Blood Pressure --}}
            <div class="border-top pt-3 mt-1 mb-3">
                <div class="section-label mb-3">Blood Pressure</div>

                {{-- Add form --}}
                <form method="POST"
                      action="{{ route('clinical.doctor.bp.store', [$unitView->id, $visit->id]) }}"
                      class="row g-2 align-items-end mb-3">
                    @csrf
                    <div class="col-auto">
                        <label class="form-label small fw-semibold mb-1">Systolic</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="systolic" class="form-control" min="40" max="300" style="width:75px;" required>
                            <span class="input-group-text text-muted" style="font-size:.75rem;">mmHg</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small fw-semibold mb-1">Diastolic</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="diastolic" class="form-control" min="20" max="200" style="width:75px;" required>
                            <span class="input-group-text text-muted" style="font-size:.75rem;">mmHg</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small fw-semibold mb-1">Date</label>
                        <input type="date" name="bp_date" id="bp_date" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small fw-semibold mb-1">Time</label>
                        <input type="time" name="bp_time" id="bp_time" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Add
                        </button>
                    </div>
                    @error('systolic') <div class="col-12"><span class="text-danger small">{{ $message }}</span></div> @enderror
                    @error('diastolic') <div class="col-12"><span class="text-danger small">{{ $message }}</span></div> @enderror
                </form>
                <script>
                (function () {
                    var n = new Date(), p = function (x) { return String(x).padStart(2, '0'); };
                    document.getElementById('bp_date').value =
                        n.getFullYear() + '-' + p(n.getMonth() + 1) + '-' + p(n.getDate());
                    document.getElementById('bp_time').value =
                        p(n.getHours()) + ':' + p(n.getMinutes());
                })();
                </script>

                {{-- Build sorted readings: previous visits (oldest→newest) then current visit --}}
                @php
                    $allBpReadings = collect();

                    // Previous visits – each reading labeled with visit date
                    foreach (($prevVisits ?? collect()) as $pv) {
                        if ($pv->bp_systolic && $pv->bp_diastolic) {
                            $allBpReadings->push((object)[
                                'id'          => null,
                                'systolic'    => (int) $pv->bp_systolic,
                                'diastolic'   => (int) $pv->bp_diastolic,
                                'recorded_at' => $pv->created_at,
                                'source'      => 'prev_admission',
                                'visit_label' => 'Visit #'.$pv->visit_number.' '.($pv->visit_date instanceof \Carbon\Carbon ? $pv->visit_date->format('d M') : \Carbon\Carbon::parse($pv->visit_date)->format('d M')),
                            ]);
                        }
                        foreach ($pv->bpReadings as $r) {
                            $allBpReadings->push((object)[
                                'id'          => $r->id,
                                'systolic'    => (int) $r->systolic,
                                'diastolic'   => (int) $r->diastolic,
                                'recorded_at' => $r->recorded_at,
                                'source'      => 'prev_doctor',
                                'visit_label' => 'Visit #'.$pv->visit_number.' '.($pv->visit_date instanceof \Carbon\Carbon ? $pv->visit_date->format('d M') : \Carbon\Carbon::parse($pv->visit_date)->format('d M')),
                            ]);
                        }
                    }

                    // Current visit admission reading
                    if ($visit->bp_systolic && $visit->bp_diastolic) {
                        $allBpReadings->push((object)[
                            'id'          => null,
                            'systolic'    => (int) $visit->bp_systolic,
                            'diastolic'   => (int) $visit->bp_diastolic,
                            'recorded_at' => $visit->created_at,
                            'source'      => 'admission',
                            'visit_label' => 'This Visit',
                        ]);
                    }
                    foreach ($visit->bpReadings as $r) {
                        $allBpReadings->push((object)[
                            'id'          => $r->id,
                            'systolic'    => (int) $r->systolic,
                            'diastolic'   => (int) $r->diastolic,
                            'recorded_at' => $r->recorded_at,
                            'source'      => 'doctor',
                            'visit_label' => 'This Visit',
                        ]);
                    }
                    $allBpReadings = $allBpReadings->sortBy('recorded_at')->values();

                    $bpChartData = $allBpReadings->map(function ($r) {
                        return [
                            'label'     => \Carbon\Carbon::parse($r->recorded_at)->format('d M Y'),
                            'sublabel'  => $r->visit_label ?? '',
                            'systolic'  => $r->systolic,
                            'diastolic' => $r->diastolic,
                            'source'    => $r->source,
                        ];
                    })->values()->toArray();
                @endphp

                {{-- Toggle buttons --}}
                <div class="d-flex justify-content-end mb-2">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" id="bp-chart-btn">
                            <i class="bi bi-graph-up"></i> Chart
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="bp-table-btn">
                            <i class="bi bi-table"></i> Table
                        </button>
                    </div>
                </div>

                {{-- Chart view (default) --}}
                <div id="bp-chart-view">
                    @if($allBpReadings->isEmpty())
                        <div class="text-center py-4 text-muted border rounded">
                            <i class="bi bi-heart-pulse" style="font-size:2rem;opacity:.15;"></i>
                            <p class="mt-2 small mb-0">No readings recorded yet</p>
                        </div>
                    @else
                        <canvas id="bp-chart" height="160"></canvas>
                    @endif
                </div>

                {{-- Table view (hidden by default) --}}
                <div id="bp-table-view" style="display:none;">
                    <table class="table table-sm table-bordered bp-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Systolic</th>
                                <th>Diastolic</th>
                                <th>Status</th>
                                <th>Date &amp; Time</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allBpReadings as $r)
                                @php
                                    $sys = $r->systolic; $dia = $r->diastolic;
                                    if ($sys >= 180 || $dia >= 120)      { $stText = 'Crisis';   $stCls = 'danger'; }
                                    elseif ($sys >= 140 || $dia >= 90)   { $stText = 'High';     $stCls = 'danger'; }
                                    elseif ($sys >= 130 || $dia >= 80)   { $stText = 'Elevated'; $stCls = 'warning'; }
                                    else                                  { $stText = 'Normal';   $stCls = 'success'; }
                                @endphp
                                <tr>
                                    <td><span class="fw-semibold text-danger">{{ $r->systolic }}</span> <small class="text-muted">mmHg</small></td>
                                    <td><span class="fw-semibold text-primary">{{ $r->diastolic }}</span> <small class="text-muted">mmHg</small></td>
                                    <td><span class="badge bg-{{ $stCls }}-subtle text-{{ $stCls }} border border-{{ $stCls }}-subtle">{{ $stText }}</span></td>
                                    <td class="small">
                                        {{ \Carbon\Carbon::parse($r->recorded_at)->format('d M Y H:i') }}
                                        @if($r->source === 'admission')
                                            <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Admission</span>
                                        @elseif(in_array($r->source, ['prev_admission','prev_doctor']))
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1" style="font-size:.65rem;">{{ $r->visit_label }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($r->source === 'doctor' && $r->id)
                                            <form method="POST"
                                                  action="{{ route('clinical.doctor.bp.delete', [$unitView->id, $r->id]) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this reading?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1">
                                                    <i class="bi bi-trash3" style="font-size:.7rem;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted small py-3">No readings recorded yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>{{-- end bp-table-view --}}
            </div>{{-- end BP section --}}

            {{-- Cardiology / Respiratory / Abdominal / Neurological / Dermatological --}}
            @foreach($examSecs as $es)
            <div class="exam-tag-section border-top pt-3 mt-1" id="sec-{{ $es['key'] }}" data-section-key="{{ $es['key'] }}">
                <div class="section-label">{{ $es['label'] }}</div>
                <div class="tag-input-wrap mb-2">
                    <input type="text" class="form-control form-control-sm exam-input"
                           data-category="{{ $es['cat'] }}" placeholder="Type and press Enter…" autocomplete="off">
                    <div class="tag-dropdown exam-dropdown"></div>
                </div>
                <div class="tags-container exam-tags"
                     data-initial="@json($note && $note->{$es['key']} !== null ? $note->{$es['key']} : ['Normal findings'])">
                </div>
            </div>
            @endforeach

        </div>
    </div>

    {{-- ── E. Investigations card ────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-semibold">
                <i class="bi bi-flask me-2 text-success"></i>Investigations
            </span>
        </div>
        <div class="card-body p-4">

            {{-- Add investigation form --}}
            @if($visit->status !== 'visited')
            <form id="inv-form" class="row g-2 align-items-end mb-3">
                @csrf
                <div class="col-sm-3">
                    <label class="form-label form-label-sm mb-1">Investigation</label>
                    <select name="name" id="inv_name" class="form-select form-select-sm" required>
                        <option value="" disabled selected>Select…</option>
                        @foreach(['FBS','HbA1c','Serum creatinine','Total cholesterol','TSH'] as $invOpt)
                            <option value="{{ $invOpt }}">{{ $invOpt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label form-label-sm mb-1">Value</label>
                    <input type="text" name="value" id="inv_value" class="form-control form-control-sm"
                           placeholder="e.g. 5.6 mmol/L" required maxlength="100">
                </div>
                <div class="col-sm-2">
                    <label class="form-label form-label-sm mb-1">Date</label>
                    <input type="date" name="inv_date" id="inv_date" class="form-control form-control-sm" required>
                </div>
                <div class="col-sm-2">
                    <label class="form-label form-label-sm mb-1">Time</label>
                    <input type="time" name="inv_time" id="inv_time" class="form-control form-control-sm" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Add
                    </button>
                </div>
            </form>
            <script>
            (function () {
                var n = new Date(), p = function (x) { return String(x).padStart(2, '0'); };
                document.getElementById('inv_date').value =
                    n.getFullYear() + '-' + p(n.getMonth() + 1) + '-' + p(n.getDate());
                document.getElementById('inv_time').value =
                    p(n.getHours()) + ':' + p(n.getMinutes());
            })();
            </script>
            @endif

            {{-- Unified investigation view (current + all previous, chart/table dual view) --}}
            @php $hasAnyInv = !empty($allInvData); @endphp

            <div id="inv-empty" class="text-center py-4 text-muted border rounded {{ $hasAnyInv ? 'd-none' : '' }}">
                <i class="bi bi-flask" style="font-size:2rem;opacity:.15;"></i>
                <p class="mt-2 small mb-0">No investigations recorded yet</p>
            </div>

            <div id="inv-container" class="{{ $hasAnyInv ? '' : 'd-none' }}">
                @foreach($allInvData as $invName => $readings)
                @php $sk = Str::slug($invName, '-'); @endphp
                <div class="inv-group mb-4 {{ !$loop->first ? 'border-top pt-3' : '' }}"
                     id="inv-group-{{ $sk }}" data-inv-name="{{ $invName }}" data-safe-key="{{ $sk }}">

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold small">{{ $invName }}</span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary active inv-view-btn"
                                    data-group="{{ $sk }}" data-view="chart">
                                <i class="bi bi-graph-up"></i> Chart
                            </button>
                            <button type="button" class="btn btn-outline-secondary inv-view-btn"
                                    data-group="{{ $sk }}" data-view="table">
                                <i class="bi bi-table"></i> Table
                            </button>
                        </div>
                    </div>

                    <div id="inv-chart-view-{{ $sk }}">
                        <canvas id="inv-chart-{{ $sk }}" height="120"></canvas>
                    </div>

                    <div id="inv-table-view-{{ $sk }}" style="display:none;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Value</th>
                                    <th>Date &amp; Time</th>
                                    <th>Visit</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="inv-tbody-{{ $sk }}">
                                @foreach($readings as $r)
                                <tr id="{{ $r['id'] ? 'inv-row-'.$r['id'] : '' }}"
                                    class="{{ $r['current'] ? 'table-primary bg-opacity-10' : '' }}"
                                    data-inv-id="{{ $r['id'] }}"
                                    data-value="{{ $r['value'] }}"
                                    data-recorded-at="{{ $r['recorded_at'] }}">
                                    <td class="fw-semibold">{{ $r['value'] }}</td>
                                    <td class="small">{{ $r['recorded_at'] }}</td>
                                    <td class="small text-muted">{{ $r['visit_label'] }}</td>
                                    <td class="text-end">
                                        @if($r['deletable'] && $r['id'])
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger py-0 px-1 inv-delete-btn"
                                                data-id="{{ $r['id'] }}"
                                                data-url="{{ route('clinical.doctor.investigation.delete', [$unitView->id, $r['id']]) }}"
                                                data-group="{{ $sk }}">
                                            <i class="bi bi-trash3" style="font-size:.7rem;"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- ── F. Clinic Section ──────────────────────────────────────────────── --}}
    @php
        $drugTypes  = ['Oral','S/C','IM','IV','S/L','Syrup','MDI','DPI','Suppository','LA'];
        $drugUnits  = ['mg','g','mcg','ml','tabs','item'];
        $drugFreqs  = ['mane','nocte','bd','tds','daily','EOD','SOS'];
        $typeAbbr   = \App\Models\VisitDrug::$typeAbbr;
        $freqAbbr   = \App\Models\VisitDrug::$freqAbbr;

        // Build ordered visit list for summary tab (all completed prev visits + current if visited)
        $summaryVisits = $prevVisits->sortByDesc('visit_date')->sortByDesc('visit_number')->values();
        if ($visit->status === 'visited') {
            $summaryVisits = $summaryVisits->prepend($visit);
        }
        $summaryVisits = $summaryVisits->filter(function($v) {
            return in_array($v->category, ['new_clinic_visit', 'recurrent_clinic_visit']);
        })->values();

        $catBadge = [
            'opd'                    => ['OPD',             'bg-primary-subtle text-primary border-primary-subtle'],
            'new_clinic_visit'       => ['New Clinic',      'bg-success-subtle text-success border-success-subtle'],
            'recurrent_clinic_visit' => ['Recurrent Visit', 'bg-info-subtle text-info border-info-subtle'],
            'urgent'                 => ['Urgent',          'bg-danger-subtle text-danger border-danger-subtle'],
        ];
    @endphp
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom pb-0">
            <p class="fw-semibold mb-2" style="font-size:.875rem;">
                <i class="bi bi-hospital me-1 text-success"></i>Clinic Section
            </p>
            <ul class="nav nav-tabs card-header-tabs" id="drug-chart-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="drug-active-tab"
                            data-bs-toggle="tab" data-bs-target="#drug-active-pane" type="button" role="tab">
                        <i class="bi bi-capsule me-1 text-success"></i>Active Drugs
                        <span class="badge bg-success ms-1" id="drug-count-badge">{{ $clinicDrugs->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="drug-changes-tab"
                            data-bs-toggle="tab" data-bs-target="#drug-changes-pane" type="button" role="tab">
                        <i class="bi bi-clock-history me-1 text-muted"></i>Change List
                        <span class="badge bg-secondary ms-1" id="change-count-badge">{{ $allDrugChanges->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="visit-summary-tab"
                            data-bs-toggle="tab" data-bs-target="#visit-summary-pane" type="button" role="tab">
                        <i class="bi bi-journal-medical me-1 text-primary"></i>Clinic Visit Summary
                        <span class="badge bg-primary ms-1">{{ $summaryVisits->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4 tab-content" id="drug-chart-tab-content">

            {{-- ── Tab 1: Active Drugs ─────────────────────────────────── --}}
            <div class="tab-pane fade show active" id="drug-active-pane" role="tabpanel">

                {{-- Add form --}}
                @if($visit->status !== 'visited')
                <form id="drug-add-form" class="row g-2 align-items-end mb-4">
                    <div class="col-sm-3">
                        <label class="form-label form-label-sm mb-1">Drug Name</label>
                        <div class="drug-name-wrap">
                            <input type="text" name="name" id="drug_name" class="form-control form-control-sm"
                                   placeholder="Type to search…" autocomplete="off" required>
                            <span class="drug-ai-spinner" id="drug-ai-spinner">
                                <span class="spinner-border spinner-border-sm"></span>
                            </span>
                            <div id="drug-name-dropdown" class="tag-dropdown"></div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label form-label-sm mb-1">Route</label>
                        <select name="type" id="drug_type" class="form-select form-select-sm">
                            @foreach($drugTypes as $dt)
                                <option value="{{ $dt }}" @selected($dt === 'Oral')>{{ $dt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <label class="form-label form-label-sm mb-1">Dose</label>
                        <input type="text" name="dose" id="drug_dose" class="form-control form-control-sm"
                               placeholder="e.g. 500" required maxlength="50">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label form-label-sm mb-1">Unit</label>
                        <select name="unit" id="drug_unit" class="form-select form-select-sm">
                            @foreach($drugUnits as $du)
                                <option value="{{ $du }}">{{ $du }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label form-label-sm mb-1">Frequency</label>
                        <select name="frequency" id="drug_frequency" class="form-select form-select-sm">
                            @foreach($drugFreqs as $df)
                                <option value="{{ $df }}">{{ $df }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label form-label-sm mb-1">Duration</label>
                        <div class="dur-group input-group input-group-sm" id="drug-dur-group">
                            <input type="text" inputmode="numeric" class="form-control form-control-sm dur-qty" value="30" style="max-width:58px;">
                            <select class="form-select form-select-sm dur-unit">
                                <option value="days">days</option>
                                <option value="weeks">weeks</option>
                                <option value="months">months</option>
                            </select>
                            <input type="hidden" name="duration" value="30 days">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Add
                        </button>
                    </div>
                </form>
                @endif

                {{-- Drugs table --}}
                <div id="drug-empty"
                     class="text-center py-4 text-muted border rounded {{ $clinicDrugs->isNotEmpty() ? 'd-none' : '' }}">
                    <i class="bi bi-capsule" style="font-size:2rem;opacity:.15;"></i>
                    <p class="mt-2 small mb-0">No drugs added yet</p>
                </div>

                <table id="drug-table"
                       class="table table-sm table-bordered mb-0 {{ $clinicDrugs->isEmpty() ? 'd-none' : '' }}">
                    <thead class="table-light">
                        <tr>
                            <th style="width:8%">Type</th>
                            <th>Drug</th>
                            <th style="width:8%">Dose</th>
                            <th style="width:7%">Unit</th>
                            <th style="width:9%">Freq.</th>
                            <th style="width:9%">Duration</th>
                            @if($visit->status !== 'visited')<th style="width:8%"></th>@endif
                        </tr>
                    </thead>
                    <tbody id="drug-tbody">
                        @foreach($clinicDrugs as $drug)
                        <tr id="drug-row-{{ $drug->id }}">
                            <td class="drug-cell-type">{{ $drug->type }}</td>
                            <td class="drug-cell-name">{{ $drug->name }}</td>
                            <td class="drug-cell-dose">{{ $drug->dose }}</td>
                            <td class="drug-cell-unit">{{ $drug->unit }}</td>
                            <td class="drug-cell-freq">{{ $drug->frequency }}</td>
                            <td class="drug-cell-dur">{{ $drug->duration ?? '30 days' }}</td>
                            @if($visit->status !== 'visited')
                            <td class="text-end text-nowrap">
                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary py-0 px-1 drug-edit-btn"
                                        data-id="{{ $drug->id }}"
                                        data-duration="{{ $drug->duration ?? '30 days' }}"
                                        data-url="{{ route('clinical.doctor.drug.update', [$unitView->id, $drug->id]) }}">
                                    <i class="bi bi-pencil" style="font-size:.7rem;"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger py-0 px-1 ms-1 drug-del-btn"
                                        data-id="{{ $drug->id }}"
                                        data-url="{{ route('clinical.doctor.drug.delete', [$unitView->id, $drug->id]) }}">
                                    <i class="bi bi-trash3" style="font-size:.7rem;"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ── Tab 2: Change List ───────────────────────────────────── --}}
            <div class="tab-pane fade" id="drug-changes-pane" role="tabpanel">

                <div id="change-empty" class="text-center py-4 text-muted {{ $allDrugChanges->isNotEmpty() ? 'd-none' : '' }}">
                    <i class="bi bi-clock-history" style="font-size:2rem;opacity:.15;"></i>
                    <p class="mt-2 small mb-0">No changes recorded yet</p>
                </div>

                <div id="change-table-wrap" class="{{ $allDrugChanges->isEmpty() ? 'd-none' : '' }}">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:70px;">Action</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="change-tbody">
                            @foreach($allDrugChanges as $ch)
                            <tr class="change-row">
                                <td class="align-middle">
                                    @if($ch->action === 'added')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Added</span>
                                    @elseif($ch->action === 'edited')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Edited</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">D/C</span>
                                    @endif
                                </td>
                                <td class="small text-secondary">{{ $ch->toSentence() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="change-pager" class="d-flex align-items-center justify-content-between px-2 py-2 border-top bg-light" style="font-size:.78rem;color:#6b7280;display:none!important;">
                        <span id="change-pager-info"></span>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" id="change-prev">
                                <i class="bi bi-chevron-left" style="font-size:.7rem;"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" id="change-next">
                                <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Tab 3: Clinic Visit Summary ────────────────────────────── --}}
            <div class="tab-pane fade" id="visit-summary-pane" role="tabpanel">
                @if($summaryVisits->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-journal-medical" style="font-size:2.5rem;opacity:.12;"></i>
                        <p class="mt-2 small mb-0">No clinic visits recorded yet</p>
                    </div>
                @else
                    <div class="accordion accordion-flush" id="visitSummaryAccordion">
                    @foreach($summaryVisits as $idx => $sv)
                    @php
                        $svNote   = $sv->note;
                        $svDrugs  = $sv->drugs->where('section', 'clinic')->values();
                        [$svCatLabel, $svCatCls] = $catBadge[$sv->category] ?? [$sv->category, 'bg-secondary-subtle text-secondary border-secondary-subtle'];
                        $isFirst  = $idx === 0;
                        $isCurrent = $sv->id === $visit->id;
                    @endphp
                    <div class="accordion-item border-0 {{ $idx > 0 ? 'border-top' : '' }}" data-vs-index="{{ $idx }}">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $isFirst ? '' : 'collapsed' }} fw-semibold py-3 px-0"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#vs-collapse-{{ $sv->id }}"
                                    aria-expanded="{{ $isFirst ? 'true' : 'false' }}">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"
                                          style="font-size:.7rem;">
                                        #{{ $sv->visit_number }}
                                    </span>
                                    <span class="small text-dark fw-semibold">
                                        {{ $sv->visit_date->format('d M Y') }}
                                    </span>
                                    <span class="badge border {{ $svCatCls }}" style="font-size:.7rem;">
                                        {{ $svCatLabel }}
                                    </span>
                                    @if($isCurrent)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:.7rem;">
                                            Current Visit
                                        </span>
                                    @endif
                                </div>
                            </button>
                        </h2>
                        <div id="vs-collapse-{{ $sv->id }}"
                             class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                             data-bs-parent="#visitSummaryAccordion">
                            <div class="accordion-body px-0 pt-0 pb-3">

                                {{-- Vitals row --}}
                                @if($sv->height || $sv->weight || $sv->bp_systolic || $sv->clinic_number)
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if($sv->clinic_number)
                                        <span class="info-chip"><i class="bi bi-tag me-1 text-muted"></i>Clinic No: <strong>{{ $sv->clinic_number }}</strong></span>
                                    @endif
                                    @if($sv->height)
                                        <span class="info-chip"><i class="bi bi-arrow-up-short text-muted"></i>{{ $sv->height }} cm</span>
                                    @endif
                                    @if($sv->weight)
                                        <span class="info-chip"><i class="bi bi-speedometer2 me-1 text-muted"></i>{{ $sv->weight }} kg</span>
                                    @endif
                                    @if($sv->bp_systolic && $sv->bp_diastolic)
                                        <span class="info-chip"><i class="bi bi-heart-pulse me-1 text-danger"></i>{{ $sv->bp_systolic }}/{{ $sv->bp_diastolic }} mmHg</span>
                                    @endif
                                </div>
                                @endif

                                @if($svNote)
                                <div class="row g-3">
                                    {{-- Presenting Complaints --}}
                                    @if(!empty($svNote->presenting_complaints))
                                    <div class="col-12 col-md-6">
                                        <p class="section-label mb-1">Presenting Complaints</p>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($svNote->presenting_complaints as $item)
                                                <span class="tag-pill">{{ $item }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Past Medical History --}}
                                    @if(!empty($svNote->past_medical_history))
                                    <div class="col-12 col-md-6">
                                        <p class="section-label mb-1">Past Medical History</p>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($svNote->past_medical_history as $item)
                                                <span class="tag-pill">{{ $item }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Past Surgical History --}}
                                    @if(!empty($svNote->past_surgical_history))
                                    <div class="col-12 col-md-6">
                                        <p class="section-label mb-1">Past Surgical History</p>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($svNote->past_surgical_history as $item)
                                                <span class="tag-pill">{{ $item }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    {{-- General Examination --}}
                                    @if(!empty($svNote->general_looking))
                                    <div class="col-12 col-md-6">
                                        <p class="section-label mb-1">General Examination</p>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($svNote->general_looking as $item)
                                                <span class="tag-pill">{{ $item }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Pulse Rate --}}
                                    @if($svNote->pulse_rate)
                                    <div class="col-6 col-md-3">
                                        <p class="section-label mb-1">Pulse Rate</p>
                                        <span class="tag-pill"><i class="bi bi-activity me-1"></i>{{ $svNote->pulse_rate }} bpm</span>
                                    </div>
                                    @endif

                                    {{-- Management Instructions --}}
                                    @if(!empty($svNote->management_instruction))
                                    <div class="col-12">
                                        <p class="section-label mb-1">Management Instructions</p>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($svNote->management_instruction as $item)
                                                <span class="tag-pill">{{ $item }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                {{-- Clinic Drugs --}}
                                @if($svDrugs->isNotEmpty())
                                <div class="mt-3">
                                    <p class="section-label mb-1">Clinic Drugs</p>
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Type</th>
                                                <th>Drug</th>
                                                <th>Dose</th>
                                                <th>Unit</th>
                                                <th>Freq.</th>
                                                <th>Duration</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($svDrugs as $d)
                                            <tr>
                                                <td class="small">{{ $d->type }}</td>
                                                <td class="small fw-semibold">{{ $d->name }}</td>
                                                <td class="small">{{ $d->dose }}</td>
                                                <td class="small">{{ $d->unit }}</td>
                                                <td class="small">{{ $d->frequency }}</td>
                                                <td class="small">{{ $d->duration ?? '30 days' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif

                                {{-- Investigations --}}
                                @if($sv->investigations->isNotEmpty())
                                <div class="mt-3">
                                    <p class="section-label mb-1">Investigations</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($sv->investigations as $inv)
                                            <span class="info-chip">
                                                <strong>{{ $inv->name }}</strong>: {{ $inv->value }}
                                                <span class="text-muted ms-1" style="font-size:.72rem;">
                                                    {{ \Carbon\Carbon::parse($inv->recorded_at)->format('d M Y') }}
                                                </span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if(!$svNote && $svDrugs->isEmpty() && $sv->investigations->isEmpty())
                                    <p class="text-muted small mb-0 mt-1">No clinical notes recorded for this visit.</p>
                                @endif

                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>

                    {{-- Pagination controls --}}
                    <div id="vs-pagination"
                         class="d-flex align-items-center justify-content-between px-0 pt-3 mt-1 border-top"
                         style="display:none!important;">
                        <span id="vs-pager-info" class="small text-muted"></span>
                        <div class="d-flex gap-1" id="vs-pager-btns"></div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ── G. Management ──────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-semibold">
                <i class="bi bi-clipboard2-check me-1 text-primary"></i>Management
            </span>
            @if($visit->status !== 'visited' && $clinicDrugs->isNotEmpty())
            <button type="button" id="load-clinic-drugs-btn" class="btn btn-sm btn-outline-info">
                <i class="bi bi-arrow-down-circle me-1"></i>Load Clinic Drugs
                <span class="badge bg-info text-white ms-1">{{ $clinicDrugs->count() }}</span>
            </button>
            @endif
        </div>
        <div class="card-body p-4">

            {{-- Management Drug Add Form --}}
            @if($visit->status !== 'visited')
            <form id="mgmt-drug-add-form" class="row g-2 align-items-end mb-4">
                <div class="col-sm-3">
                    <label class="form-label form-label-sm mb-1">Drug Name</label>
                    <div class="drug-name-wrap">
                        <input type="text" name="name" id="mgmt_drug_name" class="form-control form-control-sm"
                               placeholder="Type to search…" autocomplete="off" required>
                        <span class="drug-ai-spinner" id="mgmt-drug-ai-spinner">
                            <span class="spinner-border spinner-border-sm"></span>
                        </span>
                        <div id="mgmt-drug-name-dropdown" class="tag-dropdown"></div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="form-label form-label-sm mb-1">Route</label>
                    <select name="type" id="mgmt_drug_type" class="form-select form-select-sm">
                        @foreach($drugTypes as $dt)
                            <option value="{{ $dt }}" @selected($dt === 'Oral')>{{ $dt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <label class="form-label form-label-sm mb-1">Dose</label>
                    <input type="text" name="dose" id="mgmt_drug_dose" class="form-control form-control-sm"
                           placeholder="e.g. 500" required maxlength="50">
                </div>
                <div class="col-sm-1">
                    <label class="form-label form-label-sm mb-1">Unit</label>
                    <select name="unit" id="mgmt_drug_unit" class="form-select form-select-sm">
                        @foreach($drugUnits as $du)
                            <option value="{{ $du }}">{{ $du }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label form-label-sm mb-1">Frequency</label>
                    <select name="frequency" id="mgmt_drug_frequency" class="form-select form-select-sm">
                        @foreach($drugFreqs as $df)
                            <option value="{{ $df }}">{{ $df }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label form-label-sm mb-1">Duration</label>
                    <div class="dur-group input-group input-group-sm" id="mgmt-drug-dur-group">
                        <input type="text" inputmode="numeric" class="form-control form-control-sm dur-qty" value="5" style="max-width:58px;">
                        <select class="form-select form-select-sm dur-unit">
                            <option value="days">days</option>
                            <option value="weeks">weeks</option>
                            <option value="months">months</option>
                        </select>
                        <input type="hidden" name="duration" value="5 days">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Add
                    </button>
                </div>
            </form>
            @endif

            {{-- Management Drugs Table --}}
            <div id="mgmt-drug-empty"
                 class="text-center py-4 text-muted border rounded {{ $managementDrugs->isNotEmpty() ? 'd-none' : '' }}">
                <i class="bi bi-capsule" style="font-size:2rem;opacity:.15;"></i>
                <p class="mt-2 small mb-0">No management drugs added yet</p>
            </div>
            <table id="mgmt-drug-table"
                   class="table table-sm table-bordered mb-0 {{ $managementDrugs->isEmpty() ? 'd-none' : '' }}">
                <thead class="table-light">
                    <tr>
                        <th style="width:8%">Type</th>
                        <th>Drug</th>
                        <th style="width:8%">Dose</th>
                        <th style="width:7%">Unit</th>
                        <th style="width:9%">Freq.</th>
                        <th style="width:9%">Duration</th>
                        <th style="width:11%">Availability</th>
                        @if($visit->status !== 'visited')<th style="width:7%"></th>@endif
                    </tr>
                </thead>
                <tbody id="mgmt-drug-tbody">
                    @foreach($managementDrugs as $drug)
                    @php
                        $stock = \App\Models\PharmacyStock::whereHas('unitView.unit', fn($q) => $q->where('institution_id', $unitView->unit->institution_id))
                            ->whereRaw('LOWER(drug_name) = LOWER(?)', [$drug->name])
                            ->where('is_out_of_stock', false)
                            ->where('remaining', '>', 0)
                            ->sum('remaining');
                    @endphp
                    <tr id="mgmt-drug-row-{{ $drug->id }}">
                        <td class="mgmt-cell-type">{{ $drug->type }}</td>
                        <td class="mgmt-cell-name">{{ $drug->name }}</td>
                        <td class="mgmt-cell-dose">{{ $drug->dose }}</td>
                        <td class="mgmt-cell-unit">{{ $drug->unit }}</td>
                        <td class="mgmt-cell-freq">{{ $drug->frequency }}</td>
                        <td class="mgmt-cell-duration">{{ $drug->duration ?? '5 days' }}</td>
                        <td class="mgmt-cell-avail">
                            @if($stock > 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:.7rem;">
                                    <i class="bi bi-check-circle me-1"></i>Available
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:.7rem;">
                                    <i class="bi bi-x-circle me-1"></i>O/S
                                </span>
                            @endif
                        </td>
                        @if($visit->status !== 'visited')
                        <td class="text-end text-nowrap">
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary py-0 px-1 mgmt-drug-edit-btn"
                                    data-id="{{ $drug->id }}"
                                    data-url="{{ route('clinical.doctor.drug.update', [$unitView->id, $drug->id]) }}">
                                <i class="bi bi-pencil" style="font-size:.7rem;"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger py-0 px-1 ms-1 mgmt-drug-del-btn"
                                    data-id="{{ $drug->id }}"
                                    data-url="{{ route('clinical.doctor.drug.delete', [$unitView->id, $drug->id]) }}">
                                <i class="bi bi-trash3" style="font-size:.7rem;"></i>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Instruction --}}
            <div class="mt-4 pt-3 border-top instr-tag-section" data-section-key="management_instruction">
                <div class="section-label mb-2">Instruction</div>
                <div class="tag-input-wrap mb-2">
                    <input type="text" class="form-control form-control-sm instr-input"
                           data-category="general_instructions"
                           placeholder="Type or select an instruction…"
                           autocomplete="off"
                           {{ $visit->status === 'visited' ? 'disabled' : '' }}>
                    <div class="tag-dropdown instr-dropdown"></div>
                </div>
                <div class="tags-container instr-tags"
                     data-initial="{{ json_encode($note?->management_instruction ?? []) }}">
                </div>
                <span class="save-indicator mt-1 d-block" id="mgmt-instruction-saved">
                    <i class="bi bi-check-circle-fill me-1"></i>Saved
                </span>
            </div>

        </div>
    </div>

    {{-- Allergy Warning Modal --}}
    <div class="modal fade" id="allergyWarningModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white border-0">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Allergy Warning
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">
                        <strong id="allergy-warning-drug-name" class="text-danger"></strong>
                        is listed as an allergen for this patient.
                    </p>
                    <p class="mb-0 text-muted small">Are you sure you want to add this medication?</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" id="allergy-warning-confirm">
                        <i class="bi bi-exclamation-circle me-1"></i>Add Anyway
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Management Drug Edit Modal --}}
    @if($visit->status !== 'visited')
    <div class="modal fade" id="mgmtDrugEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Management Drug</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="mgmt-drug-edit-form">
                        <input type="hidden" id="mgmt_edit_drug_id">
                        <input type="hidden" id="mgmt_edit_drug_url">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label form-label-sm">Drug Name</label>
                                <div class="drug-name-wrap">
                                    <input type="text" name="name" id="mgmt_edit_name" class="form-control form-control-sm"
                                           autocomplete="off" required>
                                    <span class="drug-ai-spinner" id="mgmt-edit-drug-ai-spinner">
                                        <span class="spinner-border spinner-border-sm"></span>
                                    </span>
                                    <div id="mgmt-edit-drug-name-dropdown" class="tag-dropdown"></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <label class="form-label form-label-sm">Route</label>
                                <select name="type" id="mgmt_edit_type" class="form-select form-select-sm">
                                    @foreach($drugTypes as $dt)
                                        <option value="{{ $dt }}">{{ $dt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label form-label-sm">Dose</label>
                                <input type="text" name="dose" id="mgmt_edit_dose" class="form-control form-control-sm"
                                       required maxlength="50">
                            </div>
                            <div class="col-4">
                                <label class="form-label form-label-sm">Unit</label>
                                <select name="unit" id="mgmt_edit_unit" class="form-select form-select-sm">
                                    @foreach($drugUnits as $du)
                                        <option value="{{ $du }}">{{ $du }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label form-label-sm">Frequency</label>
                                <select name="frequency" id="mgmt_edit_frequency" class="form-select form-select-sm">
                                    @foreach($drugFreqs as $df)
                                        <option value="{{ $df }}">{{ $df }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label form-label-sm">Duration</label>
                                <div class="dur-group input-group input-group-sm" id="mgmt-edit-dur-group">
                                    <input type="text" inputmode="numeric" class="form-control form-control-sm dur-qty" value="5" style="max-width:58px;">
                                    <select class="form-select form-select-sm dur-unit">
                                        <option value="days">days</option>
                                        <option value="weeks">weeks</option>
                                        <option value="months">months</option>
                                    </select>
                                    <input type="hidden" name="duration" value="5 days">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="mgmt-drug-edit-save-btn">
                        <i class="bi bi-check-lg me-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Edit Drug Modal --}}
    @if($visit->status !== 'visited')
    <div class="modal fade" id="drugEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2 text-success"></i>Edit Drug</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="drug-edit-form">
                        <input type="hidden" id="edit_drug_id">
                        <input type="hidden" id="edit_drug_url">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label form-label-sm">Drug Name</label>
                                <div class="drug-name-wrap">
                                    <input type="text" name="name" id="edit_name" class="form-control form-control-sm"
                                           autocomplete="off" required>
                                    <span class="drug-ai-spinner" id="edit-drug-ai-spinner">
                                        <span class="spinner-border spinner-border-sm"></span>
                                    </span>
                                    <div id="edit-drug-name-dropdown" class="tag-dropdown"></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <label class="form-label form-label-sm">Route</label>
                                <select name="type" id="edit_type" class="form-select form-select-sm">
                                    @foreach($drugTypes as $dt)
                                        <option value="{{ $dt }}">{{ $dt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label form-label-sm">Dose</label>
                                <input type="text" name="dose" id="edit_dose" class="form-control form-control-sm"
                                       required maxlength="50">
                            </div>
                            <div class="col-4">
                                <label class="form-label form-label-sm">Unit</label>
                                <select name="unit" id="edit_unit" class="form-select form-select-sm">
                                    @foreach($drugUnits as $du)
                                        <option value="{{ $du }}">{{ $du }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-8">
                                <label class="form-label form-label-sm">Frequency</label>
                                <select name="frequency" id="edit_frequency" class="form-select form-select-sm">
                                    @foreach($drugFreqs as $df)
                                        <option value="{{ $df }}">{{ $df }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label form-label-sm">Duration</label>
                                <div class="dur-group input-group input-group-sm" id="edit-dur-group">
                                    <input type="text" inputmode="numeric" class="form-control form-control-sm dur-qty" value="30" style="max-width:58px;">
                                    <select class="form-select form-select-sm dur-unit">
                                        <option value="days">days</option>
                                        <option value="weeks">weeks</option>
                                        <option value="months">months</option>
                                    </select>
                                    <input type="hidden" name="duration" value="30 days">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-success" id="drug-edit-save-btn">
                        <i class="bi bi-check-lg me-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── End Visit button ──────────────────────────────────────────────── --}}
    @if($visit->status !== 'visited')
        <div class="d-flex justify-content-end mb-4">
            <form id="end-visit-form" method="POST"
                  action="{{ route('clinical.doctor.end-visit', [$unitView->id, $visit->id]) }}">
                @csrf
                {{-- Populated by JS before submit; carries all current notes data --}}
                <input type="hidden" name="notes_json" id="end-visit-notes-json">
                <button type="button" class="btn btn-success btn-lg px-5" id="end-visit-btn">
                    <i class="bi bi-check-circle-fill me-2"></i>End Visit
                </button>
            </form>
        </div>
    @else
        <div class="d-flex justify-content-end mb-4">
            <span class="badge bg-success fs-6 py-2 px-4">
                <i class="bi bi-check-circle-fill me-1"></i>Visit Completed
            </span>
        </div>
    @endif

</div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Config ──────────────────────────────────────────────────────────────
    const CSRF             = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const SAVE_NOTES_URL   = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";
    const TERM_SEARCH_URL  = "{{ route('terminology.search') }}";

    // ── Helpers ─────────────────────────────────────────────────────────────
    function debounce(fn, ms) {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    }

    async function apiPost(url, body) {
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        return r.json();
    }

    async function apiPatch(url, body, keepalive = false) {
        // Send as POST + X-HTTP-Method-Override so Laravel routes PATCH correctly
        // while XAMPP/Apache can always read the POST body via php://input.
        const r = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PATCH',
            },
            body: JSON.stringify(body),
            keepalive,
        });
        return r;
    }

    async function apiDelete(url) {
        const r = await fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        if (!r.ok) {
            const text = await r.text();
            throw new Error(`HTTP ${r.status}: ${text.slice(0, 120)}`);
        }
        return r.json();
    }

    function showDropdown(dd, items, onSelect) {
        dd.innerHTML = '';
        if (!items.length) { dd.style.display = 'none'; return; }
        items.forEach(text => {
            const el = document.createElement('div');
            el.className = 'dd-item';
            el.textContent = text;
            el.addEventListener('mousedown', e => { e.preventDefault(); onSelect(text); });
            dd.appendChild(el);
        });
        dd.style.display = 'block';
    }

    function hideDropdown(dd) { dd.style.display = 'none'; }

    // ── TagInput class ───────────────────────────────────────────────────────
    class TagInput {
        constructor(inputEl, tagsEl, dropdownEl, { category, onChange, initial = [] }) {
            this.input    = inputEl;
            this.tagsEl   = tagsEl;
            this.dropdown = dropdownEl;
            this.category = category;
            this.onChange = onChange;
            this.tags     = [];

            initial.forEach(t => this._renderTag(t));

            const doSearch = debounce(async q => {
                if (q.length < 1) { hideDropdown(this.dropdown); return; }
                const url = `${TERM_SEARCH_URL}?category=${encodeURIComponent(this.category)}&q=${encodeURIComponent(q)}`;
                const data = await fetch(url, { headers: { Accept: 'application/json' } }).then(r => r.json());
                const terms = Array.isArray(data) ? data.map(d => d.term ?? d) : [];
                showDropdown(this.dropdown, terms, t => { this.addTag(t); this.input.value = ''; hideDropdown(this.dropdown); });
            }, 300);

            this.input.addEventListener('input', () => doSearch(this.input.value.trim()));
            this.input.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const v = this.input.value.trim();
                    if (v) { this.addTag(v); this.input.value = ''; hideDropdown(this.dropdown); }
                }
                if (e.key === 'Escape') hideDropdown(this.dropdown);
            });
            this.input.addEventListener('blur', () => setTimeout(() => hideDropdown(this.dropdown), 150));
        }

        addTag(text) {
            if (!text || this.tags.includes(text)) return;
            this.tags.push(text);
            this._renderTag(text);
            this.onChange(this.tags);
        }

        removeTag(text) {
            this.tags = this.tags.filter(t => t !== text);
            this.onChange(this.tags);
        }

        getTags() { return [...this.tags]; }

        _renderTag(text) {
            this.tags.includes(text) || this.tags.push(text);
            const pill = document.createElement('span');
            pill.className = 'tag-pill';
            pill.innerHTML = `${this._escape(text)} <span class="remove-tag">&times;</span>`;
            pill.querySelector('.remove-tag').addEventListener('click', () => {
                pill.remove();
                this.removeTag(text);
            });
            this.tagsEl.appendChild(pill);
        }

        _escape(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    }

})(); // end notes/history/exam IIFE
</script>

{{-- ── Allergy IIFE (isolated to prevent cross-contamination from other JS) ── --}}
<script>
(function () {
    'use strict';

    const CSRF            = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const ADD_ALLERGY_URL = "{{ route('clinical.doctor.allergy.add', [$unitView->id, $visit->patient_id]) }}";
    const DEL_ALLERGY_BASE = "{{ url('clinical/' . $unitView->id . '/patient/' . $visit->patient_id . '/allergy') }}";
    const DRUG_SEARCH_URL = "{{ route('drugs.search') }}";

    const allergyInput = document.getElementById('allergy-input');
    const allergyAddBtn = document.getElementById('allergy-add-btn');
    const allergyTags   = document.getElementById('allergy-tags');
    const allergyDd     = document.getElementById('allergy-dropdown');

    if (!allergyInput || !allergyAddBtn || !allergyTags || !allergyDd) return;

    function debounce(fn, ms) {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    }

    function showDropdown(dd, items, onSelect) {
        dd.innerHTML = '';
        if (!items.length) { dd.style.display = 'none'; return; }
        items.forEach(text => {
            const el = document.createElement('div');
            el.className = 'dd-item';
            el.textContent = text;
            el.addEventListener('mousedown', e => { e.preventDefault(); onSelect(text); });
            dd.appendChild(el);
        });
        dd.style.display = 'block';
    }

    function hideDropdown(dd) { dd.style.display = 'none'; }

    async function apiPost(url, body) {
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        return r.json();
    }

    async function apiDelete(url) {
        const r = await fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        if (!r.ok) {
            const text = await r.text();
            throw new Error(`HTTP ${r.status}: ${text.slice(0, 120)}`);
        }
        return r.json();
    }

    function allergyTagExists(name) {
        return [...allergyTags.querySelectorAll('.tag-pill')]
            .some(p => p.dataset.allergen === name);
    }

    function renderAllergyTag(allergen, id) {
        const empty = allergyTags.querySelector('#no-allergies-msg');
        if (empty) empty.remove();

        const pill = document.createElement('span');
        pill.className = 'tag-pill allergy-pill';
        pill.dataset.allergen = allergen;
        pill.innerHTML = `${allergen.replace(/&/g,'&amp;').replace(/</g,'&lt;')} <span class="remove-tag">&times;</span>`;
        pill.querySelector('.remove-tag').addEventListener('click', () => deleteAllergy(id, pill));
        allergyTags.appendChild(pill);
    }

    function addAllergyFromInput(allergen) {
        allergen = allergen.trim();
        if (!allergen || allergyTagExists(allergen)) return;

        allergyInput.value = '';
        hideDropdown(allergyDd);

        confirmDialog({
            title: 'Add Allergy',
            body: `Add "${allergen}" to the allergy list?`,
            confirmText: 'Add',
            confirmClass: 'btn-primary',
            icon: 'bi-plus-circle-fill text-primary',
        }, async () => {
            try {
                const res = await apiPost(ADD_ALLERGY_URL, { allergen });
                if (res.ok) renderAllergyTag(allergen, res.id);
            } catch (e) { console.error('Add allergy failed', e); }
        });
    }

    function deleteAllergy(id, pill) {
        const allergen = pill.dataset.allergen ?? 'this allergy';
        confirmDialog({
            title: 'Remove Allergy',
            body: `Remove "${allergen}" from the allergy list?`,
            confirmText: 'Remove',
            confirmClass: 'btn-danger',
            icon: 'bi-trash3-fill text-danger',
        }, async () => {
            try {
                await apiDelete(`${DEL_ALLERGY_BASE}/${id}`);
                pill.remove();
                if (!allergyTags.querySelector('.tag-pill')) {
                    allergyTags.innerHTML = '<span id="no-allergies-msg" class="text-muted small">No known allergies recorded</span>';
                }
            } catch (e) {
                console.error('Delete allergy failed', e);
                alert('Could not remove allergy. Please try again.');
            }
        });
    }

    // Wire up existing allergy remove buttons (server-rendered)
    allergyTags.querySelectorAll('.tag-pill').forEach(pill => {
        const btn = pill.querySelector('.remove-tag');
        const id  = btn.dataset.allergyId;
        pill.dataset.allergen = pill.childNodes[0].textContent.trim();
        btn.addEventListener('click', () => deleteAllergy(id, pill));
    });

    allergyAddBtn.addEventListener('click', () => addAllergyFromInput(allergyInput.value));
    allergyInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); addAllergyFromInput(allergyInput.value); }
        if (e.key === 'Escape') hideDropdown(allergyDd);
    });
    allergyInput.addEventListener('blur', () => setTimeout(() => hideDropdown(allergyDd), 200));

    // Suggest from admin-managed drug names list
    const doAllergySearch = debounce(async q => {
        if (q.length < 1) { hideDropdown(allergyDd); return; }
        try {
            const url = `${DRUG_SEARCH_URL}?q=${encodeURIComponent(q)}`;
            const data = await fetch(url, { headers: { Accept: 'application/json' } }).then(r => r.json());
            const names = Array.isArray(data) ? data : [];
            showDropdown(allergyDd, names, name => addAllergyFromInput(name));
        } catch (_) { hideDropdown(allergyDd); }
    }, 300);

    allergyInput.addEventListener('input', () => doAllergySearch(allergyInput.value.trim()));

})(); // end allergy IIFE
</script>

{{-- ── Clinic Visit Summary Pagination ────────────────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var PER_PAGE  = 10;
    var accordion = document.getElementById('visitSummaryAccordion');
    if (!accordion) return;

    var items = Array.from(accordion.querySelectorAll('.accordion-item[data-vs-index]'));
    var total = items.length;
    if (total <= PER_PAGE) return; // no pagination needed

    var pagination = document.getElementById('vs-pagination');
    var infoEl     = document.getElementById('vs-pager-info');
    var btnsEl     = document.getElementById('vs-pager-btns');
    if (!pagination || !infoEl || !btnsEl) return;

    pagination.style.removeProperty('display'); // override the display:none!important

    var currentPage = 1;
    var totalPages  = Math.ceil(total / PER_PAGE);

    function renderPage(page) {
        currentPage = page;
        var start = (page - 1) * PER_PAGE;
        var end   = start + PER_PAGE;

        items.forEach(function (item, i) {
            item.style.display = (i >= start && i < end) ? '' : 'none';
        });

        // Collapse all open panels on page change to avoid stale open state
        accordion.querySelectorAll('.accordion-collapse.show').forEach(function (panel) {
            panel.classList.remove('show');
        });
        accordion.querySelectorAll('.accordion-button:not(.collapsed)').forEach(function (btn) {
            btn.classList.add('collapsed');
            btn.setAttribute('aria-expanded', 'false');
        });
        // Open the first visible item on the new page
        var firstVisible = items[start];
        if (firstVisible) {
            var btn    = firstVisible.querySelector('.accordion-button');
            var panel  = firstVisible.querySelector('.accordion-collapse');
            if (btn && panel) {
                btn.classList.remove('collapsed');
                btn.setAttribute('aria-expanded', 'true');
                panel.classList.add('show');
            }
        }

        infoEl.textContent = 'Showing ' + (start + 1) + '–' + Math.min(end, total) + ' of ' + total + ' visits';
        renderButtons();
    }

    function renderButtons() {
        btnsEl.innerHTML = '';

        // Prev
        var prev = document.createElement('button');
        prev.className = 'btn btn-sm btn-outline-secondary py-0 px-2';
        prev.disabled  = currentPage === 1;
        prev.innerHTML = '<i class="bi bi-chevron-left" style="font-size:.7rem;"></i>';
        prev.addEventListener('click', function () { if (currentPage > 1) renderPage(currentPage - 1); });
        btnsEl.appendChild(prev);

        // Page number buttons (show at most 5 around current)
        var startBtn = Math.max(1, currentPage - 2);
        var endBtn   = Math.min(totalPages, startBtn + 4);
        startBtn     = Math.max(1, endBtn - 4);

        for (var p = startBtn; p <= endBtn; p++) {
            (function (pageNum) {
                var btn = document.createElement('button');
                btn.className = 'btn btn-sm py-0 px-2 ' + (pageNum === currentPage ? 'btn-primary' : 'btn-outline-secondary');
                btn.textContent = pageNum;
                btn.addEventListener('click', function () { renderPage(pageNum); });
                btnsEl.appendChild(btn);
            })(p);
        }

        // Next
        var next = document.createElement('button');
        next.className = 'btn btn-sm btn-outline-secondary py-0 px-2';
        next.disabled  = currentPage === totalPages;
        next.innerHTML = '<i class="bi bi-chevron-right" style="font-size:.7rem;"></i>';
        next.addEventListener('click', function () { if (currentPage < totalPages) renderPage(currentPage + 1); });
        btnsEl.appendChild(next);
    }

    renderPage(1);
});
</script>

@if($allBpReadings->isNotEmpty())
<script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
<script>
(function () {
    var readings = @json($bpChartData);

    // Draw chart
    var ctx = document.getElementById('bp-chart');
    if (ctx) {
        // Points from previous visits are shown slightly smaller / muted
        var pointColors = readings.map(function (r) {
            return (r.source === 'doctor' || r.source === 'admission')
                ? 'rgb(220,53,69)' : 'rgba(220,53,69,.4)';
        });
        var pointColorsDia = readings.map(function (r) {
            return (r.source === 'doctor' || r.source === 'admission')
                ? 'rgb(13,110,253)' : 'rgba(13,110,253,.4)';
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: readings.map(function (r) { return r.label; }),
                datasets: [
                    {
                        label: 'Systolic',
                        data: readings.map(function (r) { return r.systolic; }),
                        borderColor: 'rgb(220,53,69)',
                        backgroundColor: 'rgba(220,53,69,.08)',
                        pointBackgroundColor: pointColors,
                        pointRadius: 5,
                        tension: 0.3,
                        fill: false,
                    },
                    {
                        label: 'Diastolic',
                        data: readings.map(function (r) { return r.diastolic; }),
                        borderColor: 'rgb(13,110,253)',
                        backgroundColor: 'rgba(13,110,253,.08)',
                        pointBackgroundColor: pointColorsDia,
                        pointRadius: 5,
                        tension: 0.3,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 14 } },
                    tooltip: {
                        callbacks: {
                            title: function (items) {
                                var r = readings[items[0].dataIndex];
                                return r.sublabel ? r.sublabel + '  (' + r.label + ')' : r.label;
                            },
                            label: function (item) {
                                return item.dataset.label + ': ' + item.raw + ' mmHg';
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        min: 40, max: 220,
                        title: { display: true, text: 'mmHg', font: { size: 11 } },
                    },
                },
            },
        });
    }

    // Chart ↔ Table toggle
    document.getElementById('bp-chart-btn').onclick = function () {
        document.getElementById('bp-chart-view').style.display = '';
        document.getElementById('bp-table-view').style.display = 'none';
        this.classList.add('active');
        document.getElementById('bp-table-btn').classList.remove('active');
    };
    document.getElementById('bp-table-btn').onclick = function () {
        document.getElementById('bp-table-view').style.display = '';
        document.getElementById('bp-chart-view').style.display = 'none';
        this.classList.add('active');
        document.getElementById('bp-chart-btn').classList.remove('active');
    };
})();
</script>
@else
<script>
    // No readings yet — wire up toggle buttons (chart view shows empty state)
    document.getElementById('bp-chart-btn').onclick = function () {
        document.getElementById('bp-chart-view').style.display = '';
        document.getElementById('bp-table-view').style.display = 'none';
        this.classList.add('active');
        document.getElementById('bp-table-btn').classList.remove('active');
    };
    document.getElementById('bp-table-btn').onclick = function () {
        document.getElementById('bp-table-view').style.display = '';
        document.getElementById('bp-chart-view').style.display = 'none';
        this.classList.add('active');
        document.getElementById('bp-chart-btn').classList.remove('active');
    };
</script>
@endif

{{-- ── Investigation Charts + Add/Delete JS ──────────────────────────────── --}}
<script>
(function () {
    'use strict';

    // ── Helpers ──────────────────────────────────────────────────────────────
    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function invSlug(name) {
        return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }
    function hexToRgba(hex, a) {
        const r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
        return `rgba(${r},${g},${b},${a})`;
    }

    // ── Per-type color palette ────────────────────────────────────────────────
    const INV_COLORS = {
        'fbs':               '#3b82f6',
        'hba1c':             '#ef4444',
        'serum-creatinine':  '#8b5cf6',
        'total-cholesterol': '#f59e0b',
        'tsh':               '#10b981',
    };
    function colorFor(sk) { return INV_COLORS[sk] || '#6b7280'; }

    // ── In-memory data store (initialized from server) ────────────────────────
    const INV_HISTORY = @json($allInvData);   // { "FBS": [{id,value,recorded_at,visit_label,current,deletable},...] }
    const invCharts   = {};                    // sk → { chart, readings }

    // ── Initialize charts for all groups rendered by PHP ─────────────────────
    Object.entries(INV_HISTORY).forEach(([name, readings]) => {
        const sk = invSlug(name);
        initInvChart(sk, name, readings);
    });

    // ── Bind toggle buttons for PHP-rendered groups ───────────────────────────
    document.querySelectorAll('.inv-view-btn').forEach(bindToggle);

    // ── Chart factory ─────────────────────────────────────────────────────────
    function initInvChart(sk, name, readings) {
        const canvas = document.getElementById('inv-chart-' + sk);
        if (!canvas) return;
        const color = colorFor(sk);

        const labels   = readings.map(r => r.recorded_at);
        const values   = readings.map(r => parseFloat(r.value));
        const ptColors = readings.map(r => r.current ? '#ef4444' : color);
        const ptSizes  = readings.map(r => r.current ? 7 : 5);

        const chart = new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: name,
                    data: values,
                    borderColor: color,
                    backgroundColor: hexToRgba(color, 0.08),
                    pointBackgroundColor: ptColors,
                    pointRadius: ptSizes,
                    pointHoverRadius: 7,
                    tension: 0.35,
                    fill: true,
                }],
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const r = readings[ctx.dataIndex];
                                return r ? `${r.value}  •  ${r.visit_label}` : ctx.formattedValue;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: { display: true, text: name, font: { size: 10 } },
                    },
                    x: { ticks: { maxRotation: 45, font: { size: 10 } } },
                },
            },
        });

        invCharts[sk] = { chart, readings };
    }

    // ── Chart/table toggle binding ─────────────────────────────────────────────
    function bindToggle(btn) {
        btn.addEventListener('click', function () {
            const sk   = this.dataset.group;
            const view = this.dataset.view;
            document.getElementById('inv-chart-view-' + sk).style.display = view === 'chart' ? '' : 'none';
            document.getElementById('inv-table-view-' + sk).style.display = view === 'table' ? '' : 'none';
            document.querySelectorAll(`.inv-view-btn[data-group="${sk}"]`)
                .forEach(b => b.classList.toggle('active', b === this));
        });
    }

    // ── Create a new group (when a new inv type is added via AJAX) ─────────────
    function createInvGroup(name, firstReading) {
        const sk = invSlug(name);
        const container = document.getElementById('inv-container');
        const hasBorder = container.querySelector('.inv-group') ? 'border-top pt-3' : '';

        const group = document.createElement('div');
        group.className = `inv-group mb-4 ${hasBorder}`;
        group.id        = 'inv-group-' + sk;
        group.dataset.invName  = name;
        group.dataset.safeKey  = sk;
        group.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-semibold small">${escHtml(name)}</span>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary active inv-view-btn"
                            data-group="${sk}" data-view="chart">
                        <i class="bi bi-graph-up"></i> Chart
                    </button>
                    <button type="button" class="btn btn-outline-secondary inv-view-btn"
                            data-group="${sk}" data-view="table">
                        <i class="bi bi-table"></i> Table
                    </button>
                </div>
            </div>
            <div id="inv-chart-view-${sk}">
                <canvas id="inv-chart-${sk}" height="120"></canvas>
            </div>
            <div id="inv-table-view-${sk}" style="display:none;">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr><th>Value</th><th>Date &amp; Time</th><th>Visit</th><th></th></tr>
                    </thead>
                    <tbody id="inv-tbody-${sk}"></tbody>
                </table>
            </div>`;
        container.appendChild(group);
        group.querySelectorAll('.inv-view-btn').forEach(bindToggle);
        initInvChart(sk, name, [firstReading]);
    }

    // ── Append a row to a group's table ──────────────────────────────────────
    function appendInvRow(sk, reading, deleteUrl) {
        const tbody = document.getElementById('inv-tbody-' + sk);
        if (!tbody) return;
        const tr = document.createElement('tr');
        tr.id = 'inv-row-' + reading.id;
        tr.className = 'table-primary bg-opacity-10';
        tr.dataset.invId     = reading.id;
        tr.dataset.value     = reading.value;
        tr.dataset.recordedAt = reading.recorded_at;
        tr.innerHTML = `
            <td class="fw-semibold">${escHtml(reading.value)}</td>
            <td class="small">${escHtml(reading.recorded_at)}</td>
            <td class="small text-muted">This Visit</td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 inv-delete-btn"
                        data-id="${reading.id}" data-url="${deleteUrl}" data-group="${sk}">
                    <i class="bi bi-trash3" style="font-size:.7rem;"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
    }

    @if($visit->status !== 'visited')
    // ── Add investigation ─────────────────────────────────────────────────────
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const STORE_URL = "{{ route('clinical.doctor.investigation.store', [$unitView->id, $visit->id]) }}";
    const DEL_BASE  = "{{ url('clinical/' . $unitView->id . '/investigation') }}";
    const form      = document.getElementById('inv-form');
    const emptyEl   = document.getElementById('inv-empty');
    const container = document.getElementById('inv-container');

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const fd   = new FormData(form);
            const body = Object.fromEntries(fd.entries());
            try {
                const r    = await fetch(STORE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify(body),
                });
                const data = await r.json();
                if (!data.ok) return;

                const inv = data.investigation;
                const sk  = invSlug(inv.name);
                const reading = { id: inv.id, value: inv.value, recorded_at: inv.recorded_at,
                                  visit_label: 'This Visit', current: true, deletable: true };
                const deleteUrl = DEL_BASE + '/' + inv.id;

                // Hide empty state
                if (emptyEl) emptyEl.classList.add('d-none');
                container.classList.remove('d-none');

                if (!invCharts[sk]) {
                    // New investigation type — create the whole group
                    createInvGroup(inv.name, reading);
                } else {
                    // Existing group — push to chart
                    const ci = invCharts[sk];
                    ci.readings.push(reading);
                    ci.chart.data.labels.push(reading.recorded_at);
                    ci.chart.data.datasets[0].data.push(parseFloat(reading.value));
                    ci.chart.data.datasets[0].pointBackgroundColor.push('#ef4444');
                    ci.chart.data.datasets[0].pointRadius.push(7);
                    ci.chart.update();
                    appendInvRow(sk, reading, deleteUrl);
                }

                form.reset();
                const n = new Date(), p = x => String(x).padStart(2,'0');
                document.getElementById('inv_date').value = `${n.getFullYear()}-${p(n.getMonth()+1)}-${p(n.getDate())}`;
                document.getElementById('inv_time').value = `${p(n.getHours())}:${p(n.getMinutes())}`;
            } catch (err) { console.error(err); }
        });
    }

    // ── Delete investigation (event delegation on whole page) ─────────────────
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.inv-delete-btn');
        if (!btn) return;
        if (!confirm('Delete this investigation result?')) return;

        const id  = parseInt(btn.dataset.id);
        const sk  = btn.dataset.group;
        const url = btn.dataset.url;
        try {
            const r    = await fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ _method: 'DELETE' }),
            });
            const data = await r.json();
            if (!data.ok) return;

            // Remove DOM row
            const row = document.getElementById('inv-row-' + id);
            if (row) row.remove();

            // Rebuild chart from remaining readings in memory
            const ci = invCharts[sk];
            if (ci) {
                ci.readings = ci.readings.filter(r => r.id !== id);
                ci.chart.data.labels                            = ci.readings.map(r => r.recorded_at);
                ci.chart.data.datasets[0].data                  = ci.readings.map(r => parseFloat(r.value));
                ci.chart.data.datasets[0].pointBackgroundColor  = ci.readings.map(r => r.current ? '#ef4444' : colorFor(sk));
                ci.chart.data.datasets[0].pointRadius           = ci.readings.map(r => r.current ? 7 : 5);
                ci.chart.update();

                // If no readings remain, remove the entire group
                if (ci.readings.length === 0) {
                    const group = document.getElementById('inv-group-' + sk);
                    if (group) group.remove();
                    delete invCharts[sk];
                }
            }

            // Show empty state if no groups remain
            if (!document.querySelector('#inv-container .inv-group')) {
                container.classList.add('d-none');
                if (emptyEl) emptyEl.classList.remove('d-none');
            }
        } catch (err) { console.error(err); }
    });
    @endif

})();
</script>

{{-- ── Drug Chart JS ─────────────────────────────────────────────────────── --}}
{{-- Shared duration-group utilities (used by both Clinic and Management scripts) --}}
<script>
function parseDuration(str) {
    const m = String(str || '').match(/^(\d+)\s*(days?|weeks?|months?)$/i);
    if (m) {
        const raw  = m[2].toLowerCase();
        const unit = raw.startsWith('month') ? 'months' : raw.startsWith('week') ? 'weeks' : 'days';
        return { qty: m[1], unit };
    }
    return { qty: '30', unit: 'days' };
}
function setDurationGroup(groupEl, str) {
    if (!groupEl) return;
    const p = parseDuration(str);
    groupEl.querySelector('.dur-qty').value               = p.qty;
    groupEl.querySelector('.dur-unit').value              = p.unit;
    groupEl.querySelector('input[name="duration"]').value = p.qty + ' ' + p.unit;
}
function wireDurationGroup(groupEl) {
    if (!groupEl) return;
    const qty    = groupEl.querySelector('.dur-qty');
    const unit   = groupEl.querySelector('.dur-unit');
    const hidden = groupEl.querySelector('input[name="duration"]');
    function sync() { hidden.value = qty.value + ' ' + unit.value; }
    qty.addEventListener('input',   sync);
    unit.addEventListener('change', sync);
}
</script>

<script>
(function () {
    'use strict';

    const CSRF            = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const STORE_URL       = "{{ route('clinical.doctor.drug.store', [$unitView->id, $visit->id]) }}";
    const DRUG_SEARCH_URL = "{{ route('drugs.search') }}";
    const AI_DEFAULTS_URL = "{{ route('drug.defaults') }}";

    const addForm      = document.getElementById('drug-add-form');
    const drugTbody    = document.getElementById('drug-tbody');
    const drugTable    = document.getElementById('drug-table');
    const drugEmpty    = document.getElementById('drug-empty');
    const countBadge   = document.getElementById('drug-count-badge');
    const changeBadge  = document.getElementById('change-count-badge');
    const changeEmpty  = document.getElementById('change-empty');
    const changeTbody  = document.getElementById('change-tbody');
    const changeWrap   = document.getElementById('change-table-wrap');

    function escHtml(s) {
        return String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function incBadge(el) {
        el.textContent = String(parseInt(el.textContent || '0') + 1);
    }
    function decBadge(el) {
        el.textContent = String(Math.max(0, parseInt(el.textContent || '0') - 1));
    }

    // ── AI autofill flash animation ──────────────────────────────────────────
    function flashField(el) {
        el.classList.remove('ai-filled');
        void el.offsetWidth; // reflow
        el.classList.add('ai-filled');
    }

    // ── Fetch AI defaults and fill form fields ───────────────────────────────
    async function applyAiDefaults(drugName, fields, spinner, hint) {
        spinner.style.display = 'inline';
        if (hint) hint.style.display = 'none';
        try {
            const url  = `${AI_DEFAULTS_URL}?name=${encodeURIComponent(drugName)}`;
            const data = await fetch(url, { headers: { Accept: 'application/json' } }).then(r => r.json());
            if (data.error) return;

            if (data.type      && fields.type)          { fields.type.value      = data.type;      flashField(fields.type); }
            if (data.dose      && fields.dose)          { fields.dose.value      = data.dose;      flashField(fields.dose); }
            if (data.unit      && fields.unit)          { fields.unit.value      = data.unit;      flashField(fields.unit); }
            if (data.frequency && fields.frequency)     { fields.frequency.value = data.frequency; flashField(fields.frequency); }
            if (data.duration  && fields.durationGroup) { setDurationGroup(fields.durationGroup, data.duration); flashField(fields.durationGroup.querySelector('.dur-qty')); }

            if (hint && (data.dose || data.unit || data.frequency)) hint.style.display = 'block';
        } catch (_) {
            // silently ignore — user fills manually
        } finally {
            spinner.style.display = 'none';
        }
    }

    // ── Drug name autocomplete + AI trigger (shared for add & edit) ──────────
    function wireNameAutocomplete(input, dropdown, spinner, hint, fields) {
        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = this.value.trim();
            if (hint) hint.style.display = 'none';
            if (q.length < 1) { dropdown.style.display = 'none'; dropdown.innerHTML = ''; return; }
            timer = setTimeout(async () => {
                try {
                    const url   = `${DRUG_SEARCH_URL}?q=${encodeURIComponent(q)}`;
                    const items = await fetch(url, { headers: { Accept: 'application/json' } }).then(r => r.json());
                    if (!items.length) { dropdown.style.display = 'none'; return; }
                    dropdown.innerHTML = items.map(t =>
                        `<div class="dd-item" data-val="${escHtml(t)}">${escHtml(t)}</div>`
                    ).join('');
                    dropdown.style.display = 'block';
                } catch (_) { dropdown.style.display = 'none'; }
            }, 250);
        });

        // On selection — fill name then call AI
        dropdown.addEventListener('mousedown', function (e) {
            const item = e.target.closest('.dd-item');
            if (!item) return;
            e.preventDefault();
            input.value = item.dataset.val;
            dropdown.style.display = 'none';
            // Trigger AI defaults
            applyAiDefaults(item.dataset.val, fields, spinner, hint);
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#' + input.id) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    wireDurationGroup(document.getElementById('drug-dur-group'));
    wireDurationGroup(document.getElementById('edit-dur-group'));

    const addNameInput = document.getElementById('drug_name');
    const addNameDd    = document.getElementById('drug-name-dropdown');
    const addSpinner   = document.getElementById('drug-ai-spinner');
    if (addNameInput) {
        wireNameAutocomplete(addNameInput, addNameDd, addSpinner, null, {
            type:          document.getElementById('drug_type'),
            dose:          document.getElementById('drug_dose'),
            unit:          document.getElementById('drug_unit'),
            frequency:     document.getElementById('drug_frequency'),
            durationGroup: document.getElementById('drug-dur-group'),
        });
    }

    const editNameInput = document.getElementById('edit_name');
    const editNameDd    = document.getElementById('edit-drug-name-dropdown');
    const editSpinner   = document.getElementById('edit-drug-ai-spinner');
    if (editNameInput) {
        wireNameAutocomplete(editNameInput, editNameDd, editSpinner, null, {
            type:          document.getElementById('edit_type'),
            dose:          document.getElementById('edit_dose'),
            unit:          document.getElementById('edit_unit'),
            frequency:     document.getElementById('edit_frequency'),
            durationGroup: document.getElementById('edit-dur-group'),
        });
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    function buildActionCell(id, updateUrl, deleteUrl, duration) {
        return `<td class="text-end text-nowrap">
            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 drug-edit-btn"
                    data-id="${id}" data-url="${escHtml(updateUrl)}" data-duration="${escHtml(duration || '30 days')}">
                <i class="bi bi-pencil" style="font-size:.7rem;"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 ms-1 drug-del-btn"
                    data-id="${id}" data-url="${escHtml(deleteUrl)}">
                <i class="bi bi-trash3" style="font-size:.7rem;"></i>
            </button>
        </td>`;
    }

    // ── Change list pagination ────────────────────────────────────────────────
    const CHANGE_PER_PAGE = 10;
    let changePage = 1;

    function renderChangePager() {
        if (!changeTbody) return;
        const rows  = Array.from(changeTbody.querySelectorAll('.change-row'));
        const total = Math.max(1, Math.ceil(rows.length / CHANGE_PER_PAGE));
        if (changePage > total) changePage = total;
        const start = (changePage - 1) * CHANGE_PER_PAGE;

        rows.forEach((r, i) => {
            r.style.display = (i >= start && i < start + CHANGE_PER_PAGE) ? '' : 'none';
        });

        const pager    = document.getElementById('change-pager');
        const infoEl   = document.getElementById('change-pager-info');
        const prevBtn  = document.getElementById('change-prev');
        const nextBtn  = document.getElementById('change-next');

        if (pager) {
            pager.style.cssText = total > 1 ? '' : 'display:none!important;';
            if (infoEl)  infoEl.textContent  = `Page ${changePage} of ${total} (${rows.length} entries)`;
            if (prevBtn) prevBtn.disabled = changePage === 1;
            if (nextBtn) nextBtn.disabled = changePage === total;
        }
    }

    const changePrevBtn = document.getElementById('change-prev');
    const changeNextBtn = document.getElementById('change-next');
    if (changePrevBtn) changePrevBtn.addEventListener('click', function () {
        if (changePage > 1) { changePage--; renderChangePager(); }
    });
    if (changeNextBtn) changeNextBtn.addEventListener('click', function () {
        const total = Math.max(1, Math.ceil(changeTbody.querySelectorAll('.change-row').length / CHANGE_PER_PAGE));
        if (changePage < total) { changePage++; renderChangePager(); }
    });

    renderChangePager();

    function prependChange(sentence, action) {
        if (changeEmpty) changeEmpty.classList.add('d-none');
        if (changeWrap)  changeWrap.classList.remove('d-none');

        const badgeMap = { added:'bg-success-subtle text-success border-success-subtle',
                           edited:'bg-warning-subtle text-warning border-warning-subtle',
                           deleted:'bg-danger-subtle text-danger border-danger-subtle' };
        const labelMap = { added:'Added', edited:'Edited', deleted:'D/C' };

        const tr = document.createElement('tr');
        tr.className = 'change-row';
        tr.innerHTML = `
            <td class="align-middle">
                <span class="badge border ${badgeMap[action] ?? ''}">${labelMap[action] ?? action}</span>
            </td>
            <td class="small text-secondary">${escHtml(sentence)}</td>`;

        if (changeTbody) changeTbody.prepend(tr);
        incBadge(changeBadge);
        changePage = 1;
        renderChangePager();
    }

    // ── Add drug ─────────────────────────────────────────────────────────────
    if (addForm) {
        addForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(addForm).entries());
            try {
                const res = await fetch(STORE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, Accept:'application/json' },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (!data.ok) return;

                const d = data.drug;
                const actionCellHtml = buildActionCell(
                    d.id,
                    "{{ url('clinical/' . $unitView->id . '/drug') }}/" + d.id,
                    "{{ url('clinical/' . $unitView->id . '/drug') }}/" + d.id,
                    d.duration
                );

                const tr = document.createElement('tr');
                tr.id = 'drug-row-' + d.id;
                tr.innerHTML = `
                    <td class="drug-cell-type">${escHtml(d.type)}</td>
                    <td class="drug-cell-name">${escHtml(d.name)}</td>
                    <td class="drug-cell-dose">${escHtml(d.dose)}</td>
                    <td class="drug-cell-unit">${escHtml(d.unit)}</td>
                    <td class="drug-cell-freq">${escHtml(d.frequency)}</td>
                    <td class="drug-cell-dur">${escHtml(d.duration || '30 days')}</td>
                    <td><span class="badge bg-success-subtle text-success border border-success-subtle">Available</span></td>
                    ${actionCellHtml}`;
                drugTbody.appendChild(tr);

                drugEmpty.classList.add('d-none');
                drugTable.classList.remove('d-none');
                incBadge(countBadge);
                prependChange(data.drug.change ?? '', 'added');

                addForm.reset();
                document.getElementById('drug_type').value = 'Oral';
                setDurationGroup(document.getElementById('drug-dur-group'), '30 days');
            } catch (err) { console.error(err); }
        });
    }

    // ── Edit drug (open modal) ────────────────────────────────────────────────
    if (drugTbody) {
        drugTbody.addEventListener('click', function (e) {
            const editBtn = e.target.closest('.drug-edit-btn');
            if (editBtn) {
                const row = document.getElementById('drug-row-' + editBtn.dataset.id);
                document.getElementById('edit_drug_id').value  = editBtn.dataset.id;
                document.getElementById('edit_drug_url').value = editBtn.dataset.url;
                document.getElementById('edit_type').value      = row.querySelector('.drug-cell-type').textContent.trim();
                document.getElementById('edit_name').value      = row.querySelector('.drug-cell-name').textContent.trim();
                document.getElementById('edit_dose').value      = row.querySelector('.drug-cell-dose').textContent.trim();
                document.getElementById('edit_unit').value      = row.querySelector('.drug-cell-unit').textContent.trim();
                document.getElementById('edit_frequency').value = row.querySelector('.drug-cell-freq').textContent.trim();
                setDurationGroup(document.getElementById('edit-dur-group'), editBtn.dataset.duration || '30 days');
                new bootstrap.Modal(document.getElementById('drugEditModal')).show();
            }
        });
    }

    // ── Save edit ────────────────────────────────────────────────────────────
    const saveEditBtn = document.getElementById('drug-edit-save-btn');
    if (saveEditBtn) {
        saveEditBtn.addEventListener('click', async function () {
            const form    = document.getElementById('drug-edit-form');
            const url     = document.getElementById('edit_drug_url').value;
            const id      = document.getElementById('edit_drug_id').value;
            const body = Object.fromEntries(new FormData(form).entries());
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PATCH',
                    },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (!data.ok) return;

                const d   = data.drug;
                const row = document.getElementById('drug-row-' + id);
                if (row) {
                    row.querySelector('.drug-cell-type').textContent = d.type;
                    row.querySelector('.drug-cell-name').textContent = d.name;
                    row.querySelector('.drug-cell-dose').textContent = d.dose;
                    row.querySelector('.drug-cell-unit').textContent = d.unit;
                    row.querySelector('.drug-cell-freq').textContent = d.frequency;
                    row.querySelector('.drug-cell-dur').textContent  = d.duration || '30 days';
                    const eb = row.querySelector('.drug-edit-btn');
                    if (eb) eb.dataset.duration = d.duration || '30 days';
                }
                prependChange(data.change, 'edited');
                bootstrap.Modal.getInstance(document.getElementById('drugEditModal')).hide();
            } catch (err) { console.error(err); }
        });
    }

    // ── Delete drug ──────────────────────────────────────────────────────────
    if (drugTbody) {
        drugTbody.addEventListener('click', async function (e) {
            const delBtn = e.target.closest('.drug-del-btn');
            if (!delBtn) return;
            if (!confirm('Remove this drug from the chart?')) return;

            const url = delBtn.dataset.url;
            try {
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN':CSRF, Accept:'application/json',
                               'Content-Type':'application/json' },
                });
                const data = await res.json();
                if (!data.ok) return;

                const row = document.getElementById('drug-row-' + delBtn.dataset.id);
                if (row) row.remove();
                decBadge(countBadge);
                prependChange(data.change, 'deleted');
                if (!drugTbody.querySelector('tr')) {
                    drugTable.classList.add('d-none');
                    drugEmpty.classList.remove('d-none');
                }
            } catch (err) { console.error(err); }
        });
    }


})();
</script>

{{-- ── Management Section JS ──────────────────────────────────────────────── --}}
<script>
(function () {
    'use strict';

    const CSRF              = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const STORE_URL         = "{{ route('clinical.doctor.drug.store', [$unitView->id, $visit->id]) }}";
    const STOCK_CHECK_URL   = "{{ route('clinical.doctor.drug-stock-check', $unitView->id) }}";
    const CLINIC_DRUGS      = {!! json_encode($clinicDrugs->map(fn($d) => ['type'=>$d->type,'name'=>$d->name,'dose'=>$d->dose,'unit'=>$d->unit,'frequency'=>$d->frequency,'duration'=>$d->duration??'30 days'])->values()) !!};
    const PATIENT_ALLERGIES = {!! json_encode($visit->patient->allergies->pluck('allergen')->map(fn($a) => mb_strtolower($a))->values()) !!};
    const DRUG_SEARCH_URL = "{{ route('drugs.search') }}";
    const AI_DEFAULTS_URL = "{{ route('drug.defaults') }}";
    const TERM_SEARCH_URL = "{{ route('terminology.search') }}";
    const SAVE_NOTES_URL  = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function debounce(fn, ms) {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    }

    // ── AI defaults autofill ─────────────────────────────────────────────────
    function flashField(el) {
        el.classList.remove('ai-filled');
        void el.offsetWidth;
        el.classList.add('ai-filled');
    }

    async function applyAiDefaults(drugName, fields, spinner) {
        spinner.style.display = 'inline';
        try {
            const url  = `${AI_DEFAULTS_URL}?name=${encodeURIComponent(drugName)}`;
            const data = await fetch(url, { headers: { Accept: 'application/json' } }).then(r => r.json());
            if (data.error) return;
            if (data.type      && fields.type)          { fields.type.value      = data.type;      flashField(fields.type); }
            if (data.dose      && fields.dose)          { fields.dose.value      = data.dose;      flashField(fields.dose); }
            if (data.unit      && fields.unit)          { fields.unit.value      = data.unit;      flashField(fields.unit); }
            if (data.frequency && fields.frequency)     { fields.frequency.value = data.frequency; flashField(fields.frequency); }
            if (data.duration  && fields.durationGroup) { setDurationGroup(fields.durationGroup, data.duration); flashField(fields.durationGroup.querySelector('.dur-qty')); }
        } catch (_) {} finally {
            spinner.style.display = 'none';
        }
    }

    // ── Drug name autocomplete ────────────────────────────────────────────────
    function wireNameAutocomplete(input, dropdown, spinner, fields) {
        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = this.value.trim();
            if (q.length < 1) { dropdown.style.display = 'none'; dropdown.innerHTML = ''; return; }
            timer = setTimeout(async () => {
                try {
                    const items = await fetch(`${DRUG_SEARCH_URL}?q=${encodeURIComponent(q)}`, { headers: { Accept: 'application/json' } }).then(r => r.json());
                    if (!items.length) { dropdown.style.display = 'none'; return; }
                    dropdown.innerHTML = items.map(t => `<div class="dd-item" data-val="${escHtml(t)}">${escHtml(t)}</div>`).join('');
                    dropdown.style.display = 'block';
                } catch (_) { dropdown.style.display = 'none'; }
            }, 250);
        });
        dropdown.addEventListener('mousedown', function (e) {
            const item = e.target.closest('.dd-item');
            if (!item) return;
            e.preventDefault();
            input.value = item.dataset.val;
            dropdown.style.display = 'none';
            applyAiDefaults(item.dataset.val, fields, spinner);
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#' + input.id) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    // ── Add form ─────────────────────────────────────────────────────────────
    const addForm = document.getElementById('mgmt-drug-add-form');
    const addNameInput  = document.getElementById('mgmt_drug_name');
    const addNameDd     = document.getElementById('mgmt-drug-name-dropdown');
    const addSpinner    = document.getElementById('mgmt-drug-ai-spinner');

    wireDurationGroup(document.getElementById('mgmt-drug-dur-group'));
    wireDurationGroup(document.getElementById('mgmt-edit-dur-group'));

    if (addNameInput) {
        wireNameAutocomplete(addNameInput, addNameDd, addSpinner, {
            type:          document.getElementById('mgmt_drug_type'),
            dose:          document.getElementById('mgmt_drug_dose'),
            unit:          document.getElementById('mgmt_drug_unit'),
            frequency:     document.getElementById('mgmt_drug_frequency'),
            durationGroup: document.getElementById('mgmt-drug-dur-group'),
        });
    }

    const mgmtTbody  = document.getElementById('mgmt-drug-tbody');
    const mgmtTable  = document.getElementById('mgmt-drug-table');
    const mgmtEmpty  = document.getElementById('mgmt-drug-empty');

    // ── Helpers ───────────────────────────────────────────────────────────────
    function availBadge(inStock) {
        return inStock
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:.7rem;"><i class="bi bi-check-circle me-1"></i>Available</span>`
            : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:.7rem;"><i class="bi bi-x-circle me-1"></i>O/S</span>`;
    }

    async function checkStock(drugName) {
        try {
            const r = await fetch(STOCK_CHECK_URL + '?drug=' + encodeURIComponent(drugName), {
                headers: { 'X-CSRF-TOKEN': CSRF, Accept: 'application/json' },
            });
            return await r.json();  // { in_stock, remaining }
        } catch { return { in_stock: false, remaining: 0 }; }
    }

    function isAllergic(drugName) {
        const lower = drugName.toLowerCase();
        return PATIENT_ALLERGIES.some(a => lower.includes(a) || a.includes(lower));
    }

    function buildMgmtActionCell(id, updateUrl, deleteUrl) {
        return `<td class="text-end text-nowrap">
            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 mgmt-drug-edit-btn"
                    data-id="${id}" data-url="${escHtml(updateUrl)}">
                <i class="bi bi-pencil" style="font-size:.7rem;"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 ms-1 mgmt-drug-del-btn"
                    data-id="${id}" data-url="${escHtml(deleteUrl)}">
                <i class="bi bi-trash3" style="font-size:.7rem;"></i>
            </button>
        </td>`;
    }

    async function doAddMgmtDrug(body) {
        const res  = await fetch(STORE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, Accept: 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (!data.ok) return;

        const d   = data.drug;
        const dur = d.duration || '';
        const drugUrl = "{{ url('clinical/' . $unitView->id . '/drug') }}/" + d.id;
        const actionCell = buildMgmtActionCell(d.id, drugUrl, drugUrl);

        // Check stock for the availability cell
        const stock = await checkStock(d.name);

        const tr = document.createElement('tr');
        tr.id = 'mgmt-drug-row-' + d.id;
        tr.innerHTML = `
            <td class="mgmt-cell-type">${escHtml(d.type)}</td>
            <td class="mgmt-cell-name">${escHtml(d.name)}</td>
            <td class="mgmt-cell-dose">${escHtml(d.dose)}</td>
            <td class="mgmt-cell-unit">${escHtml(d.unit)}</td>
            <td class="mgmt-cell-freq">${escHtml(d.frequency)}</td>
            <td class="mgmt-cell-duration">${escHtml(dur)}</td>
            <td class="mgmt-cell-avail">${availBadge(stock.in_stock)}</td>
            ${actionCell}`;
        mgmtTbody.appendChild(tr);
        mgmtEmpty.classList.add('d-none');
        mgmtTable.classList.remove('d-none');
    }

    if (addForm) {
        addForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(addForm).entries());
            body.section = 'management';

            // Allergy check
            if (PATIENT_ALLERGIES.length && isAllergic(body.name)) {
                const allergyModal = new bootstrap.Modal(document.getElementById('allergyWarningModal'));
                document.getElementById('allergy-warning-drug-name').textContent = body.name;
                document.getElementById('allergy-warning-confirm').onclick = async function () {
                    allergyModal.hide();
                    try {
                        await doAddMgmtDrug(body);
                        addForm.reset();
                        document.getElementById('mgmt_drug_type').value = 'Oral';
                        setDurationGroup(document.getElementById('mgmt-drug-dur-group'), '5 days');
                    } catch (err) { console.error(err); }
                };
                allergyModal.show();
                return;
            }

            try {
                await doAddMgmtDrug(body);
                addForm.reset();
                document.getElementById('mgmt_drug_type').value = 'Oral';
                setDurationGroup(document.getElementById('mgmt-drug-dur-group'), '5 days');
            } catch (err) { console.error(err); }
        });
    }

    // ── Load Clinic Drugs ─────────────────────────────────────────────────────
    const loadClinicBtn = document.getElementById('load-clinic-drugs-btn');
    if (loadClinicBtn) {
        loadClinicBtn.addEventListener('click', async function () {
            const clinicDrugs = CLINIC_DRUGS;
            if (!clinicDrugs.length) return;

            // Collect names already in management table to avoid duplicates
            // Use firstChild text only so the "Clinic" badge span doesn't pollute the name
            const existingNames = new Set(
                [...mgmtTbody.querySelectorAll('.mgmt-cell-name')].map(el => {
                    const node = el.firstChild;
                    return (node ? node.textContent : el.textContent).trim().toLowerCase();
                })
            );

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading…';

            let added = 0;
            for (const drug of clinicDrugs) {
                if (existingNames.has(drug.name.toLowerCase())) continue;
                try {
                    const res  = await fetch(STORE_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, Accept: 'application/json' },
                        body: JSON.stringify({ ...drug, section: 'management' }),
                    });
                    const data = await res.json();
                    if (!data.ok) continue;

                    const d      = data.drug;
                    const dur    = d.duration || '';
                    const drugUrl = "{{ url('clinical/' . $unitView->id . '/drug') }}/" + d.id;
                    const actionCell = buildMgmtActionCell(d.id, drugUrl, drugUrl);
                    const stock  = await checkStock(d.name);

                    const tr = document.createElement('tr');
                    tr.id = 'mgmt-drug-row-' + d.id;
                    tr.className = 'clinic-loaded-row';
                    tr.innerHTML = `
                        <td class="mgmt-cell-type">${escHtml(d.type)}</td>
                        <td class="mgmt-cell-name">${escHtml(d.name)}<span class="clinic-loaded-badge">Clinic</span></td>
                        <td class="mgmt-cell-dose">${escHtml(d.dose)}</td>
                        <td class="mgmt-cell-unit">${escHtml(d.unit)}</td>
                        <td class="mgmt-cell-freq">${escHtml(d.frequency)}</td>
                        <td class="mgmt-cell-duration">${escHtml(dur)}</td>
                        <td class="mgmt-cell-avail">${availBadge(stock.in_stock)}</td>
                        ${actionCell}`;
                    mgmtTbody.appendChild(tr);
                    existingNames.add(drug.name.toLowerCase());
                    added++;

                    mgmtEmpty.classList.add('d-none');
                    mgmtTable.classList.remove('d-none');
                } catch (err) { console.error(err); }
            }

            this.disabled = false;
            if (added === 0) {
                this.innerHTML = '<i class="bi bi-check-circle me-1"></i>Already loaded';
            } else {
                this.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Loaded';
                this.classList.replace('btn-outline-info', 'btn-info');
                this.classList.add('text-white');
            }
        });
    }

    // ── Edit (open modal) ─────────────────────────────────────────────────────
    if (mgmtTbody) {
        mgmtTbody.addEventListener('click', function (e) {
            const editBtn = e.target.closest('.mgmt-drug-edit-btn');
            if (!editBtn) return;
            const row = document.getElementById('mgmt-drug-row-' + editBtn.dataset.id);
            document.getElementById('mgmt_edit_drug_id').value   = editBtn.dataset.id;
            document.getElementById('mgmt_edit_drug_url').value  = editBtn.dataset.url;
            document.getElementById('mgmt_edit_type').value      = row.querySelector('.mgmt-cell-type').textContent.trim();
            const nameCell = row.querySelector('.mgmt-cell-name');
            document.getElementById('mgmt_edit_name').value = (nameCell.firstChild ? nameCell.firstChild.textContent : nameCell.textContent).trim();
            document.getElementById('mgmt_edit_dose').value      = row.querySelector('.mgmt-cell-dose').textContent.trim();
            document.getElementById('mgmt_edit_unit').value      = row.querySelector('.mgmt-cell-unit').textContent.trim();
            document.getElementById('mgmt_edit_frequency').value = row.querySelector('.mgmt-cell-freq').textContent.trim();
            setDurationGroup(document.getElementById('mgmt-edit-dur-group'), row.querySelector('.mgmt-cell-duration').textContent.trim());
            new bootstrap.Modal(document.getElementById('mgmtDrugEditModal')).show();
        });
    }

    // Edit modal — drug name autocomplete
    const editNameInput = document.getElementById('mgmt_edit_name');
    const editNameDd    = document.getElementById('mgmt-edit-drug-name-dropdown');
    const editSpinner   = document.getElementById('mgmt-edit-drug-ai-spinner');
    if (editNameInput) {
        wireNameAutocomplete(editNameInput, editNameDd, editSpinner, {
            type:          document.getElementById('mgmt_edit_type'),
            dose:          document.getElementById('mgmt_edit_dose'),
            unit:          document.getElementById('mgmt_edit_unit'),
            frequency:     document.getElementById('mgmt_edit_frequency'),
            durationGroup: document.getElementById('mgmt-edit-dur-group'),
        });
    }

    // ── Save edit ─────────────────────────────────────────────────────────────
    const saveEditBtn = document.getElementById('mgmt-drug-edit-save-btn');
    if (saveEditBtn) {
        saveEditBtn.addEventListener('click', async function () {
            const form = document.getElementById('mgmt-drug-edit-form');
            const url  = document.getElementById('mgmt_edit_drug_url').value;
            const id   = document.getElementById('mgmt_edit_drug_id').value;
            const body = Object.fromEntries(new FormData(form).entries());
            try {
                const res  = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        Accept: 'application/json',
                        'X-HTTP-Method-Override': 'PATCH',
                    },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (!data.ok) return;

                const d   = data.drug;
                const row = document.getElementById('mgmt-drug-row-' + id);
                if (row) {
                    row.querySelector('.mgmt-cell-type').textContent     = d.type;
                    row.querySelector('.mgmt-cell-name').textContent     = d.name;
                    row.querySelector('.mgmt-cell-dose').textContent     = d.dose;
                    row.querySelector('.mgmt-cell-unit').textContent     = d.unit;
                    row.querySelector('.mgmt-cell-freq').textContent     = d.frequency;
                    row.querySelector('.mgmt-cell-duration').textContent = d.duration || '';
                }
                bootstrap.Modal.getInstance(document.getElementById('mgmtDrugEditModal')).hide();
            } catch (err) { console.error(err); }
        });
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    if (mgmtTbody) {
        mgmtTbody.addEventListener('click', async function (e) {
            const delBtn = e.target.closest('.mgmt-drug-del-btn');
            if (!delBtn) return;
            if (!confirm('Remove this drug from management?')) return;
            try {
                const res  = await fetch(delBtn.dataset.url, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, Accept: 'application/json', 'Content-Type': 'application/json' },
                });
                const data = await res.json();
                if (!data.ok) return;
                const row = document.getElementById('mgmt-drug-row-' + delBtn.dataset.id);
                if (row) row.remove();
                if (!mgmtTbody.querySelector('tr')) {
                    mgmtTable.classList.add('d-none');
                    mgmtEmpty.classList.remove('d-none');
                }
            } catch (err) { console.error(err); }
        });
    }

})();
</script>

{{-- ── End Visit: save all notes via AJAX, then submit form ───────────── --}}
<script>
(function () {
    var btn = document.getElementById('end-visit-btn');
    if (!btn) return;

    var CSRF           = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var SAVE_NOTES_URL = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";

    btn.addEventListener('click', function () {
        confirmDialog({
            title: 'End Visit',
            body: 'Close this visit? This will mark the patient as Visited.',
            confirmText: 'End Visit',
            confirmClass: 'btn-success',
            icon: 'bi-check-circle-fill text-success',
        }, function () {
            // Collect all section tags from JS-memory registry (authoritative source,
            // registered by each section's DOMContentLoaded script).
            var data = {};
            var sections = window.__sectionTags || {};
            Object.keys(sections).forEach(function (key) {
                data[key] = sections[key]();
            });

            // Pulse rate is a plain <input>, not a tag section
            var pr = document.getElementById('pulse-rate-input');
            if (pr && pr.value !== '') {
                data.pulse_rate = parseInt(pr.value, 10);
            }

            // Store in hidden field as a server-side fallback
            document.getElementById('end-visit-notes-json').value = JSON.stringify(data);

            // First: save all notes in a single AJAX call (no beforeunload race)
            // Then: clear localStorage for this visit and submit the form
            var doSubmit = function () {
                try {
                    var prefix = 'phims_v{{ $visit->id }}_';
                    var toRemove = [];
                    for (var i = 0; i < localStorage.length; i++) {
                        var k = localStorage.key(i);
                        if (k && k.indexOf(prefix) === 0) toRemove.push(k);
                    }
                    toRemove.forEach(function (k) { localStorage.removeItem(k); });
                } catch(e) {}
                document.getElementById('end-visit-form').submit();
            };

            fetch(SAVE_NOTES_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            }).then(doSubmit).catch(doSubmit);
        });
    });
})();
</script>

{{-- ── Management Instruction Tag Input ─────────────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var CSRF           = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var SAVE_URL       = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";
    var TERM_URL       = "{{ route('terminology.search') }}";

    var wrap     = document.querySelector('.instr-tag-section');
    if (!wrap) return;

    var input    = wrap.querySelector('.instr-input');
    var tagsEl   = wrap.querySelector('.instr-tags');
    var dropdown = wrap.querySelector('.instr-dropdown');
    var saved    = document.getElementById('mgmt-instruction-saved');

    if (!input || !tagsEl || !dropdown) return;

    // ── Load existing tags ───────────────────────────────────────────────────
    var VISIT_LS_KEY = 'phims_v{{ $visit->id }}_management_instruction';
    var tags = [];
    var lsRaw = null;
    try { lsRaw = localStorage.getItem(VISIT_LS_KEY); } catch(e) {}
    var initial = [];
    if (lsRaw !== null) {
        try { initial = JSON.parse(lsRaw); } catch(e) {}
    } else {
        try { initial = JSON.parse(tagsEl.dataset.initial || '[]'); } catch(e) {}
    }
    initial.forEach(function(t) { renderPill(t); tags.push(t); });
    saveLocal(); // seed localStorage with initial state

    // Register getter so End Visit can read current tags without DOM scraping
    window.__sectionTags = window.__sectionTags || {};
    window.__sectionTags['management_instruction'] = function() { return tags.slice(); };

    // ── Helpers ──────────────────────────────────────────────────────────────
    function addTagPill(text) {
        if (!text || tags.indexOf(text) !== -1) return;
        tags.push(text);
        renderPill(text);
    }

    function renderPill(text) {
        var pill = document.createElement('span');
        pill.className = 'tag-pill';
        pill.innerHTML = escH(text) + ' <span class="remove-tag" style="cursor:pointer;">&times;</span>';
        pill.querySelector('.remove-tag').addEventListener('click', function () {
            pill.remove();
            tags = tags.filter(function(t){ return t !== text; });
            save();
        });
        tagsEl.appendChild(pill);
    }

    function escH(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function showSavedIndicator() {
        if (!saved) return;
        saved.classList.add('show');
        setTimeout(function(){ saved.classList.remove('show'); }, 2000);
    }

    function saveLocal() {
        try { localStorage.setItem(VISIT_LS_KEY, JSON.stringify(tags)); } catch(e) {}
    }

    function save(keepalive) {
        saveLocal();
        fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ management_instruction: tags }),
            keepalive: !!keepalive,
        })
        .then(function(r){ if (r.ok) showSavedIndicator(); })
        .catch(function(){});
    }

    window.addEventListener('beforeunload', function () { try { save(true); } catch(e) {} });

    function showDd(items) {
        dropdown.innerHTML = '';
        if (!items.length) { dropdown.style.display = 'none'; return; }
        items.forEach(function(term) {
            var el = document.createElement('div');
            el.className = 'dd-item';
            el.textContent = term;
            el.addEventListener('mousedown', function(e) {
                e.preventDefault();
                input.value = '';
                dropdown.style.display = 'none';
                addTagPill(term);
                save();
            });
            dropdown.appendChild(el);
        });
        dropdown.style.display = 'block';
    }

    function hideDd() { dropdown.style.display = 'none'; }

    // ── Input events ─────────────────────────────────────────────────────────
    var searchTimer;
    input.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = input.value.trim();
        if (!q) { hideDd(); return; }
        searchTimer = setTimeout(function() {
            fetch(TERM_URL + '?category=general_instructions&q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r){ return r.json(); })
            .then(function(data){ showDd(Array.isArray(data) ? data : []); })
            .catch(function(){ hideDd(); });
        }, 300);
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var v = input.value.trim();
            if (v) {
                input.value = '';
                hideDd();
                addTagPill(v);
                save();
            }
        }
        if (e.key === 'Escape') hideDd();
    });

    input.addEventListener('blur', function() {
        setTimeout(hideDd, 150);
    });
});
</script>

{{-- ── Presenting Complaints (combined: complaint × duration) ── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var CSRF     = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var SAVE_URL = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";
    var TERM_URL = "{{ route('terminology.search') }}";
    var FIELD    = 'presenting_complaints';
    var CATEGORY = 'presenting_complaints';
    var VISIT_LS_KEY = 'phims_v{{ $visit->id }}_' + FIELD;

    var complaintInput  = document.getElementById('pc-complaint-input');
    var durationInput   = document.getElementById('pc-duration-input');
    var addBtn          = document.getElementById('pc-add-btn');
    var tagsEl          = document.querySelector('#sec-presenting_complaints .history-tags');
    var dropdown        = document.getElementById('pc-complaint-dropdown');
    var durationDropdown = document.getElementById('pc-duration-dropdown');
    if (!complaintInput || !tagsEl || !addBtn) return;

    var tags = [];
    var lsRaw = null;
    try { lsRaw = localStorage.getItem(VISIT_LS_KEY); } catch(e) {}
    var initial = [];
    if (lsRaw !== null) {
        try { initial = JSON.parse(lsRaw); } catch(e) {}
    } else {
        try { initial = JSON.parse(tagsEl.dataset.initial || '[]'); } catch(e) {}
    }
    initial.forEach(function(t) { renderPill(t); tags.push(t); });
    saveLocal();

    window.__sectionTags = window.__sectionTags || {};
    window.__sectionTags[FIELD] = function() { return tags.slice(); };

    function combineAndAdd() {
        var complaint = complaintInput.value.trim();
        var duration  = durationInput ? durationInput.value.trim() : '';
        if (!complaint) return;
        var tag = duration ? (complaint + ' x ' + duration) : complaint;
        if (tags.indexOf(tag) === -1) {
            tags.push(tag);
            renderPill(tag);
            save();
        }
        complaintInput.value = '';
        if (durationInput) durationInput.value = '';
        if (dropdown) dropdown.style.display = 'none';
        complaintInput.focus();
    }

    addBtn.addEventListener('click', combineAndAdd);
    complaintInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); combineAndAdd(); }
        if (e.key === 'Escape' && dropdown) dropdown.style.display = 'none';
    });
    if (durationInput) {
        durationInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); if (durationDropdown) durationDropdown.style.display = 'none'; combineAndAdd(); }
            if (e.key === 'Escape' && durationDropdown) durationDropdown.style.display = 'none';
        });
        durationInput.addEventListener('blur', function() {
            setTimeout(function(){ if (durationDropdown) durationDropdown.style.display = 'none'; }, 150);
        });
    }

    var durationSearchTimer;
    if (durationInput && durationDropdown) {
        durationInput.addEventListener('input', function() {
            clearTimeout(durationSearchTimer);
            var q = durationInput.value.trim();
            if (!q) { durationDropdown.style.display = 'none'; return; }
            durationSearchTimer = setTimeout(function() {
                fetch(TERM_URL + '?category=complaint_durations&q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' }
                })
                .then(function(r){ return r.json(); })
                .then(function(data){
                    var items = Array.isArray(data) ? data : [];
                    durationDropdown.innerHTML = '';
                    if (!items.length) { durationDropdown.style.display = 'none'; return; }
                    items.forEach(function(item) {
                        var term = (typeof item === 'object' && item.term) ? item.term : String(item);
                        var el = document.createElement('div');
                        el.className = 'dd-item';
                        el.textContent = term;
                        el.addEventListener('mousedown', function(e) {
                            e.preventDefault();
                            durationInput.value = term;
                            durationDropdown.style.display = 'none';
                            combineAndAdd();
                        });
                        durationDropdown.appendChild(el);
                    });
                    durationDropdown.style.display = 'block';
                })
                .catch(function(){ durationDropdown.style.display = 'none'; });
            }, 300);
        });
    }

    var searchTimer;
    complaintInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = complaintInput.value.trim();
        if (!q || !dropdown) { if (dropdown) dropdown.style.display = 'none'; return; }
        searchTimer = setTimeout(function() {
            fetch(TERM_URL + '?category=' + encodeURIComponent(CATEGORY) + '&q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r){ return r.json(); })
            .then(function(data){
                var items = Array.isArray(data) ? data : [];
                dropdown.innerHTML = '';
                if (!items.length) { dropdown.style.display = 'none'; return; }
                items.forEach(function(item) {
                    var term = (typeof item === 'object' && item.term) ? item.term : String(item);
                    var el = document.createElement('div');
                    el.className = 'dd-item';
                    el.textContent = term;
                    el.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        complaintInput.value = term;
                        dropdown.style.display = 'none';
                        if (durationInput) durationInput.focus();
                    });
                    dropdown.appendChild(el);
                });
                dropdown.style.display = 'block';
            })
            .catch(function(){ if (dropdown) dropdown.style.display = 'none'; });
        }, 300);
    });
    complaintInput.addEventListener('blur', function() {
        setTimeout(function(){ if (dropdown) dropdown.style.display = 'none'; }, 150);
    });

    function renderPill(text) {
        var pill = document.createElement('span');
        pill.className = 'tag-pill';
        pill.innerHTML = escH(text) + ' <span class="remove-tag" style="cursor:pointer;">&times;</span>';
        pill.querySelector('.remove-tag').addEventListener('click', function () {
            pill.remove();
            tags = tags.filter(function(t){ return t !== text; });
            save();
        });
        tagsEl.appendChild(pill);
    }

    function escH(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function saveLocal() {
        try { localStorage.setItem(VISIT_LS_KEY, JSON.stringify(tags)); } catch(e) {}
    }

    function save() {
        saveLocal();
        var body = {};
        body[FIELD] = tags;
        fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        }).catch(function(){});
    }

    window.addEventListener('beforeunload', function () { try { save(); } catch(e) {} });
});
</script>

{{-- ── Past Medical History (condition for X years) ── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var CSRF     = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var SAVE_URL = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";
    var TERM_URL = "{{ route('terminology.search') }}";
    var FIELD    = 'past_medical_history';
    var CATEGORY = 'past_medical_history';
    var VISIT_LS_KEY = 'phims_v{{ $visit->id }}_' + FIELD;

    var conditionInput  = document.getElementById('pmh-condition-input');
    var yearsInput      = document.getElementById('pmh-years-input');
    var addBtn          = document.getElementById('pmh-add-btn');
    var tagsEl          = document.querySelector('#sec-past_medical_history .history-tags');
    var dropdown        = document.getElementById('pmh-condition-dropdown');
    var durationDropdown = document.getElementById('pmh-duration-dropdown');
    if (!conditionInput || !tagsEl || !addBtn) return;

    var tags = [];
    var lsRaw = null;
    try { lsRaw = localStorage.getItem(VISIT_LS_KEY); } catch(e) {}
    var initial = [];
    if (lsRaw !== null) {
        try { initial = JSON.parse(lsRaw); } catch(e) {}
    } else {
        try { initial = JSON.parse(tagsEl.dataset.initial || '[]'); } catch(e) {}
    }
    initial.forEach(function(t) { renderPill(t); tags.push(t); });
    saveLocal();

    window.__sectionTags = window.__sectionTags || {};
    window.__sectionTags[FIELD] = function() { return tags.slice(); };

    function combineAndAdd() {
        var condition = conditionInput.value.trim();
        var years     = yearsInput ? yearsInput.value.trim() : '';
        if (!condition) return;
        var tag = years ? (condition + ' for ' + years) : condition;
        if (tags.indexOf(tag) === -1) {
            tags.push(tag);
            renderPill(tag);
            save();
        }
        conditionInput.value = '';
        if (yearsInput) yearsInput.value = '';
        if (dropdown) dropdown.style.display = 'none';
        conditionInput.focus();
    }

    addBtn.addEventListener('click', combineAndAdd);
    conditionInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); combineAndAdd(); }
        if (e.key === 'Escape' && dropdown) dropdown.style.display = 'none';
    });
    if (yearsInput) {
        yearsInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); if (durationDropdown) durationDropdown.style.display = 'none'; combineAndAdd(); }
            if (e.key === 'Escape' && durationDropdown) durationDropdown.style.display = 'none';
        });
        yearsInput.addEventListener('blur', function() {
            setTimeout(function(){ if (durationDropdown) durationDropdown.style.display = 'none'; }, 150);
        });
    }

    var durationSearchTimer;
    if (yearsInput && durationDropdown) {
        yearsInput.addEventListener('input', function() {
            clearTimeout(durationSearchTimer);
            var q = yearsInput.value.trim();
            if (!q) { durationDropdown.style.display = 'none'; return; }
            durationSearchTimer = setTimeout(function() {
                fetch(TERM_URL + '?category=complaint_durations&q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' }
                })
                .then(function(r){ return r.json(); })
                .then(function(data){
                    var items = Array.isArray(data) ? data : [];
                    durationDropdown.innerHTML = '';
                    if (!items.length) { durationDropdown.style.display = 'none'; return; }
                    items.forEach(function(item) {
                        var term = (typeof item === 'object' && item.term) ? item.term : String(item);
                        var el = document.createElement('div');
                        el.className = 'dd-item';
                        el.textContent = term;
                        el.addEventListener('mousedown', function(e) {
                            e.preventDefault();
                            yearsInput.value = term;
                            durationDropdown.style.display = 'none';
                            combineAndAdd();
                        });
                        durationDropdown.appendChild(el);
                    });
                    durationDropdown.style.display = 'block';
                })
                .catch(function(){ durationDropdown.style.display = 'none'; });
            }, 300);
        });
    }

    var searchTimer;
    conditionInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = conditionInput.value.trim();
        if (!q || !dropdown) { if (dropdown) dropdown.style.display = 'none'; return; }
        searchTimer = setTimeout(function() {
            fetch(TERM_URL + '?category=' + encodeURIComponent(CATEGORY) + '&q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r){ return r.json(); })
            .then(function(data){
                var items = Array.isArray(data) ? data : [];
                dropdown.innerHTML = '';
                if (!items.length) { dropdown.style.display = 'none'; return; }
                items.forEach(function(item) {
                    var term = (typeof item === 'object' && item.term) ? item.term : String(item);
                    var el = document.createElement('div');
                    el.className = 'dd-item';
                    el.textContent = term;
                    el.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        conditionInput.value = term;
                        dropdown.style.display = 'none';
                        if (yearsInput) yearsInput.focus();
                    });
                    dropdown.appendChild(el);
                });
                dropdown.style.display = 'block';
            })
            .catch(function(){ if (dropdown) dropdown.style.display = 'none'; });
        }, 300);
    });
    conditionInput.addEventListener('blur', function() {
        setTimeout(function(){ if (dropdown) dropdown.style.display = 'none'; }, 150);
    });

    function renderPill(text) {
        var pill = document.createElement('span');
        pill.className = 'tag-pill';
        pill.innerHTML = escH(text) + ' <span class="remove-tag" style="cursor:pointer;">&times;</span>';
        pill.querySelector('.remove-tag').addEventListener('click', function () {
            pill.remove();
            tags = tags.filter(function(t){ return t !== text; });
            save();
        });
        tagsEl.appendChild(pill);
    }

    function escH(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function saveLocal() {
        try { localStorage.setItem(VISIT_LS_KEY, JSON.stringify(tags)); } catch(e) {}
    }

    function save() {
        saveLocal();
        var body = {};
        body[FIELD] = tags;
        fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        }).catch(function(){});
    }

    window.addEventListener('beforeunload', function () { try { save(); } catch(e) {} });
});
</script>

{{-- ── Past Surgical History (procedure on YEAR) ── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var CSRF     = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var SAVE_URL = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";
    var TERM_URL = "{{ route('terminology.search') }}";
    var FIELD    = 'past_surgical_history';
    var CATEGORY = 'past_surgical_history';
    var VISIT_LS_KEY = 'phims_v{{ $visit->id }}_' + FIELD;

    var procedureInput = document.getElementById('psh-procedure-input');
    var whenInput      = document.getElementById('psh-when-input');
    var addBtn         = document.getElementById('psh-add-btn');
    var tagsEl         = document.querySelector('#sec-past_surgical_history .history-tags');
    var dropdown       = document.getElementById('psh-procedure-dropdown');
    if (!procedureInput || !tagsEl || !addBtn) return;

    var tags = [];
    var lsRaw = null;
    try { lsRaw = localStorage.getItem(VISIT_LS_KEY); } catch(e) {}
    var initial = [];
    if (lsRaw !== null) {
        try { initial = JSON.parse(lsRaw); } catch(e) {}
    } else {
        try { initial = JSON.parse(tagsEl.dataset.initial || '[]'); } catch(e) {}
    }
    initial.forEach(function(t) { renderPill(t); tags.push(t); });
    saveLocal();

    window.__sectionTags = window.__sectionTags || {};
    window.__sectionTags[FIELD] = function() { return tags.slice(); };

    function combineAndAdd() {
        var procedure = procedureInput.value.trim();
        var when      = whenInput ? whenInput.value.trim() : '';
        if (!procedure) return;
        var tag = when ? (procedure + ' on ' + when) : procedure;
        if (tags.indexOf(tag) === -1) {
            tags.push(tag);
            renderPill(tag);
            save();
        }
        procedureInput.value = '';
        if (whenInput) whenInput.value = '';
        if (dropdown) dropdown.style.display = 'none';
        procedureInput.focus();
    }

    addBtn.addEventListener('click', combineAndAdd);
    procedureInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); combineAndAdd(); }
        if (e.key === 'Escape' && dropdown) dropdown.style.display = 'none';
    });
    if (whenInput) {
        whenInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); combineAndAdd(); }
        });
    }

    var searchTimer;
    procedureInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = procedureInput.value.trim();
        if (!q || !dropdown) { if (dropdown) dropdown.style.display = 'none'; return; }
        searchTimer = setTimeout(function() {
            fetch(TERM_URL + '?category=' + encodeURIComponent(CATEGORY) + '&q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r){ return r.json(); })
            .then(function(data){
                var items = Array.isArray(data) ? data : [];
                dropdown.innerHTML = '';
                if (!items.length) { dropdown.style.display = 'none'; return; }
                items.forEach(function(item) {
                    var term = (typeof item === 'object' && item.term) ? item.term : String(item);
                    var el = document.createElement('div');
                    el.className = 'dd-item';
                    el.textContent = term;
                    el.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        procedureInput.value = term;
                        dropdown.style.display = 'none';
                        if (whenInput) whenInput.focus();
                    });
                    dropdown.appendChild(el);
                });
                dropdown.style.display = 'block';
            })
            .catch(function(){ if (dropdown) dropdown.style.display = 'none'; });
        }, 300);
    });
    procedureInput.addEventListener('blur', function() {
        setTimeout(function(){ if (dropdown) dropdown.style.display = 'none'; }, 150);
    });

    function renderPill(text) {
        var pill = document.createElement('span');
        pill.className = 'tag-pill';
        pill.innerHTML = escH(text) + ' <span class="remove-tag" style="cursor:pointer;">&times;</span>';
        pill.querySelector('.remove-tag').addEventListener('click', function () {
            pill.remove();
            tags = tags.filter(function(t){ return t !== text; });
            save();
        });
        tagsEl.appendChild(pill);
    }

    function escH(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function saveLocal() {
        try { localStorage.setItem(VISIT_LS_KEY, JSON.stringify(tags)); } catch(e) {}
    }

    function save() {
        saveLocal();
        var body = {};
        body[FIELD] = tags;
        fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        }).catch(function(){});
    }

    window.addEventListener('beforeunload', function () { try { save(); } catch(e) {} });
});
</script>

{{-- ── History tag sections — one flat script per section (same pattern as Instruction) ── --}}
@foreach($sections as $sec)
<script>
document.addEventListener('DOMContentLoaded', function () {
    var CSRF     = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var SAVE_URL = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";
    var TERM_URL = "{{ route('terminology.search') }}";
    var FIELD    = '{{ $sec['key'] }}';
    var CATEGORY = '{{ $sec['category'] }}';

    var wrap = document.getElementById('sec-{{ $sec['key'] }}');
    if (!wrap) return;

    var input    = wrap.querySelector('.history-input');
    var tagsEl   = wrap.querySelector('.history-tags');
    var dropdown = wrap.querySelector('.history-dropdown');
    if (!input || !tagsEl || !dropdown) return;

    var VISIT_LS_KEY = 'phims_v{{ $visit->id }}_' + FIELD;

    var tags = [];
    var lsRaw = null;
    try { lsRaw = localStorage.getItem(VISIT_LS_KEY); } catch(e) {}
    var initial = [];
    if (lsRaw !== null) {
        // Prefer localStorage (reflects changes made since last AJAX save)
        try { initial = JSON.parse(lsRaw); } catch(e) {}
    } else {
        // Fall back to server data on first load
        try { initial = JSON.parse(tagsEl.dataset.initial || '[]'); } catch(e) {}
    }
    initial.forEach(function(t) { renderPill(t); tags.push(t); });
    saveLocal(); // seed localStorage with initial state

    // Register getter so End Visit can read current tags without DOM scraping
    window.__sectionTags = window.__sectionTags || {};
    window.__sectionTags[FIELD] = function() { return tags.slice(); };

    function addTagPill(text) {
        if (!text || tags.indexOf(text) !== -1) return;
        tags.push(text);
        renderPill(text);
    }

    function renderPill(text) {
        var pill = document.createElement('span');
        pill.className = 'tag-pill';
        pill.innerHTML = escH(text) + ' <span class="remove-tag" style="cursor:pointer;">&times;</span>';
        pill.querySelector('.remove-tag').addEventListener('click', function () {
            pill.remove();
            tags = tags.filter(function(t){ return t !== text; });
            save();
        });
        tagsEl.appendChild(pill);
    }

    function escH(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function saveLocal() {
        try { localStorage.setItem(VISIT_LS_KEY, JSON.stringify(tags)); } catch(e) {}
    }

    function save(keepalive) {
        saveLocal();
        var body = {};
        body[FIELD] = tags;
        fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
            keepalive: !!keepalive,
        })
        .then(function(r){})
        .catch(function(){});
    }

    window.addEventListener('beforeunload', function () { try { save(true); } catch(e) {} });

    var searchTimer;
    function showDd(items) {
        dropdown.innerHTML = '';
        if (!items.length) { dropdown.style.display = 'none'; return; }
        items.forEach(function(item) {
            var term = (typeof item === 'object' && item.term) ? item.term : String(item);
            var el = document.createElement('div');
            el.className = 'dd-item';
            el.textContent = term;
            el.addEventListener('mousedown', function(e) {
                e.preventDefault();
                input.value = '';
                dropdown.style.display = 'none';
                addTagPill(term);
                save();
            });
            dropdown.appendChild(el);
        });
        dropdown.style.display = 'block';
    }

    function hideDd() { dropdown.style.display = 'none'; }

    input.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = input.value.trim();
        if (!q) { hideDd(); return; }
        searchTimer = setTimeout(function() {
            fetch(TERM_URL + '?category=' + encodeURIComponent(CATEGORY) + '&q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r){ return r.json(); })
            .then(function(data){ showDd(Array.isArray(data) ? data : []); })
            .catch(function(){ hideDd(); });
        }, 300);
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var v = input.value.trim();
            if (v) {
                input.value = '';
                hideDd();
                addTagPill(v);
                save();
            }
        }
        if (e.key === 'Escape') hideDd();
    });

    input.addEventListener('blur', function() { setTimeout(hideDd, 150); });
});
</script>
@endforeach

{{-- ── Exam tag sections — one flat script per section ── --}}
@php
$allExamSecs = array_merge(
    [['key' => 'general_looking', 'category' => 'general_looking']],
    array_map(fn($es) => ['key' => $es['key'], 'category' => $es['cat']], $examSecs)
);
@endphp
@foreach($allExamSecs as $exSec)
<script>
document.addEventListener('DOMContentLoaded', function () {
    var CSRF     = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var SAVE_URL = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";
    var TERM_URL = "{{ route('terminology.search') }}";
    var FIELD    = '{{ $exSec['key'] }}';
    var CATEGORY = '{{ $exSec['category'] }}';

    var wrap = document.getElementById('sec-{{ $exSec['key'] }}');
    if (!wrap) return;

    var input    = wrap.querySelector('.exam-input');
    var tagsEl   = wrap.querySelector('.exam-tags');
    var dropdown = wrap.querySelector('.exam-dropdown');
    if (!input || !tagsEl || !dropdown) return;

    var VISIT_LS_KEY = 'phims_v{{ $visit->id }}_' + FIELD;

    var tags = [];
    var lsRaw = null;
    try { lsRaw = localStorage.getItem(VISIT_LS_KEY); } catch(e) {}
    var initial = [];
    if (lsRaw !== null) {
        // Prefer localStorage (reflects changes made since last AJAX save)
        try { initial = JSON.parse(lsRaw); } catch(e) {}
    } else {
        // Fall back to server data on first load
        try { initial = JSON.parse(tagsEl.dataset.initial || '[]'); } catch(e) {}
    }
    initial.forEach(function(t) { renderPill(t); tags.push(t); });
    saveLocal(); // seed localStorage with initial state

    // Register getter so End Visit can read current tags without DOM scraping
    window.__sectionTags = window.__sectionTags || {};
    window.__sectionTags[FIELD] = function() { return tags.slice(); };

    function addTagPill(text) {
        if (!text || tags.indexOf(text) !== -1) return;
        tags.push(text);
        renderPill(text);
    }

    function renderPill(text) {
        var pill = document.createElement('span');
        pill.className = 'tag-pill';
        pill.innerHTML = escH(text) + ' <span class="remove-tag" style="cursor:pointer;">&times;</span>';
        pill.querySelector('.remove-tag').addEventListener('click', function () {
            pill.remove();
            tags = tags.filter(function(t){ return t !== text; });
            save();
        });
        tagsEl.appendChild(pill);
    }

    function escH(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function saveLocal() {
        try { localStorage.setItem(VISIT_LS_KEY, JSON.stringify(tags)); } catch(e) {}
    }

    function save(keepalive) {
        saveLocal();
        var body = {};
        body[FIELD] = tags;
        fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
            keepalive: !!keepalive,
        })
        .then(function(r){})
        .catch(function(){});
    }

    window.addEventListener('beforeunload', function () { try { save(true); } catch(e) {} });

    var searchTimer;
    function showDd(items) {
        dropdown.innerHTML = '';
        if (!items.length) { dropdown.style.display = 'none'; return; }
        items.forEach(function(item) {
            var term = (typeof item === 'object' && item.term) ? item.term : String(item);
            var el = document.createElement('div');
            el.className = 'dd-item';
            el.textContent = term;
            el.addEventListener('mousedown', function(e) {
                e.preventDefault();
                input.value = '';
                dropdown.style.display = 'none';
                addTagPill(term);
                save();
            });
            dropdown.appendChild(el);
        });
        dropdown.style.display = 'block';
    }

    function hideDd() { dropdown.style.display = 'none'; }

    input.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = input.value.trim();
        if (!q) { hideDd(); return; }
        searchTimer = setTimeout(function() {
            fetch(TERM_URL + '?category=' + encodeURIComponent(CATEGORY) + '&q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r){ return r.json(); })
            .then(function(data){ showDd(Array.isArray(data) ? data : []); })
            .catch(function(){ hideDd(); });
        }, 300);
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var v = input.value.trim();
            if (v) {
                input.value = '';
                hideDd();
                addTagPill(v);
                save();
            }
        }
        if (e.key === 'Escape') hideDd();
    });

    input.addEventListener('blur', function() { setTimeout(hideDd, 150); });
});
</script>
@endforeach

{{-- ── Pulse rate save ── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var CSRF         = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var SAVE_URL     = "{{ route('clinical.doctor.save-notes', [$unitView->id, $visit->id]) }}";
    var PULSE_LS_KEY = 'phims_v{{ $visit->id }}_pulse_rate';
    var pulseInput   = document.getElementById('pulse-rate-input');
    if (!pulseInput) return;

    // Restore from localStorage if available (persists across reloads)
    var lsVal = null;
    try { lsVal = localStorage.getItem(PULSE_LS_KEY); } catch(e) {}
    if (lsVal !== null) {
        pulseInput.value = lsVal;
    }

    pulseInput.addEventListener('change', function() {
        var val = this.value !== '' ? parseInt(this.value, 10) : null;
        // Keep localStorage in sync
        try {
            if (this.value !== '') localStorage.setItem(PULSE_LS_KEY, this.value);
            else localStorage.removeItem(PULSE_LS_KEY);
        } catch(e) {}
        fetch(SAVE_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify({ pulse_rate: val }),
        })
        .then(function(r){})
        .catch(function(){});
    });
    window.addEventListener('beforeunload', function() {
        if (pulseInput.value === '') return;
        try {
            fetch(SAVE_URL, {
                method: 'POST', keepalive: true,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ pulse_rate: parseInt(pulseInput.value, 10) }),
            });
        } catch(e) {}
    });
});
</script>
@endpush
