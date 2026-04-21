@if($visits->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-person-x" style="font-size:2.5rem; opacity:.3;"></i>
        <p class="mt-2 mb-0">No patients found</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:3rem;">#</th>
                    <th>Name</th>
                    <th>PHN</th>
                    <th>Age / Gender</th>
                    <th>NIC</th>
                    <th>Last Visit</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($visits as $i => $visit)
                    <tr>
                        <td class="text-muted small">{{ $visits->firstItem() + $i }}</td>
                        <td class="fw-medium">
                            <a href="{{ route('clinical.nurse.patient-history', [$unitView->id, $visit->patient_id]) }}"
                               class="text-decoration-none text-body"
                               title="View visit history">
                                {{ $visit->patient->name }}
                                <i class="bi bi-clock-history ms-1 text-muted" style="font-size:.7rem;"></i>
                            </a>
                        </td>
                        <td><code class="small">{{ $visit->patient->phn }}</code></td>
                        <td class="small">
                            {{ $visit->patient->computed_age ?? '—' }}
                            / {{ ucfirst($visit->patient->gender) }}
                        </td>
                        <td class="small text-muted">{{ $visit->patient->nic ?? '—' }}</td>
                        <td class="small">{{ $visit->visit_date->format('d M Y') }}</td>
                        <td>
                            @if($visit->status === 'waiting' && $visit->visit_date->isToday())
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Waiting</span>
                            @elseif($visit->status === 'in_progress' && $visit->visit_date->isToday())
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">In Progress</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Visited</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('clinical.patients.edit', [$unitView->id, $visit->patient_id]) }}"
                                   class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!in_array($visit->patient_id, $inQueueToday))
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary py-0 px-2 open-queue-modal"
                                            data-action="{{ route('clinical.patients.add-to-queue', [$unitView->id, $visit->patient_id]) }}"
                                            data-patient="{{ $visit->patient->name }}"
                                            data-has-clinic-visit="{{ in_array($visit->patient_id, $hasClinicVisit) ? '1' : '0' }}">
                                        <i class="bi bi-plus-circle me-1"></i>Queue
                                    </button>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check2-circle me-1"></i>In queue
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($visits->hasPages())
        <div class="d-flex justify-content-center pt-3">
            {{ $visits->links() }}
        </div>
    @endif
@endif
