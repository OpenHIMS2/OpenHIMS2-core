@extends('layouts.clinical')
@section('title', 'Reports')

@push('styles')
<style>
.report-card {
    cursor: pointer;
    border: 1px solid #e9ecef;
    border-radius: .75rem;
    transition: box-shadow .15s, border-color .15s, transform .12s;
    background: #fff;
}
.report-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.10);
    border-color: #c2d6ff;
    transform: translateY(-2px);
}
.report-icon-box {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.section-label {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #6c757d;
}
/* Patient search modal */
.patient-result-row {
    cursor: pointer;
    transition: background .1s;
}
.patient-result-row:hover { background: #f1f3f5; }

/* Condition list */
.condition-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .35rem .5rem;
    border: 1px solid #e9ecef;
    border-radius: .4rem;
    background: #f8f9fa;
    margin-bottom: .3rem;
}
.condition-item .remove-condition {
    margin-left: auto;
    cursor: pointer;
    color: #dc3545;
    font-size: .85rem;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div style="max-width:960px; margin:0 auto;">

    {{-- Page header --}}
    <div class="d-flex align-items-center mb-4">
        <div class="me-3" style="width:48px;height:48px;border-radius:14px;background:#fff3e0;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#e65100;">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold">Reports</h4>
            <p class="mb-0 text-muted" style="font-size:.85rem;">Generate and print clinical reports and letters</p>
        </div>
    </div>

    {{-- ── Individual Reports ── --}}
    <div class="mb-2">
        <span class="section-label"><i class="bi bi-person-lines-fill me-1"></i>Individual Reports</span>
    </div>
    <div class="row g-3 mb-4">
        {{-- Clinic Confirmation Letter --}}
        <div class="col-md-4 col-sm-6">
            <div class="report-card p-3 h-100" data-bs-toggle="modal" data-bs-target="#letterModal">
                <div class="d-flex align-items-start gap-3">
                    <div class="report-icon-box" style="background:#e3f2fd; color:#1565c0;">
                        <i class="bi bi-envelope-paper-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1" style="font-size:.95rem;">Clinic Confirmation Letter</div>
                        <div class="text-muted" style="font-size:.78rem;">Generates an official letter confirming a patient's registration and medical conditions at this clinic.</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Placeholder for future reports --}}
        <div class="col-md-4 col-sm-6">
            <div class="report-card p-3 h-100 opacity-50" style="cursor:default;">
                <div class="d-flex align-items-start gap-3">
                    <div class="report-icon-box" style="background:#f3e5f5; color:#6a1b9a;">
                        <i class="bi bi-file-medical-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1" style="font-size:.95rem;">Referral Letter <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Coming Soon</span></div>
                        <div class="text-muted" style="font-size:.78rem;">Generate a referral letter to another hospital or specialist.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Aggregated Reports ── --}}
    <div class="mb-2">
        <span class="section-label"><i class="bi bi-bar-chart-fill me-1"></i>Aggregated Reports</span>
    </div>
    <div class="row g-3">
        {{-- Monthly Clinic Report --}}
        <div class="col-md-4 col-sm-6">
            <div class="report-card p-3 h-100" data-bs-toggle="modal" data-bs-target="#monthlyReportModal">
                <div class="d-flex align-items-start gap-3">
                    <div class="report-icon-box" style="background:#e8f5e9; color:#2e7d32;">
                        <i class="bi bi-calendar2-month-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1" style="font-size:.95rem;">Monthly Clinic Report</div>
                        <div class="text-muted" style="font-size:.78rem;">Attendance summary, visit breakdown by category, and daily statistics for a selected month.</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Placeholder --}}
        <div class="col-md-4 col-sm-6">
            <div class="report-card p-3 h-100 opacity-50" style="cursor:default;">
                <div class="d-flex align-items-start gap-3">
                    <div class="report-icon-box" style="background:#fff3e0; color:#e65100;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1" style="font-size:.95rem;">Disease Trend Report <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Coming Soon</span></div>
                        <div class="text-muted" style="font-size:.78rem;">Visualise trends in diagnoses and presenting complaints over time.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════
     Clinic Confirmation Letter Modal
