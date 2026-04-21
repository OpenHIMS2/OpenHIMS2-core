@php
/*
 * GMC Queue Partial — 4 tabs (New Clinic, Recurrent, OPD, Urgent)
 * $grouped  : ClinicVisit collection keyed by category
 * $unitView : UnitView model (used for routes + role detection)
 */

$cats = [
    'new_clinic_visit'       => ['label' => 'New Clinic',  'short' => 'New Clinic',  'icon' => 'bi-person-plus-fill',  'color' => '#dc2626', 'bg' => '#fff1f2', 'border' => '#fecaca', 'tabId' => 'qt-new'],
    'recurrent_clinic_visit' => ['label' => 'Recurrent',   'short' => 'Recurrent',   'icon' => 'bi-arrow-repeat',       'color' => '#1d4ed8', 'bg' => '#eff6ff', 'border' => '#bfdbfe', 'tabId' => 'qt-rec'],
    'opd'                    => ['label' => 'OPD',          'short' => 'OPD',          'icon' => 'bi-hospital',           'color' => '#15803d', 'bg' => '#f0fdf4', 'border' => '#bbf7d0', 'tabId' => 'qt-opd'],
    'urgent'                 => ['label' => 'Urgent',       'short' => 'Urgent',       'icon' => 'bi-person-badge-fill',  'color' => '#b45309', 'bg' => '#fffbeb', 'border' => '#fde68a', 'tabId' => 'qt-urg'],
];

$bladePath = $unitView->viewTemplate->blade_path ?? '';
$isDoctor  = str_contains($bladePath, 'doctor');
$isNurse   = str_contains($bladePath, 'nurse');
$canClick  = $isDoctor || $isNurse;

$totalWaiting    = $grouped->flatten()->where('status', 'waiting')->count();
$totalInProgress = $grouped->flatten()->where('status', 'in_progress')->count();

// Default to the tab with the most patients; fall back to first category
$defaultCat = collect($cats)->keys()->first();
$maxCount   = 0;
foreach ($cats as $key => $cat) {
    $cnt = $grouped->get($key, collect())->count();
    if ($cnt > $maxCount) { $maxCount = $cnt; $defaultCat = $key; }
}
@endphp

