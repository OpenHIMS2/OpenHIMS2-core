@extends('layouts.clinical')
@section('title', $pageTitle ?? 'GMC - Nurse View')

@push('styles')
<style>
    /* ── Rose / Pink theme ─────────────────────────────────────────────── */
    :root { --c:#e11d48; --c-light:#fff1f2; --c-mid:#fecdd3; --c-dark:#9f1239; }

    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.2} }

    .role-banner {
        background: linear-gradient(135deg, #be123c 0%, #e11d48 50%, #fb7185 100%);
        border-radius: 1rem; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
        position: relative; overflow: hidden;
    }
    .role-banner::before {
        content:''; position:absolute; top:-40%; right:-3%;
        width:260px; height:260px; background:rgba(255,255,255,.07); border-radius:50%;
    }
    .role-banner::after {
        content:''; position:absolute; bottom:-50%; right:12%;
        width:160px; height:160px; background:rgba(255,255,255,.05); border-radius:50%;
    }

    .gmc-card { border-radius:.875rem !important; border:1px solid #f1f5f9 !important;
                box-shadow:0 1px 4px rgba(0,0,0,.06), 0 6px 20px rgba(0,0,0,.04) !important; }
    .gmc-card-header { background:#fffafa !important; border-bottom:1px solid #fde8e8 !important;
                       border-radius:.875rem .875rem 0 0 !important; }

    .search-wrap .input-group-text { background:#fff; border-color:#e2e8f0; }
    .search-wrap .form-control { border-color:#e2e8f0; }
    .search-wrap .form-control:focus { border-color:var(--c); box-shadow:0 0 0 .2rem rgba(225,29,72,.15); }
    .filter-row .form-select, .filter-row .form-control { font-size:.82rem; border-color:#e2e8f0; }
    .filter-row .form-select:focus, .filter-row .form-control:focus { border-color:var(--c); box-shadow:0 0 0 .2rem rgba(225,29,72,.12); }

    .queue-tab-btn { background:#f9fafb; border-color:#e5e7eb !important; cursor:pointer; transition:all .15s; }
    .queue-tab-btn:hover { background:#f1f5f9; }

    #addToQueueModal .modal-content { border-radius:1rem; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.18); }
    #addToQueueModal .btn-confirm { background:var(--c); color:white; border:none; border-radius:.5rem; font-weight:600; }
    #addToQueueModal .btn-confirm:hover { background:var(--c-dark); }
    .section-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c); }
</style>
@endpush

@section('content')

{{-- ── Role Banner ─────────────────────────────────────────────────────────── --}}
<div class="role-banner">
    <div class="d-flex align-items-center justify-content-between position-relative" style="z-index:1;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:3rem;height:3rem;background:rgba(255,255,255,.18);backdrop-filter:blur(4px);">
                <i class="bi bi-heart-pulse-fill" style="font-size:1.45rem;color:white;"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-white">{{ $viewTemplate->name }}</h5>
                <p class="mb-0 small text-white" style="opacity:.82;">
                    <i class="bi bi-building me-1"></i>{{ $unit->name }}
                    <span class="mx-2" style="opacity:.5;">·</span>
                    <i class="bi bi-geo-alt me-1"></i>{{ $unit->institution->name }}
                </p>
            </div>
        </div>
        <div class="text-end d-none d-sm-block position-relative" style="z-index:1;">
            <div class="small text-white" style="opacity:.75;">{{ now()->format('l') }}</div>
            <div class="fw-bold text-white">{{ now()->format('d M Y') }}</div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- ── LEFT: Patient Records ───────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card gmc-card h-100">
            <div class="card-header gmc-card-header px-4 py-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-people-fill" style="color:var(--c);"></i>
                        <span class="fw-semibold text-dark">Patient Records</span>
                    </div>
                    <a href="{{ route('clinical.patients.create', $unitView->id) }}"
                       class="btn btn-sm fw-semibold"
                       style="background:var(--c);color:white;border-radius:.5rem;">
                        <i class="bi bi-person-plus-fill me-1"></i>Register Patient
                    </a>
                </div>
                <div class="input-group search-wrap mb-2">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-search text-muted" style="font-size:.82rem;"></i>
                    </span>
                    <input type="text" id="search-input"
                           class="form-control border-start-0 border-end-0 ps-1"
                           placeholder="Search by name, NIC, PHN or mobile…" autocomplete="off">
                </div>
                <div class="row g-2 filter-row">
                    <div class="col-sm-4">
                        <input type="date" id="filter-date" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-4">
                        <select id="filter-month" class="form-select form-select-sm">
                            <option value="">— Month —</option>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select id="filter-year" class="form-select form-select-sm">
                            <option value="">— Year —</option>
                            @foreach(range(date('Y'), date('Y')-4) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="patient-list-container" class="p-3">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <p class="mt-2 mb-0 small">Loading…</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Today's Queue ────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card gmc-card">
            <div class="card-header gmc-card-header px-3 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:2rem;height:2rem;background:var(--c-light);">
                            <i class="bi bi-list-ol" style="color:var(--c);font-size:.85rem;"></i>
                        </div>
                        <span class="fw-semibold text-dark">Today's Queue</span>
                    </div>
                    <button class="btn btn-sm btn-outline-danger px-3" style="border-radius:.5rem;"
                            onclick="window.resetQueue()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>New Queue
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="queue-container">
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Add-to-Queue Modal ───────────────────────────────────────────────────── --}}
<div class="modal fade" id="addToQueueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:2.2rem;height:2.2rem;background:var(--c-light);">
                        <i class="bi bi-plus-circle-fill" style="color:var(--c);"></i>
                    </div>
                    <h6 class="modal-title fw-bold mb-0">Add to Today's Queue</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="rounded-2 px-3 py-2 mb-3 d-flex align-items-center gap-2"
                     style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <i class="bi bi-person-circle text-muted"></i>
                    <span class="small text-muted">Patient:</span>
                    <strong id="modal-patient-name" class="small text-dark ms-1"></strong>
                </div>
                <p class="section-label mb-2">Select visit category</p>
                <div class="row g-2" id="modal-category-group">
                    @foreach([
                        'opd'                    => ['OPD Patient',          'bi-hospital',         'primary'],
                        'new_clinic_visit'        => ['New Clinic Visit',     'bi-person-plus-fill', 'danger'],
                        'recurrent_clinic_visit'  => ['Recurrent Visit',      'bi-arrow-repeat',     'primary'],
                        'urgent'                  => ['Urgent Patient',       'bi-person-badge-fill','warning'],
                    ] as $val => [$label, $icon, $color])
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="modal_category"
                                   id="mcat-{{ $val }}" value="{{ $val }}">
                            <label class="btn btn-outline-{{ $color }} w-100 text-start py-2 px-3 small"
                                   for="mcat-{{ $val }}">
                                <i class="bi {{ $icon }} me-2"></i>{{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div id="modal-cat-error" class="text-danger small mt-2" style="display:none;">Please select a category.</div>
                <hr class="my-2">
                <div class="mb-0">
                    <label class="form-label form-label-sm mb-1">Blood Pressure (mmHg)</label>
                    <div class="row g-2 mb-1">
                        <div class="col-6">
                            <input type="number" id="modal-bp-systolic" class="form-control form-control-sm" placeholder="Systolic" min="0" max="300">
                        </div>
                        <div class="col-6">
                            <input type="number" id="modal-bp-diastolic" class="form-control form-control-sm" placeholder="Diastolic" min="0" max="300">
                        </div>
                    </div>
                    <div class="form-text mb-1">Systolic / Diastolic</div>
                    <input type="datetime-local" id="modal-bp-recorded-at" class="form-control form-control-sm">
                    <div class="form-text">BP recorded date &amp; time</div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-confirm px-4" id="modal-confirm-btn">
                    <i class="bi bi-plus-circle me-1"></i>Add to Queue
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const listUrl   = "{{ route('clinical.nurse.patients', $unitView->id) }}";
    const queueUrl  = "{{ route('clinical.gmc.queue', $unitView->id) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function fetchQueue() {
        fetch(queueUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                document.getElementById('queue-container').innerHTML = html;
                bindQueueTabs();
            });
    }

    function bindQueueTabs() {
        document.querySelectorAll('.queue-tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = this.dataset.target;
                const color    = this.dataset.color;
                const bg       = this.dataset.bg;
                const border   = this.dataset.border;

                document.querySelectorAll('.queue-tab-btn').forEach(b => {
                    b.style.background  = '#f9fafb';
                    b.style.borderColor = '#e5e7eb';
                });
                document.querySelectorAll('.queue-tab-panel').forEach(p => {
                    p.style.display = 'none';
                });

                this.style.background  = bg;
                this.style.borderColor = color;
                const panel = document.getElementById(targetId);
                if (panel) panel.style.display = '';
            });
        });

        // Nurse: queue patient rows are clickable
        document.querySelectorAll('.queue-patient-row[data-href]').forEach(row => {
            row.addEventListener('click', function (e) {
                if (!e.target.closest('button')) location.href = this.dataset.href;
            });
        });
    }

    function fetchPatientList() {
        const search = document.getElementById('search-input').value;
        const date   = document.getElementById('filter-date').value;
        const month  = document.getElementById('filter-month').value;
        const year   = document.getElementById('filter-year').value;
        const params = new URLSearchParams({ search, date, month, year });

        document.getElementById('patient-list-container').innerHTML =
            '<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm" role="status"></div></div>';

        fetch(listUrl + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                document.getElementById('patient-list-container').innerHTML = html;
                bindQueueButtons();
            });
    }

    const queueModal      = new bootstrap.Modal(document.getElementById('addToQueueModal'));
    const modalNameEl     = document.getElementById('modal-patient-name');
    const modalCatError   = document.getElementById('modal-cat-error');
    const modalConfirmBtn = document.getElementById('modal-confirm-btn');
    let   pendingActionUrl = null;

    function localDateTimeValue() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        return now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) +
               'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
    }

    document.getElementById('addToQueueModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('modal-bp-systolic').value    = '';
        document.getElementById('modal-bp-diastolic').value   = '';
        document.getElementById('modal-bp-recorded-at').value = '';
    });

    window.openAddToQueueModal = function (btn) {
        pendingActionUrl = btn.dataset.action;
        modalNameEl.textContent = btn.dataset.patient;
        document.querySelectorAll('input[name="modal_category"]').forEach(r => r.checked = false);
        modalCatError.style.display = 'none';
        document.getElementById('modal-bp-recorded-at').value = localDateTimeValue();

        const newClinicWrapper = document.getElementById('mcat-new_clinic_visit').closest('.col-6');
        if (btn.dataset.hasClinicVisit === '1') {
            newClinicWrapper.style.display = 'none';
            document.getElementById('mcat-new_clinic_visit').checked = false;
        } else {
            newClinicWrapper.style.display = '';
        }

        queueModal.show();
    };

    modalConfirmBtn.addEventListener('click', function () {
        const selected = document.querySelector('input[name="modal_category"]:checked');
        if (!selected) { modalCatError.style.display = ''; return; }
        modalCatError.style.display = 'none';
        modalConfirmBtn.disabled = true;

        const body      = new URLSearchParams({ _token: csrfToken, category: selected.value });
        const systolic     = document.getElementById('modal-bp-systolic').value.trim();
        const diastolic    = document.getElementById('modal-bp-diastolic').value.trim();
        const bpRecordedAt = document.getElementById('modal-bp-recorded-at').value.trim();
        if (systolic)     body.append('bp_systolic',     systolic);
        if (diastolic)    body.append('bp_diastolic',    diastolic);
        if (bpRecordedAt) body.append('bp_recorded_at', bpRecordedAt);

        fetch(pendingActionUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest',
                       'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString(),
        })
        .then(r => {
            modalConfirmBtn.disabled = false;
            if (!r.ok) { alert('Failed to add patient to queue.'); return; }
            queueModal.hide();
            fetchQueue();
            fetchPatientList();
        })
        .catch(() => { modalConfirmBtn.disabled = false; alert('Network error.'); });
    });

    function bindQueueButtons() {
        document.querySelectorAll('.open-queue-modal').forEach(btn => {
            btn.addEventListener('click', function () { window.openAddToQueueModal(this); });
        });
    }

    let debounceTimer;
    function debounced() { clearTimeout(debounceTimer); debounceTimer = setTimeout(fetchPatientList, 400); }

    document.getElementById('search-input').addEventListener('input', debounced);
    document.getElementById('filter-date').addEventListener('change', fetchPatientList);
    document.getElementById('filter-month').addEventListener('change', fetchPatientList);
    document.getElementById('filter-year').addEventListener('change', fetchPatientList);

    document.getElementById('filter-date').addEventListener('change', function () {
        if (this.value) { document.getElementById('filter-month').value = ''; document.getElementById('filter-year').value = ''; }
    });
    document.getElementById('filter-month').addEventListener('change', function () { document.getElementById('filter-date').value = ''; });
    document.getElementById('filter-year').addEventListener('change', function () { document.getElementById('filter-date').value = ''; });

    window.removeFromQueue = function (url) {
        confirmDialog({
            title: 'Remove from Queue', body: 'Remove this patient from the queue?',
            confirmText: 'Remove', confirmClass: 'btn-danger',
            icon: 'bi-person-dash-fill text-danger',
        }, () => {
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest',
                           'Content-Type': 'application/x-www-form-urlencoded' },
                body: '_token=' + csrfToken,
            }).then(() => { fetchQueue(); fetchPatientList(); });
        });
    };

    const resetUrl = "{{ route('clinical.patients.reset-queue', $unitView->id) }}";

    window.resetQueue = function () {
        confirmDialog({
            title: 'Create New Queue',
            body: 'All waiting patients will be marked as Visited and queue numbers will restart from 1 for each category. Patients currently in consultation must be ended first.',
            confirmText: 'Create New Queue', confirmClass: 'btn-danger',
            icon: 'bi-arrow-counterclockwise text-danger',
        }, () => {
            fetch(resetUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest',
                           'Content-Type': 'application/x-www-form-urlencoded' },
                body: '_token=' + csrfToken,
            }).then(r => {
                if (!r.ok) {
                    return r.json().then(data => {
                        confirmDialog({
                            title: 'Cannot Create New Queue', body: data.message,
                            confirmText: 'OK', confirmClass: 'btn-secondary',
                            icon: 'bi-exclamation-circle-fill text-danger',
                        }, () => {});
                    });
                }
                fetchQueue(); fetchPatientList();
            });
        });
    };

    fetchPatientList();
    fetchQueue();
    setInterval(fetchQueue, 30000);
})();
</script>
@endpush