═══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="letterModal" tabindex="-1" aria-labelledby="letterModalLabel">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#e3f2fd;">
                <h6 class="modal-title fw-bold" id="letterModalLabel">
                    <i class="bi bi-envelope-paper-fill me-2 text-primary"></i>Clinic Confirmation Letter
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Step 1: Patient Search --}}
                <div id="letterStep1">
                    <p class="text-muted mb-3" style="font-size:.87rem;">Search for a patient by name, PHN, NIC, or mobile number.</p>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="letterPatientSearch"
                               class="form-control"
                               placeholder="Type name, PHN, NIC or mobile…"
                               autocomplete="off">
                    </div>
                    <div id="letterSearchResults" class="border rounded" style="display:none; max-height:260px; overflow-y:auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Name</th>
                                    <th>PHN</th>
                                    <th>NIC</th>
                                    <th>Age</th>
                                    <th>Sex</th>
                                </tr>
                            </thead>
                            <tbody id="letterSearchBody"></tbody>
                        </table>
                    </div>
                    <div id="letterSearchSpinner" class="text-center py-3 d-none">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <span class="ms-2 text-muted small">Searching…</span>
                    </div>
                    <div id="letterNoResults" class="text-center text-muted py-3 d-none" style="font-size:.85rem;">
                        <i class="bi bi-person-x me-1"></i>No patients found.
                    </div>
                </div>

                {{-- Step 2: Editable Patient Form (hidden until patient selected) --}}
                <div id="letterStep2" class="d-none">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="bi bi-person-check-fill text-success fs-5"></i>
                        <span class="fw-semibold" id="letterSelectedName">—</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" id="letterChangeBtn">
                            <i class="bi bi-arrow-left me-1"></i>Change Patient
                        </button>
                    </div>

                    <form id="letterPrintForm" method="POST"
                          action="{{ route('reports.clinic-confirmation-letter') }}"
                          target="_blank">
                        @csrf

                        {{-- Demographics --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body py-2">
                                <div class="fw-semibold text-uppercase mb-2" style="font-size:.72rem; letter-spacing:.07em; color:#6c757d;">
                                    <i class="bi bi-person-fill me-1"></i>Patient Demographics
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm mb-1">Full Name</label>
                                        <input type="text" name="patient_name" id="lf_name" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm mb-1">Age (years)</label>
                                        <input type="text" name="patient_age" id="lf_age" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm mb-1">Sex</label>
                                        <input type="text" name="patient_gender" id="lf_gender" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm mb-1">Address</label>
                                        <textarea name="patient_address" id="lf_address" class="form-control form-control-sm" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm mb-1">Mobile</label>
                                        <input type="text" name="patient_mobile" id="lf_mobile" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm mb-1">PHN</label>
                                        <input type="text" name="patient_phn" id="lf_phn" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm mb-1">NIC</label>
                                        <input type="text" name="patient_nic" id="lf_nic" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Medical Conditions --}}
                        <div class="card border-0 bg-light mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="fw-semibold text-uppercase me-2" style="font-size:.72rem; letter-spacing:.07em; color:#6c757d;">
                                        <i class="bi bi-clipboard2-pulse-fill me-1"></i>Medical Conditions
                                    </span>
                                    <span class="text-muted" style="font-size:.72rem;">(remove conditions not relevant for this letter)</span>
                                </div>

                                <div id="conditionsList"></div>

                                {{-- Hidden inputs populated dynamically before print --}}
                                <div id="conditionHiddenInputs"></div>

                                <div id="noConditionsMsg" class="text-muted text-center py-2 d-none" style="font-size:.82rem;">
                                    <i class="bi bi-info-circle me-1"></i>No past medical history recorded for this patient.
                                </div>

                                {{-- Add custom condition --}}
                                <div class="d-flex gap-2 mt-2">
                                    <input type="text" id="newConditionName" class="form-control form-control-sm" placeholder="Add condition…" style="flex:2;">
                                    <input type="number" id="newConditionYear" class="form-control form-control-sm" placeholder="Year" style="flex:1; max-width:90px;">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addConditionBtn">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary d-none" id="letterPrintBtn">
                    <i class="bi bi-printer-fill me-1"></i>Generate & Print Letter
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════
     Monthly Clinic Report Modal
═══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="monthlyReportModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background:#e8f5e9;">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-calendar2-month-fill me-2 text-success"></i>Monthly Clinic Report
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reports.monthly-clinic') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:.87rem;">Select the month and unit to generate the report.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label form-label-sm mb-1">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select form-select-sm" required>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ date('F', mktime(0,0,0,$m,1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-sm mb-1">Year <span class="text-danger">*</span></label>
                            <select name="year" class="form-select form-select-sm" required>
                                @foreach(range(now()->year, now()->year - 3) as $y)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($units->isNotEmpty())
                        <div class="col-12">
                            <label class="form-label form-label-sm mb-1">Unit <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select form-select-sm" required>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-printer-fill me-1"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Patient search for Confirmation Letter ──────────────────────────────────
let searchTimeout = null;
let conditions    = [];   // [{condition, year}]

const searchInput    = document.getElementById('letterPatientSearch');
const resultsBox     = document.getElementById('letterSearchResults');
const resultsBody    = document.getElementById('letterSearchBody');
const spinner        = document.getElementById('letterSearchSpinner');
const noResults      = document.getElementById('letterNoResults');
const step1          = document.getElementById('letterStep1');
const step2          = document.getElementById('letterStep2');
const selectedName   = document.getElementById('letterSelectedName');
const changeBtn      = document.getElementById('letterChangeBtn');
const printBtn       = document.getElementById('letterPrintBtn');
const conditionsList = document.getElementById('conditionsList');
const noCondMsg      = document.getElementById('noConditionsMsg');