{{-- ── Stats strip ────────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center gap-2 px-2 pt-2 pb-1 flex-wrap">
    <span class="badge rounded-pill px-2 py-1" style="background:#f0f9ff;color:#0369a1;border:1px solid #bae6fd;font-size:.68rem;">
        <i class="bi bi-hourglass-split me-1"></i>{{ $totalWaiting }} Waiting
    </span>
    @if($totalInProgress > 0)
    <span class="badge rounded-pill px-2 py-1" style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;font-size:.68rem;">
        <span style="animation:blink 1s ease-in-out infinite;display:inline-block;">●</span>
        {{ $totalInProgress }} In Progress
    </span>
    @endif
    <span class="ms-auto text-muted" style="font-size:.65rem;">
        <i class="bi bi-arrow-clockwise me-1"></i>{{ now()->format('H:i:s') }}
    </span>
</div>

{{-- ── 4 Tab Buttons ───────────────────────────────────────────────────────── --}}
<div class="d-flex gap-1 px-2 py-2">
    @foreach($cats as $key => $cat)
    @php $count = $grouped->get($key, collect())->count(); @endphp
    <button type="button"
            class="queue-tab-btn flex-fill text-center border rounded-2 py-2 px-0"
            data-target="{{ $cat['tabId'] }}"
            data-color="{{ $cat['color'] }}"
            data-bg="{{ $cat['bg'] }}"
            data-border="{{ $cat['border'] }}"
            style="background:{{ $key === $defaultCat ? $cat['bg'] : '#f9fafb' }};
                   border-color:{{ $key === $defaultCat ? $cat['color'] : '#e5e7eb' }} !important;
                   cursor:pointer;transition:all .15s;">
        <div class="fw-black font-monospace" style="font-size:1.25rem;color:{{ $cat['color'] }};line-height:1.1;">
            {{ $count }}
        </div>
        <div style="font-size:.58rem;color:#6b7280;line-height:1.3;margin-top:.1rem;">
            <i class="bi {{ $cat['icon'] }}" style="color:{{ $cat['color'] }};"></i>
            <br>{{ $cat['short'] }}
        </div>
    </button>
    @endforeach
</div>

{{-- ── Tab Content Panels ──────────────────────────────────────────────────── --}}
<div style="max-height:54vh;overflow-y:auto;padding-bottom:.5rem;">
    @foreach($cats as $key => $cat)
    @php $items = $grouped->get($key, collect()); @endphp
    <div id="{{ $cat['tabId'] }}" class="queue-tab-panel" style="{{ $key === $defaultCat ? '' : 'display:none;' }}">
        @if($items->isEmpty())
            <div class="text-center py-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                     style="width:3rem;height:3rem;background:{{ $cat['bg'] }};">
                    <i class="bi {{ $cat['icon'] }}" style="color:{{ $cat['color'] }};font-size:1.2rem;"></i>
                </div>
                <p class="text-muted small mb-0">No {{ strtolower($cat['label']) }} patients</p>
            </div>
        @else
            <ul class="list-unstyled mb-0 px-2">
                @foreach($items as $visit)
                <li class="queue-patient-row py-2 rounded-2 px-2 mb-1"
                    @if($canClick)
                        data-href="@if($isDoctor && $visit->status === 'in_progress'){{ route('clinical.doctor.visit-page', [$unitView->id, $visit->id]) }}@elseif($isDoctor){{ route('clinical.doctor.patient-history', [$unitView->id, $visit->patient_id]) }}@elseif($isNurse){{ route('clinical.nurse.patient-history', [$unitView->id, $visit->patient_id]) }}@endif"
                    @endif
                    style="{{ $visit->status === 'in_progress' ? 'background:'.$cat['bg'].';' : '' }}
                           {{ $canClick ? 'cursor:pointer;' : '' }}
                           transition:background .1s;">
                    <div class="d-flex align-items-center gap-2">
                        {{-- Visit number badge --}}
                        <div class="rounded-2 d-flex align-items-center justify-content-center fw-black font-monospace flex-shrink-0"
                             style="width:2.4rem;height:2.4rem;background:{{ $cat['bg'] }};color:{{ $cat['color'] }};border:1.5px solid {{ $cat['border'] }};font-size:.95rem;">
                            {{ $visit->visit_number }}
                        </div>
                        {{-- Patient details --}}
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-dark text-truncate" style="font-size:.83rem;">
                                {{ $visit->patient->name }}
                            </div>
                            <div style="font-size:.7rem;color:#9ca3af;">
                                {{ $visit->patient->phn ?? '—' }}
                                @if($visit->patient->computed_age)
                                    · {{ $visit->patient->computed_age }}y
                                    / {{ Str::upper(substr($visit->patient->gender ?? '', 0, 1)) }}
                                @endif
                            </div>
                            {{-- Extra fields --}}
                            @if($visit->bp_systolic && $visit->bp_diastolic)
                                <div style="font-size:.68rem;color:#9ca3af;">
                                    <i class="bi bi-activity me-1"></i>{{ $visit->bp_systolic }}/{{ $visit->bp_diastolic }} mmHg
                                </div>
                            @endif
                            @if($visit->clinic_number)
                                <div style="font-size:.68rem;color:#9ca3af;">Clinic#: {{ $visit->clinic_number }}</div>
                            @endif
                            @if($visit->opd_number)
                                <div style="font-size:.68rem;color:#9ca3af;">OPD#: {{ $visit->opd_number }}</div>
                            @endif
                        </div>
                        {{-- Status + remove --}}
                        <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0">
                            @if($visit->status === 'in_progress')
                                <span class="badge px-2 py-1 rounded-pill"
                                      style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;font-size:.6rem;">
                                    <span style="animation:blink 1s ease-in-out infinite;display:inline-block;">●</span> In&nbsp;Progress
                                </span>
                            @else
                                <span class="badge px-2 py-1 rounded-pill"
                                      style="background:#f0f9ff;color:#0369a1;border:1px solid #bae6fd;font-size:.6rem;">
                                    Waiting
                                </span>
                                <button class="btn p-0 border-0"
                                        style="color:#f87171;font-size:.9rem;line-height:1;"
                                        title="Remove from queue"
                                        onclick="event.stopPropagation();
                                                 window.removeFromQueue('{{ route('clinical.patients.remove-from-queue', [$unitView->id, $visit->id]) }}')">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        @endif
    </div>
    @endforeach
</div>
