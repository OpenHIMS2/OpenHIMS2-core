@php
$categories = [
    'opd'                    => ['label' => 'OPD Patient',             'icon' => 'bi-hospital',          'color' => 'primary'],
    'new_clinic_visit'       => ['label' => 'New Clinic Visit',        'icon' => 'bi-person-plus-fill',  'color' => 'success'],
    'recurrent_clinic_visit' => ['label' => 'Recurrent Clinic Visit',  'icon' => 'bi-arrow-repeat',      'color' => 'info'],
    'urgent'                 => ['label' => 'Urgent Patient',          'icon' => 'bi-person-badge-fill', 'color' => 'danger'],
];
$totalCount = $grouped->flatten()->count();
@endphp

@if($totalCount === 0)
    <div class="text-center py-4 text-muted">
        <i class="bi bi-people" style="font-size:2rem;opacity:.3;"></i>
        <p class="mt-2 mb-0 small">No patients in queue</p>
    </div>
@else
    <div class="accordion accordion-flush" id="queue-accordion">
        @foreach($categories as $key => $cat)
            @php $items = $grouped->get($key, collect()); @endphp
            @if($items->isNotEmpty())
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header">
                    <button class="accordion-button py-2 px-0"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#cat-{{ $key }}"
                            aria-expanded="true">
                        <span class="d-flex align-items-center gap-2 small fw-semibold">
                            <i class="bi {{ $cat['icon'] }} text-{{ $cat['color'] }}"></i>
                            {{ $cat['label'] }}
                            <span class="badge bg-{{ $cat['color'] }} rounded-pill">{{ $items->count() }}</span>
                        </span>
                    </button>
                </h2>
                <div id="cat-{{ $key }}" class="accordion-collapse collapse show">
                    <div class="accordion-body px-0 pt-0 pb-1">
                        <ul class="list-group list-group-flush">
                            @foreach($items as $visit)
                            <li class="list-group-item px-0 py-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-{{ $cat['color'] }} rounded-pill fw-bold"
                                          style="min-width:2rem;font-size:.8rem;">
                                        {{ $visit->visit_number }}
                                    </span>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="fw-medium small text-truncate"
                                             title="{{ $visit->patient->name }}">
                                            {{ $visit->patient->name }}
                                        </div>
                                        <small class="text-muted">{{ $visit->patient->phn ?? '—' }}</small>
                                        {{-- Extra visit detail --}}
                                        @if($visit->category === 'opd' && $visit->opd_number)
                                            <small class="text-muted d-block">OPD#: {{ $visit->opd_number }}</small>
                                        @endif
                                        @if(in_array($visit->category, ['new_clinic_visit','recurrent_clinic_visit']) && $visit->clinic_number)
                                            <small class="text-muted d-block">Clinic#: {{ $visit->clinic_number }}</small>
                                        @endif
                                        @if($visit->bp_systolic && $visit->bp_diastolic)
                                            <small class="text-muted d-block">BP: {{ $visit->bp_systolic }}/{{ $visit->bp_diastolic }} mmHg</small>
                                        @endif
                                    </div>
                                    @if($visit->status === 'waiting')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle"
                                              style="font-size:.6rem;">Waiting</span>
                                        <button class="btn btn-sm p-0 text-danger border-0"
                                                style="width:1.4rem;height:1.4rem;line-height:1;"
                                                title="Remove from queue"
                                                onclick="window.removeFromQueue(
                                                    '{{ route('clinical.patients.remove-from-queue', [$unitView->id, $visit->id]) }}'
                                                )">
                                            <i class="bi bi-x-circle" style="font-size:.9rem;"></i>
                                        </button>
                                    @elseif($visit->status === 'in_progress')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle"
                                              style="font-size:.6rem;">In Progress</span>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>
    <div class="pt-2 text-end">
        <small class="text-muted">{{ $totalCount }} patient(s) waiting</small>
    </div>
@endif
