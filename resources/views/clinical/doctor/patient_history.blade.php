@extends('layouts.clinical')
@section('title', ($patient->name ?? 'Patient') . ' — Profile')

@push('styles')
<style>
/* ── Animations ─────────────────────────────────────────────────────────── */
@keyframes pulse-ring { 0%,100%{box-shadow:0 0 0 0 rgba(37,99,235,.35)} 50%{box-shadow:0 0 0 10px rgba(37,99,235,0)} }
@keyframes blink      { 0%,100%{opacity:1} 50%{opacity:.2} }
.today-pulse { animation: pulse-ring 2s ease-in-out infinite; }
.blink-dot   { animation: blink 1s ease-in-out infinite; display:inline-block; }

/* ── Hero header ─────────────────────────────────────────────────────────── */
.patient-hero {
    background: linear-gradient(135deg, #1e3a5f 0%, #1e4d8c 50%, #1a6b8a 100%);
    border-radius: .75rem;
    color: #fff;
    padding: .85rem 1.1rem;
}
.patient-avatar {
    width: 2.6rem; height: 2.6rem;
    background: rgba(255,255,255,.18);
    border: 1.5px solid rgba(255,255,255,.35);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; font-weight: 700; flex-shrink: 0;
    color: #fff;
}
.hero-stat {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: .45rem;
    padding: .25rem .6rem;
    text-align: center;
    white-space: nowrap;
}
.hero-stat .stat-val { font-size: .88rem; font-weight: 700; line-height: 1.2; }
.hero-stat .stat-lbl { font-size: .58rem; text-transform: uppercase; letter-spacing: .05em; opacity: .72; }

/* ── Demographics row ────────────────────────────────────────────────────── */
.demo-pill {
    background: rgba(255,255,255,.13);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: .35rem;
    padding: .18rem .5rem;
    font-size: .75rem;
    color: rgba(255,255,255,.9);
    white-space: nowrap;
}
.demo-pill .lbl { font-size: .56rem; opacity: .68; text-transform: uppercase; letter-spacing: .05em; display: block; margin-bottom: 1px; }

/* ── Allergy banner ──────────────────────────────────────────────────────── */
.allergy-banner {
    background: linear-gradient(90deg, #fef2f2, #fff5f5);
    border-left: 4px solid #ef4444;
    border-radius: .5rem;
    padding: .75rem 1rem;
}

/* ── Tab pills (custom) ──────────────────────────────────────────────────── */
.profile-tabs .nav-link {
    border: 1px solid #e2e8f0;
    border-radius: .5rem;
    color: #64748b;
    font-size: .85rem;
    padding: .5rem 1.25rem;
    font-weight: 500;
    transition: all .15s;
}
.profile-tabs .nav-link.active {
    background: #1e4d8c;
    border-color: #1e4d8c;
    color: #fff;
    box-shadow: 0 2px 8px rgba(30,77,140,.35);
}
.profile-tabs .nav-link:not(.active):hover { background: #f0f7ff; border-color: #bfdbfe; color: #1e4d8c; }

/* ── Section cards ───────────────────────────────────────────────────────── */
.section-card {
    border: 0;
    border-radius: .75rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.04);
    overflow: hidden;
}
.section-card .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    padding: .85rem 1.25rem;
}
.section-header-icon {
    width: 2rem; height: 2rem;
    border-radius: .4rem;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}

/* ── Tag badges ──────────────────────────────────────────────────────────── */
.tag-badge {
    display: inline-block;
    padding: .25rem .6rem;
    border-radius: 2rem;
    font-size: .76rem;
    font-weight: 500;
    margin: .15rem .15rem .15rem 0;
    border: 1px solid transparent;
}
.tag-medical  { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
.tag-surgical { background:#fff7ed; color:#9a3412; border-color:#fdba74; }
.tag-social   { background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
.tag-menstrual{ background:#fdf4ff; color:#7e22ce; border-color:#e9d5ff; }
.tag-complaint{ background:#f0fdfa; color:#0f766e; border-color:#99f6e4; }
.tag-exam     { background:#f0fdf4; color:#14532d; border-color:#bbf7d0; }
.tag-drug     { background:#f0f9ff; color:#0c4a6e; border-color:#bae6fd; }
.tag-neutral  { background:#f8fafc; color:#334155; border-color:#e2e8f0; }

/* ── Complaint frequency bar ─────────────────────────────────────────────── */
.complaint-bar-wrap { display:flex; align-items:center; gap:.6rem; margin-bottom:.5rem; }
.complaint-bar-track { flex:1; background:#f1f5f9; border-radius:2rem; height:6px; overflow:hidden; }
.complaint-bar-fill  { height:100%; border-radius:2rem; background:linear-gradient(90deg,#0891b2,#0e7490); }

/* ── Visit history sidebar ───────────────────────────────────────────────── */
.visit-item { transition: background .12s; }
.visit-item:hover { background: #f0f7ff; }
.visit-item.active { background: #eff6ff; border-left: 3px solid #2563eb; }
.sidebar-sticky { position: sticky; top: 1rem; }

/* ── Summary label ───────────────────────────────────────────────────────── */
.summary-lbl {
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #94a3b8;
    font-weight: 700;
    margin-bottom: .3rem;
}
.empty-note { color: #cbd5e1; font-style: italic; font-size: .82rem; }

/* ── Investigation table ─────────────────────────────────────────────────── */
.inv-table th { background: #f8fafc; font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b; }
.inv-table td { font-size: .83rem; vertical-align: middle; }

/* ── Drug chart ──────────────────────────────────────────────────────────── */
.drug-row-head { background: #f0f9ff; }
.drug-row-head td { font-size: .72rem; color: #0369a1; font-weight: 600; padding: .35rem .75rem; }
.drug-row-body td { font-size: .8rem; padding: .4rem .75rem; }

/* ── Visit detail card (right panel) ────────────────────────────────────── */
.visit-detail-section { border-top: 1px solid #f1f5f9; padding-top: 1rem; margin-top: 1rem; }
.detail-lbl { font-size: .64rem; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; font-weight: 700; margin-bottom: .3rem; }
</style>
@endpush

@section('content')
@php
/* ── Aggregate data across all past visits ── */
$allNotes       = $pastVisits->map(fn($v) => $v->note)->filter();
$allBpReadings  = $pastVisits->flatMap(fn($v) => $v->bpReadings)->sortBy('recorded_at')->values();
$allInvs        = $pastVisits->flatMap(fn($v) => $v->investigations->map(fn($i) => array_merge($i->toArray(), ['visit_no' => $v->visit_number, 'visit_date' => $v->visit_date])))->sortByDesc('recorded_at')->values();

/* ── Unique aggregated history items ── */
$aggMedHist  = $allNotes->flatMap(fn($n) => $n->past_medical_history  ?? [])->unique()->values();
$aggSurgHist = $allNotes->flatMap(fn($n) => $n->past_surgical_history ?? [])->unique()->values();
$aggSocial   = $allNotes->flatMap(fn($n) => $n->social_history        ?? [])->unique()->values();
$aggMenst    = $allNotes->flatMap(fn($n) => $n->menstrual_history     ?? [])->unique()->values();

/* ── Presenting complaints with frequency ── */
$complaintCounts = $allNotes->flatMap(fn($n) => $n->presenting_complaints ?? [])->countBy()->sortByDesc(fn($c) => $c);
$maxComplaint    = $complaintCounts->max() ?: 1;

/* ── Examination findings aggregated ── */
$examFields = [
    'general_looking'       => ['General Looking',   '#0891b2', 'bi-eye'],
    'cardiology_findings'   => ['Cardiovascular',    '#dc2626', 'bi-heart-pulse'],
    'respiratory_findings'  => ['Respiratory',       '#2563eb', 'bi-lungs'],
    'abdominal_findings'    => ['Abdominal',         '#059669', 'bi-body-text'],
    'neurological_findings' => ['Neurological',      '#7c3aed', 'bi-activity'],
    'dermatological_findings'=>['Dermatological',    '#d97706', 'bi-bandaid'],
];
$aggExam = [];
foreach ($examFields as $k => [$label, $color, $icon]) {
    $vals = $allNotes->flatMap(fn($n) => $n->{$k} ?? [])->filter(fn($v) => $v !== 'Normal findings')->unique()->values();
    $aggExam[$k] = ['label'=>$label, 'color'=>$color, 'icon'=>$icon, 'values'=>$vals];
}

/* ── Category labels ── */
$catLabels = [
    'opd'                    => ['OPD',       'bi-hospital',         'primary', '#2563eb'],
    'new_clinic_visit'       => ['New',       'bi-person-plus-fill', 'success', '#059669'],
    'recurrent_clinic_visit' => ['Recurrent', 'bi-arrow-repeat',     'info',    '#0891b2'],
    'urgent'                 => ['Urgent',    'bi-person-badge-fill','danger',  '#dc2626'],
];

$totalVisits = $pastVisits->count() + ($todayVisit ? 1 : 0);
$firstVisit  = $pastVisits->last();

/* ── Initials for avatar ── */
$nameParts = explode(' ', trim($patient->name));
$initials  = strtoupper(substr($nameParts[0] ?? '?', 0, 1) . substr(end($nameParts), 0, 1));

/* ── Selected visit for detail panel ── */
$sv = $selectedVisit;
@endphp

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- HERO HEADER (compact strip)                                           --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="patient-hero mb-3">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        {{-- Back --}}
        <a href="{{ route('clinical.show', $unitView->id) }}"
           class="btn btn-sm btn-link text-white p-0 text-decoration-none flex-shrink-0">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        {{-- Avatar --}}
        <div class="patient-avatar flex-shrink-0">{{ $initials }}</div>
        {{-- Name + unit --}}
        <div class="flex-grow-1 me-1">
            <div class="fw-bold text-white lh-sm" style="font-size:1rem;">{{ $patient->name }}</div>
            <div style="color:rgba(255,255,255,.65);font-size:.72rem;">
                {{ $unitView->unit->name }} &middot; {{ $unitView->unit->institution->name }}
            </div>
        </div>
        {{-- Demographics pills --}}
        @if($patient->phn)
        <div class="demo-pill"><span class="lbl">PHN</span>{{ $patient->phn }}</div>
        @endif
        <div class="demo-pill"><span class="lbl">Age</span>{{ $patient->computed_age ?? '—' }} yrs</div>
        <div class="demo-pill"><span class="lbl">Sex</span>{{ ucfirst($patient->gender) }}</div>
        @if($patient->nic)
        <div class="demo-pill"><span class="lbl">NIC</span>{{ $patient->nic }}</div>
        @endif
        @if($patient->mobile)
        <div class="demo-pill"><span class="lbl">Mobile</span>{{ $patient->mobile }}</div>
        @endif
        {{-- Stats --}}
        <div class="hero-stat">
            <div class="stat-val">{{ $totalVisits }}</div>
            <div class="stat-lbl">Visits</div>
        </div>
        @if($allBpReadings->isNotEmpty())
        @php $latestBp = $allBpReadings->last(); @endphp
        <div class="hero-stat">
            <div class="stat-val">{{ $latestBp->systolic }}/{{ $latestBp->diastolic }}</div>
            <div class="stat-lbl">Latest BP</div>
        </div>
        @endif
        <div class="hero-stat">
            <div class="stat-val">{{ $patient->allergies->count() }}</div>
            <div class="stat-lbl">Allergies</div>
        </div>
        {{-- Edit button --}}
        <a href="{{ route('clinical.patients.edit', [$unitView->id, $patient->id]) }}"
           class="btn btn-sm flex-shrink-0"
           style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:.4rem;padding:.25rem .65rem;font-size:.78rem;">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ALLERGY BANNER                                                        --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
@if($patient->allergies->isNotEmpty())
<div class="allergy-banner mb-3 d-flex align-items-center gap-2 flex-wrap">
    <div class="flex-shrink-0">
        <span class="badge text-bg-danger fs-6 px-2 py-1">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>ALLERGIES
        </span>
    </div>
    @foreach($patient->allergies as $allergy)
        <span class="tag-badge tag-medical fw-semibold">{{ $allergy->allergen }}</span>
    @endforeach
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TODAY'S VISIT ACTION CARD                                             --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
@if($todayVisit)
@php [$cl,$ci,$cc] = $catLabels[$todayVisit->category] ?? ['—','bi-dash','primary']; @endphp
<div class="card section-card border-2 border-primary mb-3 {{ $todayVisit->status === 'waiting' ? 'today-pulse' : '' }}">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold flex-shrink-0"
                 style="width:2.6rem;height:2.6rem;background:#eff6ff;font-size:.9rem;">
                #{{ $todayVisit->visit_number }}
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">Today's Visit</div>
                <div class="text-muted small">
                    <i class="bi {{ $ci }} me-1 text-{{ $cc }}"></i>{{ $cl }}
                    <span class="ms-2"><i class="bi bi-calendar3 me-1"></i>{{ now()->format('d M Y') }}</span>
                </div>
            </div>
            @if($todayVisit->status === 'waiting')
                <form method="POST" action="{{ route('clinical.doctor.start-visit', [$unitView->id, $todayVisit->id]) }}">
                    @csrf
                    <button class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-play-circle-fill me-1"></i>Start Visit
                    </button>
                </form>
            @elseif($todayVisit->status === 'in_progress')
                <a href="{{ route('clinical.doctor.visit-page', [$unitView->id, $todayVisit->id]) }}"
                   class="btn btn-warning btn-sm px-3 text-dark">
                    <span class="blink-dot text-danger me-1">●</span>Continue
                </a>
            @else
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                    <i class="bi bi-check-circle-fill me-1"></i>Completed
                </span>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TABS                                                                  --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<ul class="nav profile-tabs gap-2 mb-3" id="profileTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="summary-tab" data-bs-toggle="tab"
                data-bs-target="#summary-pane" type="button" role="tab">
            <i class="bi bi-person-lines-fill me-1"></i>Patient Summary
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="visits-tab" data-bs-toggle="tab"
                data-bs-target="#visits-pane" type="button" role="tab">
            <i class="bi bi-journal-medical me-1"></i>Visit History
            @if($pastVisits->isNotEmpty())
                <span class="badge rounded-pill ms-1"
                      style="background:#1e4d8c;font-size:.62rem;">{{ $pastVisits->count() }}</span>
            @endif
        </button>
    </li>
</ul>

<div class="tab-content">

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 1 — PATIENT SUMMARY                                              --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade show active" id="summary-pane" role="tabpanel">

    {{-- Row 1 — History columns --}}
    <div class="row g-3 mb-3">

        {{-- Medical & Surgical History --}}
        <div class="col-lg-6">
            <div class="card section-card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-header-icon" style="background:#fef2f2;color:#dc2626;">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </span>
                    <span class="fw-semibold" style="font-size:.9rem;">Past Medical &amp; Surgical History</span>
                </div>
                <div class="card-body p-3">
                    @if($aggMedHist->isNotEmpty())
                    <div class="mb-3">
                        <div class="summary-lbl">Medical History</div>
                        @foreach($aggMedHist as $v)
                            <span class="tag-badge tag-medical">{{ $v }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if($aggSurgHist->isNotEmpty())
                    <div class="mb-2">
                        <div class="summary-lbl">Surgical History</div>
                        @foreach($aggSurgHist as $v)
                            <span class="tag-badge tag-surgical">{{ $v }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if($aggMedHist->isEmpty() && $aggSurgHist->isEmpty())
                        <span class="empty-note">No recorded history.</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Social & Menstrual + Allergies --}}
        <div class="col-lg-6">
            <div class="card section-card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-header-icon" style="background:#eff6ff;color:#2563eb;">
                        <i class="bi bi-people"></i>
                    </span>
                    <span class="fw-semibold" style="font-size:.9rem;">Social, Menstrual &amp; Allergies</span>
                </div>
                <div class="card-body p-3">
                    @if($patient->allergies->isNotEmpty())
                    <div class="mb-3">
                        <div class="summary-lbl"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>Allergies</div>
                        @foreach($patient->allergies as $a)
                            <span class="tag-badge tag-medical fw-semibold">{{ $a->allergen }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if($aggSocial->isNotEmpty())
                    <div class="mb-3">
                        <div class="summary-lbl">Social History</div>
                        @foreach($aggSocial as $v)
                            <span class="tag-badge tag-social">{{ $v }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if($aggMenst->isNotEmpty())
                    <div class="mb-2">
                        <div class="summary-lbl">Menstrual History</div>
                        @foreach($aggMenst as $v)
                            <span class="tag-badge tag-menstrual">{{ $v }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if($patient->allergies->isEmpty() && $aggSocial->isEmpty() && $aggMenst->isEmpty())
                        <span class="empty-note">No recorded data.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2 — Presenting Complaints + BP Chart --}}
    <div class="row g-3 mb-3">

        {{-- Presenting Complaints frequency --}}
        <div class="col-lg-5">
            <div class="card section-card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-header-icon" style="background:#f0fdfa;color:#0891b2;">
                        <i class="bi bi-chat-square-text"></i>
                    </span>
                    <span class="fw-semibold" style="font-size:.9rem;">Presenting Complaints</span>
                    @if($complaintCounts->isNotEmpty())
                        <span class="badge rounded-pill ms-auto"
                              style="background:#e0f2fe;color:#0369a1;font-size:.65rem;">
                            {{ $complaintCounts->count() }} unique
                        </span>
                    @endif
                </div>
                <div class="card-body p-3">
                    @if($complaintCounts->isEmpty())
                        <span class="empty-note">No complaints recorded.</span>
                    @else
                        @foreach($complaintCounts as $complaint => $count)
                        <div class="complaint-bar-wrap">
                            <span class="tag-badge tag-complaint mb-0" style="min-width:7rem;">{{ $complaint }}</span>
                            <div class="complaint-bar-track flex-grow-1">
                                <div class="complaint-bar-fill"
                                     style="width:{{ round(($count/$maxComplaint)*100) }}%"></div>
                            </div>
                            <span class="text-muted" style="font-size:.72rem;min-width:2.2rem;text-align:right;">
                                ×{{ $count }}
                            </span>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- BP Trend Chart --}}
        <div class="col-lg-7">
            <div class="card section-card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-header-icon" style="background:#fef2f2;color:#dc2626;">
                        <i class="bi bi-activity"></i>
                    </span>
                    <span class="fw-semibold" style="font-size:.9rem;">Blood Pressure Trend</span>
                    @if($allBpReadings->isNotEmpty())
                        <span class="badge rounded-pill ms-auto"
                              style="background:#fef2f2;color:#dc2626;font-size:.65rem;">
                            {{ $allBpReadings->count() }} readings
                        </span>
                    @endif
                </div>
                <div class="card-body p-3">
                    @if($allBpReadings->isEmpty())
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted py-4">
                            <div class="text-center">
                                <i class="bi bi-bar-chart-line" style="font-size:2.5rem;opacity:.12;"></i>
                                <p class="mt-2 mb-0 small">No BP readings recorded.</p>
                            </div>
                        </div>
                    @else
                        <canvas id="bp-chart" style="max-height:210px;"></canvas>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3 — Examination Findings --}}
    @if(collect($aggExam)->filter(fn($e) => $e['values']->isNotEmpty())->isNotEmpty())
    <div class="card section-card mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <span class="section-header-icon" style="background:#f0fdf4;color:#059669;">
                <i class="bi bi-stethoscope"></i>
            </span>
            <span class="fw-semibold" style="font-size:.9rem;">Examination Findings</span>
            <span class="ms-1 text-muted small">(unique abnormal findings across all visits)</span>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                @foreach($aggExam as $k => $ef)
                    @if($ef['values']->isNotEmpty())
                    <div class="col-sm-6 col-lg-4">
                        <div class="summary-lbl">
                            <i class="bi {{ $ef['icon'] }} me-1" style="color:{{ $ef['color'] }};"></i>
                            {{ $ef['label'] }}
                        </div>
                        @foreach($ef['values'] as $v)
                            <span class="tag-badge tag-exam">{{ $v }}</span>
                        @endforeach
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Row 4 — Investigations --}}
    @if($allInvs->isNotEmpty())
    @php
        $invChartGroups = $allInvs->groupBy('name')->map(function ($rows, $name) {
            $sorted      = $rows->sortBy('recorded_at')->values();
            $numericVals = $sorted->map(fn($r) => is_numeric($r['value']) ? (float) $r['value'] : null);
            $allNumeric  = $sorted->count() > 0 && $numericVals->filter(fn($v) => $v !== null)->count() === $sorted->count();
            return [
                'name'         => $name,
                'labels'       => $sorted->map(fn($r) => \Carbon\Carbon::parse($r['recorded_at'])->format('d M Y'))->values()->toArray(),
                'values'       => $sorted->map(fn($r) => $r['value'])->values()->toArray(),
                'num_values'   => $allNumeric ? $numericVals->values()->toArray() : null,
                'is_numeric'   => $allNumeric,
                'latest_value' => $sorted->last()['value'],
                'latest_date'  => \Carbon\Carbon::parse($sorted->last()['recorded_at'])->format('d M Y'),
                'count'        => $sorted->count(),
            ];
        })->values();
    @endphp
    <div class="card section-card mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <span class="section-header-icon" style="background:#fffbeb;color:#d97706;">
                <i class="bi bi-eyedropper"></i>
            </span>
            <span class="fw-semibold" style="font-size:.9rem;">Investigation Results</span>
            <span class="badge rounded-pill ms-auto"
                  style="background:#fffbeb;color:#92400e;font-size:.65rem;">
                {{ $allInvs->count() }} results
            </span>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                @foreach($invChartGroups as $ic)
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="border rounded-3 p-3 h-100" style="background:#fafafa;">
                        {{-- Header: name + latest value badge --}}
                        <div class="d-flex align-items-start justify-content-between mb-1 gap-2">
                            <span class="fw-semibold" style="font-size:.82rem;color:#1e293b;">{{ $ic['name'] }}</span>
                            <span class="badge rounded-pill flex-shrink-0"
                                  style="background:#ecfdf5;color:#065f46;font-size:.72rem;white-space:nowrap;">
                                {{ $ic['latest_value'] }}
                            </span>
                        </div>
                        <p class="text-muted mb-2" style="font-size:.68rem;">
                            Latest: {{ $ic['latest_date'] }}
                            &nbsp;·&nbsp;
                            {{ $ic['count'] }} {{ $ic['count'] === 1 ? 'reading' : 'readings' }}
                        </p>

                        @if($ic['is_numeric'] && $ic['count'] > 1)
                            {{-- Line chart for numeric multi-point data --}}
                            <canvas class="inv-mini-chart"
                                    data-labels='@json($ic["labels"])'
                                    data-values='@json($ic["num_values"])'
                                    data-name="{{ $ic['name'] }}"
                                    height="80"></canvas>
                        @elseif($ic['count'] > 1)
                            {{-- Non-numeric: simple value/date list --}}
                            <div style="max-height:90px;overflow-y:auto;">
                                @php $pairs = array_reverse(array_map(null, $ic['labels'], $ic['values'])); @endphp
                                @foreach($pairs as $pair)
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom"
                                     style="font-size:.72rem;">
                                    <span class="text-muted">{{ $pair[0] }}</span>
                                    <span class="fw-semibold" style="color:#059669;">{{ $pair[1] }}</span>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Row 5 — Clinic Drug Chart (latest visit only) --}}
    @php
        $latestVisitWithDrugs = $pastVisits->first(fn($v) => $v->drugs->where('section','clinic')->isNotEmpty());
        $latestClinicDrugs    = $latestVisitWithDrugs ? $latestVisitWithDrugs->drugs->where('section','clinic') : collect();
    @endphp
    @if($latestClinicDrugs->isNotEmpty())
    <div class="card section-card mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <span class="section-header-icon" style="background:#f0f9ff;color:#0369a1;">
                <i class="bi bi-capsule-pill"></i>
            </span>
            <span class="fw-semibold" style="font-size:.9rem;">Clinic Drug Chart</span>
            <span class="ms-1 text-muted small">
                Visit #{{ $latestVisitWithDrugs->visit_number }} &mdash; {{ $latestVisitWithDrugs->visit_date->format('d M Y') }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:8%;font-size:.72rem;">Route</th>
                            <th style="font-size:.72rem;">Drug</th>
                            <th style="width:8%;font-size:.72rem;">Dose</th>
                            <th style="width:7%;font-size:.72rem;">Unit</th>
                            <th style="width:9%;font-size:.72rem;">Freq.</th>
                            <th style="width:12%;font-size:.72rem;">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestClinicDrugs as $drug)
                        <tr class="drug-row-body">
                            <td><span class="tag-badge tag-drug mb-0" style="font-size:.7rem;">{{ $drug->type }}</span></td>
                            <td class="fw-semibold" style="color:#0c4a6e;">{{ $drug->name }}</td>
                            <td>{{ $drug->dose }}</td>
                            <td>{{ $drug->unit }}</td>
                            <td>{{ $drug->frequency }}</td>
                            <td>{{ $drug->duration ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($pastVisits->isEmpty())
    <div class="card section-card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-text" style="font-size:3.5rem;opacity:.08;"></i>
            <p class="mt-3 mb-1 fw-medium">No completed visits yet</p>
            <p class="small mb-0">Patient summary will appear here after visits are completed.</p>
        </div>
    </div>
    @endif

</div>{{-- /summary-pane --}}

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 2 — VISIT HISTORY                                                --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade" id="visits-pane" role="tabpanel">

    @if($pastVisits->isEmpty())
    <div class="card section-card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-x" style="font-size:3rem;opacity:.1;"></i>
            <p class="mt-3 mb-1 fw-medium">No previous visits</p>
            <p class="small mb-0">Completed visit details will appear here.</p>
        </div>
    </div>
    @else
    <div class="row g-3 align-items-start">

        {{-- ── Visit list sidebar ──────────────────────────────────────── --}}
        <div class="col-lg-4 col-xl-3">
            <div class="card section-card sidebar-sticky">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span class="fw-semibold" style="font-size:.85rem;">
                        <i class="bi bi-clock-history me-2 text-muted"></i>All Visits
                    </span>
                    <span class="badge rounded-pill"
                          style="background:#1e4d8c;color:#fff;font-size:.65rem;">
                        {{ $pastVisits->count() }}
                    </span>
                </div>
                <div class="list-group list-group-flush" style="max-height:70vh;overflow-y:auto;">
                    @foreach($pastVisits as $v)
                        @php
                            [$vl, $vi, $vc] = $catLabels[$v->category] ?? ['—','bi-dash','secondary'];
                            $isActive = $sv && $sv->id === $v->id;
                        @endphp
                        <a href="{{ route('clinical.doctor.patient-history', [$unitView->id, $patient->id]) }}?visit={{ $v->id }}#visits-tab"
                           class="list-group-item list-group-item-action visit-item px-3 py-2 {{ $isActive ? 'active' : '' }}"
                           onclick="document.getElementById('visits-tab').click()">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                      style="width:2rem;height:2rem;font-size:.7rem;background:{{ $isActive ? '#dbeafe' : '#f1f5f9' }};color:{{ $isActive ? '#1e40af' : '#64748b' }};">
                                    #{{ $v->visit_number }}
                                </span>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-medium" style="font-size:.82rem;">
                                        {{ $v->visit_date->format('d M Y') }}
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size:.7rem;">
                                        <i class="bi {{ $vi }} me-1 text-{{ $vc }}"></i>{{ $vl }}
                                    </div>
                                </div>
                                @if($v->status === 'visited')
                                    <span class="badge" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;font-size:.58rem;">
                                        Done
                                    </span>
                                @elseif($v->status === 'in_progress')
                                    <span class="badge" style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;font-size:.58rem;">
                                        Active
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Visit detail panel ──────────────────────────────────────── --}}
        <div class="col-lg-8 col-xl-9">
            @if($sv)
            @php
                $svNote = $sv->note;
                [$svLabel, $svIcon, $svColor] = $catLabels[$sv->category] ?? ['—','bi-dash','secondary'];
                $svClinicDrugs = $sv->drugs->where('section','clinic')->values();
                $svMgmtDrugs   = $sv->drugs->where('section','management')->values();
                $svHistFields = [
                    'presenting_complaints' => 'Presenting Complaints',
                    'complaint_durations'   => 'Duration',
                    'past_medical_history'  => 'Past Medical History',
                    'past_surgical_history' => 'Past Surgical History',
                    'social_history'        => 'Social History',
                    'menstrual_history'     => 'Menstrual History',
                ];
                $svExamFields = [
                    'general_looking'        => ['General Looking',    'bi-eye',         '#0891b2'],
                    'cardiology_findings'    => ['Cardiovascular',     'bi-heart-pulse', '#dc2626'],
                    'respiratory_findings'   => ['Respiratory',        'bi-lungs',       '#2563eb'],
                    'abdominal_findings'     => ['Abdominal',          'bi-body-text',   '#059669'],
                    'neurological_findings'  => ['Neurological',       'bi-activity',    '#7c3aed'],
                    'dermatological_findings'=> ['Dermatological',     'bi-bandaid',     '#d97706'],
                ];
            @endphp

            {{-- Visit header strip --}}
            <div class="card section-card mb-3">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                             style="width:3rem;height:3rem;font-size:1rem;background:{{ $svColor === 'primary' ? '#eff6ff' : ($svColor === 'success' ? '#f0fdf4' : ($svColor === 'danger' ? '#fef2f2' : '#f0f9ff')) }};color:{{ '#' . ($svColor === 'primary' ? '1e40af' : ($svColor === 'success' ? '166534' : ($svColor === 'danger' ? '991b1b' : '0369a1'))) }};">
                            #{{ $sv->visit_number }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold fs-6">{{ $sv->visit_date->format('d M Y') }}</div>
                            <div class="text-muted small">
                                <i class="bi {{ $svIcon }} me-1 text-{{ $svColor }}"></i>{{ $svLabel }}
                            </div>
                        </div>
                        <span class="badge px-2 py-1"
                              style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
                            <i class="bi bi-check-circle-fill me-1"></i>Visited
                        </span>
                    </div>

                    @if($sv->height || $sv->weight || $sv->bp_systolic || $sv->clinic_number)
                    <hr class="my-2">
                    <div class="row g-2" style="font-size:.82rem;">
                        @if($sv->clinic_number)
                        <div class="col-6 col-sm-3">
                            <div class="detail-lbl">Clinic No.</div>
                            <span class="fw-semibold">{{ $sv->clinic_number }}</span>
                        </div>
                        @endif
                        @if($sv->height)
                        <div class="col-6 col-sm-3">
                            <div class="detail-lbl">Height</div>
                            <span class="fw-semibold">{{ $sv->height }} cm</span>
                        </div>
                        @endif
                        @if($sv->weight)
                        <div class="col-6 col-sm-3">
                            <div class="detail-lbl">Weight</div>
                            <span class="fw-semibold">{{ $sv->weight }} kg</span>
                        </div>
                        @endif
                        @if($sv->bp_systolic && $sv->bp_diastolic)
                        <div class="col-6 col-sm-3">
                            <div class="detail-lbl">Admission BP</div>
                            <span class="fw-semibold text-danger">{{ $sv->bp_systolic }}/{{ $sv->bp_diastolic }} mmHg</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- History --}}
            <div class="card section-card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-header-icon" style="background:#eff6ff;color:#2563eb;">
                        <i class="bi bi-clock-history"></i>
                    </span>
                    <span class="fw-semibold" style="font-size:.88rem;">History</span>
                </div>
                <div class="card-body p-3">
                    @php $hasHist = false; @endphp
                    @foreach($svHistFields as $hKey => $hLabel)
                        @php $vals = $svNote ? ($svNote->{$hKey} ?? []) : []; @endphp
                        @if(count($vals))
                            @php $hasHist = true; @endphp
                            <div class="mb-2">
                                <div class="detail-lbl">{{ $hLabel }}</div>
                                @foreach($vals as $v)
                                    <span class="tag-badge tag-neutral">{{ $v }}</span>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                    @if(!$hasHist)<span class="empty-note">No history recorded.</span>@endif
                </div>
            </div>

            {{-- Examination --}}
            <div class="card section-card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-header-icon" style="background:#f0fdf4;color:#059669;">
                        <i class="bi bi-stethoscope"></i>
                    </span>
                    <span class="fw-semibold" style="font-size:.88rem;">Examination</span>
                </div>
                <div class="card-body p-3">
                    @php $hasExamDetail = false; @endphp
                    @if($svNote?->pulse_rate)
                        @php $hasExamDetail = true; @endphp
                        <div class="mb-2">
                            <div class="detail-lbl">Pulse Rate</div>
                            <span class="tag-badge" style="background:#fef2f2;color:#991b1b;border-color:#fecaca;">
                                <i class="bi bi-heart-pulse me-1"></i>{{ $svNote->pulse_rate }} bpm
                            </span>
                        </div>
                    @endif
                    @if($sv->bpReadings->isNotEmpty())
                        @php $hasExamDetail = true; @endphp
                        <div class="mb-2">
                            <div class="detail-lbl">Blood Pressure</div>
                            @foreach($sv->bpReadings as $bp)
                                <span class="tag-badge" style="background:#fef2f2;color:#991b1b;border-color:#fecaca;">
                                    <i class="bi bi-activity me-1"></i>{{ $bp->systolic }}/{{ $bp->diastolic }} mmHg
                                    <span class="text-muted ms-1" style="font-size:.7rem;">{{ $bp->recorded_at->format('d M H:i') }}</span>
                                </span>
                            @endforeach
                        </div>
                    @endif
                    @foreach($svExamFields as $exKey => [$exLabel, $exIcon, $exColor])
                        @php $vals = $svNote ? ($svNote->{$exKey} ?? []) : []; @endphp
                        @if(count($vals))
                            @php $hasExamDetail = true; @endphp
                            <div class="mb-2">
                                <div class="detail-lbl">
                                    <i class="bi {{ $exIcon }} me-1" style="color:{{ $exColor }};"></i>{{ $exLabel }}
                                </div>
                                @foreach($vals as $v)
                                    <span class="tag-badge tag-exam">{{ $v }}</span>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                    @if(!$hasExamDetail)<span class="empty-note">No examination findings recorded.</span>@endif
                </div>
            </div>

            {{-- Investigations --}}
            @if($sv->investigations->isNotEmpty())
            <div class="card section-card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-header-icon" style="background:#fffbeb;color:#d97706;">
                        <i class="bi bi-eyedropper"></i>
                    </span>
                    <span class="fw-semibold" style="font-size:.88rem;">Investigations</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 inv-table">
                        <thead><tr><th class="px-3 py-2">Investigation</th><th class="py-2">Value</th><th class="py-2">Date &amp; Time</th></tr></thead>
                        <tbody>
                            @foreach($sv->investigations as $inv)
                            <tr>
                                <td class="px-3">{{ $inv->name }}</td>
                                <td class="fw-bold" style="color:#059669;">{{ $inv->value }}</td>
                                <td class="text-muted">{{ $inv->recorded_at->format('d M Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Clinic Drugs --}}
            @if($svClinicDrugs->isNotEmpty())
            <div class="card section-card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-header-icon" style="background:#f0f9ff;color:#0369a1;">
                        <i class="bi bi-capsule"></i>
                    </span>
                    <span class="fw-semibold" style="font-size:.88rem;">Clinic Drugs</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th class="px-3">Route</th><th>Drug</th><th>Dose</th><th>Unit</th><th>Freq.</th><th>Duration</th></tr></thead>
                        <tbody>
                            @foreach($svClinicDrugs as $drug)
                            <tr class="drug-row-body">
                                <td class="px-3"><span class="tag-badge tag-drug mb-0" style="font-size:.7rem;">{{ $drug->type }}</span></td>
                                <td class="fw-semibold" style="color:#0c4a6e;">{{ $drug->name }}</td>
                                <td>{{ $drug->dose }}</td>
                                <td>{{ $drug->unit }}</td>
                                <td>{{ $drug->frequency }}</td>
                                <td>{{ $drug->duration ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Management --}}
            @if($svMgmtDrugs->isNotEmpty() || count($svNote?->management_instruction ?? []))
            <div class="card section-card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-header-icon" style="background:#fdf4ff;color:#7c3aed;">
                        <i class="bi bi-clipboard2-check"></i>
                    </span>
                    <span class="fw-semibold" style="font-size:.88rem;">Management</span>
                </div>
                <div class="card-body p-3">
                    @if($svMgmtDrugs->isNotEmpty())
                        <div class="summary-lbl mb-2">Drugs</div>
                        <table class="table table-sm mb-3">
                            <thead class="table-light"><tr><th>Route</th><th>Drug</th><th>Dose</th><th>Unit</th><th>Freq.</th><th>Duration</th></tr></thead>
                            <tbody>
                                @foreach($svMgmtDrugs as $drug)
                                <tr class="drug-row-body">
                                    <td><span class="tag-badge tag-drug mb-0" style="font-size:.7rem;">{{ $drug->type }}</span></td>
                                    <td class="fw-semibold" style="color:#0c4a6e;">{{ $drug->name }}</td>
                                    <td>{{ $drug->dose }}</td>
                                    <td>{{ $drug->unit }}</td>
                                    <td>{{ $drug->frequency }}</td>
                                    <td>{{ $drug->duration ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    @php $instrs = $svNote?->management_instruction ?? []; @endphp
                    @if(count($instrs))
                        <div class="summary-lbl mb-2">Instructions</div>
                        @foreach($instrs as $instr)
                            <span class="tag-badge" style="background:#fdf4ff;color:#6b21a8;border-color:#e9d5ff;">{{ $instr }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
            @endif

            @else
            {{-- No visit selected --}}
            <div class="card section-card">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-arrow-left-circle" style="font-size:2.5rem;opacity:.15;"></i>
                    <p class="mt-3 mb-0 small">Select a visit from the list to view details.</p>
                </div>
            </div>
            @endif
        </div>{{-- /detail panel --}}
    </div>{{-- /row --}}
    @endif

</div>{{-- /visits-pane --}}
</div>{{-- /tab-content --}}

@endsection

@push('scripts')
@if($allBpReadings->isNotEmpty() || $allInvs->isNotEmpty())
<script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
@endif
@if($allBpReadings->isNotEmpty())
<script>
(function () {
    var ctx = document.getElementById('bp-chart');
    if (!ctx) return;

    var labels    = @json($allBpReadings->map(fn($bp) => $bp->recorded_at->format('d M'))->values());
    var systolic  = @json($allBpReadings->map(fn($bp) => (int) $bp->systolic)->values());
    var diastolic = @json($allBpReadings->map(fn($bp) => (int) $bp->diastolic)->values());

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Systolic',
                    data: systolic,
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220,38,38,.08)',
                    borderWidth: 2,
                    pointBackgroundColor: '#dc2626',
                    pointRadius: 3,
                    fill: true,
                    tension: .35,
                },
                {
                    label: 'Diastolic',
                    data: diastolic,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,.06)',
                    borderWidth: 2,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 3,
                    fill: true,
                    tension: .35,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ': ' + ctx.parsed.y + ' mmHg';
                        }
                    }
                }
            },
            scales: {
                y: {
                    min: 40,
                    grid: { color: 'rgba(0,0,0,.04)' },
                    ticks: { font: { size: 10 } },
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, maxRotation: 45 },
                }
            }
        }
    });
})();
</script>
@endif

@if($allInvs->isNotEmpty())
<script>
(function () {
    var palette = [
        '#0ea5e9','#10b981','#f59e0b','#8b5cf6','#ef4444',
        '#06b6d4','#84cc16','#f97316','#ec4899','#6366f1',
    ];

    document.querySelectorAll('.inv-mini-chart').forEach(function (canvas, idx) {
        var labels = JSON.parse(canvas.dataset.labels || '[]');
        var values = JSON.parse(canvas.dataset.values || '[]');
        var name   = canvas.dataset.name || '';
        if (!labels.length || !values.length) return;

        var color = palette[idx % palette.length];
        var colorBg = color + '18'; // hex alpha ~10%

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: name,
                    data: values,
                    borderColor: color,
                    backgroundColor: colorBg,
                    borderWidth: 2,
                    pointBackgroundColor: color,
                    pointRadius: labels.length > 8 ? 2 : 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: function (items) { return items[0].label; },
                            label: function (item) { return name + ': ' + item.raw; },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9 }, maxRotation: 45, maxTicksLimit: 6 },
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,.04)' },
                        ticks: { font: { size: 9 }, maxTicksLimit: 4 },
                    },
                },
            },
        });
    });
})();
</script>
@endif

{{-- Activate visits tab if URL has #visits-tab or ?visit= ─────────────── --}}
<script>
(function () {
    var hasVisitParam = new URLSearchParams(window.location.search).get('visit');
    if (window.location.hash === '#visits-tab' || hasVisitParam) {
        var t = document.getElementById('visits-tab');
        if (t) t.click();
    }
})();
</script>
@endpush