searchInput.addEventListener('input', function () {
    const q = this.value.trim();
    clearTimeout(searchTimeout);

    if (q.length < 2) {
        resultsBox.style.display = 'none';
        spinner.classList.add('d-none');
        noResults.classList.add('d-none');
        return;
    }

    spinner.classList.remove('d-none');
    resultsBox.style.display = 'none';
    noResults.classList.add('d-none');

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('reports.patient-search') }}?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            spinner.classList.add('d-none');
            if (data.length === 0) {
                noResults.classList.remove('d-none');
                return;
            }
            resultsBody.innerHTML = data.map(p => `
                <tr class="patient-result-row" data-id="${p.id}" data-name="${escHtml(p.name)}">
                    <td class="py-1">${escHtml(p.name)}</td>
                    <td class="py-1 text-muted small">${p.phn}</td>
                    <td class="py-1 text-muted small">${p.nic}</td>
                    <td class="py-1">${p.age}</td>
                    <td class="py-1">${p.gender}</td>
                </tr>
            `).join('');
            resultsBox.style.display = '';
        })
        .catch(() => { spinner.classList.add('d-none'); });
    }, 350);
});

resultsBody.addEventListener('click', function (e) {
    const row = e.target.closest('.patient-result-row');
    if (!row) return;
    loadPatient(row.dataset.id, row.dataset.name);
});

function loadPatient(id, name) {
    selectedName.textContent = name;
    resultsBox.style.display = 'none';
    spinner.classList.remove('d-none');

    fetch(`{{ url('reports/patient') }}/${id}/letter-data`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        spinner.classList.add('d-none');
        // Fill form fields
        document.getElementById('lf_name').value    = data.name    || '';
        document.getElementById('lf_age').value     = data.age     || '';
        document.getElementById('lf_gender').value  = data.gender  || '';
        document.getElementById('lf_address').value = data.address || '';
        document.getElementById('lf_mobile').value  = data.mobile  || '';
        document.getElementById('lf_phn').value     = data.phn     || '';
        document.getElementById('lf_nic').value     = data.nic     || '';

        // Load conditions
        conditions = data.conditions || [];
        renderConditions();

        step1.classList.add('d-none');
        step2.classList.remove('d-none');
        printBtn.classList.remove('d-none');
    })
    .catch(() => { spinner.classList.add('d-none'); });
}

changeBtn.addEventListener('click', () => {
    step1.classList.remove('d-none');
    step2.classList.add('d-none');
    printBtn.classList.add('d-none');
    searchInput.value = '';
    searchInput.focus();
});

// Reset modal on close
document.getElementById('letterModal').addEventListener('hidden.bs.modal', () => {
    step1.classList.remove('d-none');
    step2.classList.add('d-none');
    printBtn.classList.add('d-none');
    searchInput.value = '';
    resultsBox.style.display = 'none';
    conditions = [];
});

function renderConditions() {
    if (conditions.length === 0) {
        conditionsList.innerHTML = '';
        noCondMsg.classList.remove('d-none');
    } else {
        noCondMsg.classList.add('d-none');
        conditionsList.innerHTML = conditions.map((c, i) => `
            <div class="condition-item" data-index="${i}">
                <i class="bi bi-circle-fill text-primary" style="font-size:.45rem;flex-shrink:0;"></i>
                <span style="font-size:.88rem;">${escHtml(c.condition)}</span>
                ${c.year ? `<span class="text-muted ms-1" style="font-size:.78rem;">(since ${c.year})</span>` : ''}
                <i class="bi bi-x-circle-fill remove-condition" data-index="${i}" title="Remove"></i>
            </div>
        `).join('');

        conditionsList.querySelectorAll('.remove-condition').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.index);
                conditions.splice(idx, 1);
                renderConditions();
            });
        });
    }
}

// Add custom condition
document.getElementById('addConditionBtn').addEventListener('click', () => {
    const name = document.getElementById('newConditionName').value.trim();
    const year = document.getElementById('newConditionYear').value.trim();
    if (!name) return;
    conditions.push({ condition: name, year: year || '' });
    renderConditions();
    document.getElementById('newConditionName').value = '';
    document.getElementById('newConditionYear').value = '';
});

// Print: build hidden inputs then submit
printBtn.addEventListener('click', () => {
    // Remove old hidden inputs
    const container = document.getElementById('conditionHiddenInputs');
    container.innerHTML = '';
    conditions.forEach(c => {
        container.insertAdjacentHTML('beforeend',
            `<input type="hidden" name="condition_name[]" value="${escAttr(c.condition)}">
             <input type="hidden" name="condition_year[]" value="${escAttr(String(c.year || ''))}">`
        );
    });
    document.getElementById('letterPrintForm').submit();
});

function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}
function escAttr(str) {
    return String(str).replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
</script>
@endpush
