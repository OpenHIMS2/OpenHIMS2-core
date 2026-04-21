@php
$cats = [
    'opd'                    => ['label' => 'OPD Patient',                   'icon' => 'bi-hospital',          'bg' => '#eff6ff', 'color' => '#1d4ed8'],
    'new_clinic_visit'       => ['label' => 'New Clinic Visit',              'icon' => 'bi-person-plus-fill',  'bg' => '#f0fdf4', 'color' => '#15803d'],
    'recurrent_clinic_visit' => ['label' => 'Recurrent Clinic Visit',        'icon' => 'bi-arrow-repeat',      'bg' => '#ecfeff', 'color' => '#0e7490'],
    'urgent'                 => ['label' => 'Urgent Patient',               'icon' => 'bi-person-badge-fill', 'bg' => '#fef2f2', 'color' => '#dc2626'],
];

$waitingTotal = $waiting->flatten()->count();
@endphp

{{-- ── Stats strip ────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center gap-2 flex-wrap mb-4">
    <span class="badge rounded-pill px-3 py-2 border" style="background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe !important;font-size:.75rem;">
        <i class="bi bi-hourglass-split me-1"></i>{{ $waitingTotal }} Waiting
    </span>
    <span class="badge rounded-pill px-3 py-2 border" style="background:#fffbeb;color:#d97706;border-color:#fde68a !important;font-size:.75rem;">
        <i class="bi bi-person-fill me-1"></i>{{ $inProgress->count() }} In Progress
    </span>
    <span class="badge rounded-pill px-3 py-2 border" style="background:#f0fdf4;color:#15803d;border-color:#bbf7d0 !important;font-size:.75rem;">
        <i class="bi bi-check2-all me-1"></i>{{ $visitedCount }} Served Today
    </span>
    <span class="ms-auto text-muted" style="font-size:.72rem;">
        <i class="bi bi-arrow-clockwise me-1"></i>Updated {{ now()->format('H:i:s') }}
    </span>
</div>

@if($waitingTotal === 0 && $inProgress->isEmpty())

    {{-- Empty state --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-check2-all d-block mb-3" style="font-size:2.5rem;color:#cbd5e1;"></i>
            <p class="fw-semibold mb-1">Queue is clear</p>
            <p class="small mb-0">
                @if($visitedCount > 0)
                    All {{ $visitedCount }} patient(s) seen today.
                @else
                    No patients registered yet today.
                @endif
            </p>
        </div>
    </div>

@else

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- NOW IN CONSULTATION                                               --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($inProgress->isNotEmpty())
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="blink-dot text-warning">●</span>
            <span class="fw-semibold small text-uppercase" style="letter-spacing:.06em;color:#d97706;">
                Now in Consultation
            </span>
            <span class="badge rounded-pill ms-1" style="background:#fef3c7;color:#d97706;border:1px solid #fcd34d;font-size:.65rem;">
                {{ $inProgress->count() }}
            </span>
        </div>
        <div class="card border-0 shadow-sm overflow-hidden">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr style="background:#fffbeb;border-bottom:2px solid #fcd34d;">
                        <th style="width:4.5rem;padding:.6rem 1rem;color:#92400e;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">No.</th>
                        <th style="padding:.6rem 1rem;color:#92400e;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Patient</th>
                        <th class="d-none d-md-table-cell" style="width:8rem;padding:.6rem 1rem;color:#92400e;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">PHN</th>
                        <th class="d-none d-sm-table-cell" style="width:7rem;padding:.6rem 1rem;color:#92400e;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Age / Sex</th>
                        <th style="width:2.5rem;padding:.6rem 1rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inProgress as $visit)
                    <tr onclick="location.href='{{ route('clinical.doctor.visit-page', [$unitView->id, $visit->id]) }}'"
                        style="cursor:pointer;background:#fff8ed;">
                        <td style="padding:.75rem 1rem;">
                            <span class="fw-black font-monospace" style="font-size:1.6rem;color:#d97706;line-height:1;">
                                {{ $visit->visit_number }}
                            </span>
                        </td>
                        <td style="padding:.75rem 1rem;">
                            <div class="fw-semibold text-dark" style="font-size:.9rem;">{{ $visit->patient->name }}</div>
                            <div class="small text-muted mt-1">
                                <span class="badge px-2 py-1" style="background:#fef3c7;color:#d97706;font-size:.62rem;">
                                    In Progress
                                </span>
                                @php $cat = $cats[$visit->category] ?? null; @endphp
                                @if($cat)
                                    <span class="ms-1" style="color:#9ca3af;font-size:.75rem;">
                                        <i class="bi {{ $cat['icon'] }} me-1"></i>{{ $cat['label'] }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell text-muted small" style="padding:.75rem 1rem;">
                            {{ $visit->patient->phn ?? '—' }}
                        </td>
                        <td class="d-none d-sm-table-cell text-muted small" style="padding:.75rem 1rem;">
                            {{ $visit->patient->computed_age ?? '—' }}y&nbsp;/&nbsp;{{ Str::upper(substr($visit->patient->gender, 0, 1)) }}
                        </td>
                        <td style="padding:.75rem 1rem;">
                            <i class="bi bi-arrow-right text-muted"></i>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- WAITING — one table per category                                  --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @foreach($cats as $key => $cat)
        @php $items = $waiting->get($key, collect()); @endphp
        @if($items->isNotEmpty())
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi {{ $cat['icon'] }}" style="color:{{ $cat['color'] }};font-size:.85rem;"></i>
                <span class="fw-semibold small text-uppercase" style="letter-spacing:.06em;color:{{ $cat['color'] }};">
                    {{ $cat['label'] }}
                </span>
                <span class="badge rounded-pill ms-1"
                      style="background:{{ $cat['bg'] }};color:{{ $cat['color'] }};border:1px solid {{ $cat['color'] }}44;font-size:.65rem;">
                    {{ $items->count() }}
                </span>
            </div>
            <div class="card border-0 shadow-sm overflow-hidden">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr style="background:{{ $cat['bg'] }};border-bottom:2px solid {{ $cat['color'] }}33;">
                            <th style="width:4.5rem;padding:.6rem 1rem;color:{{ $cat['color'] }};font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">No.</th>
                            <th style="padding:.6rem 1rem;color:{{ $cat['color'] }};font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Patient</th>
                            <th class="d-none d-md-table-cell" style="width:8rem;padding:.6rem 1rem;color:{{ $cat['color'] }};font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">PHN</th>
                            <th class="d-none d-sm-table-cell" style="width:7rem;padding:.6rem 1rem;color:{{ $cat['color'] }};font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Age / Sex</th>
                            <th style="width:2.5rem;padding:.6rem 1rem;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $visit)
                        <tr onclick="location.href='{{ route('clinical.doctor.patient-history', [$unitView->id, $visit->patient_id]) }}'"
                            style="cursor:pointer;">
                            <td style="padding:.75rem 1rem;">
                                <span class="fw-black font-monospace" style="font-size:1.6rem;color:{{ $cat['color'] }};line-height:1;">
                                    {{ $visit->visit_number }}
                                </span>
                            </td>
                            <td style="padding:.75rem 1rem;">
                                <div class="fw-semibold text-dark" style="font-size:.9rem;">{{ $visit->patient->name }}</div>
                                @if($visit->patient->nic)
                                    <div class="small text-muted mt-1">{{ $visit->patient->nic }}</div>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell text-muted small" style="padding:.75rem 1rem;">
                                {{ $visit->patient->phn ?? '—' }}
                            </td>
                            <td class="d-none d-sm-table-cell text-muted small" style="padding:.75rem 1rem;">
                                {{ $visit->patient->computed_age ?? '—' }}y&nbsp;/&nbsp;{{ Str::upper(substr($visit->patient->gender, 0, 1)) }}
                            </td>
                            <td style="padding:.75rem 1rem;">
                                <i class="bi bi-arrow-right text-muted"></i>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach

@endif
