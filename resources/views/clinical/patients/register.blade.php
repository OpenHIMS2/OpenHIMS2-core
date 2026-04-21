@extends('layouts.clinical')
@section('title', $pageTitle ?? 'Register Patient')

@push('styles')
<style>
    /* ── Indigo theme ──────────────────────────────────────────────────── */
    :root { --c:#4f46e5; --c-light:#eef2ff; --c-mid:#c7d2fe; --c-dark:#3730a3; }

    .role-banner {
        background: linear-gradient(135deg, #3730a3 0%, #4f46e5 55%, #818cf8 100%);
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

    .reg-card {
        border-radius:.875rem !important;
        border:1px solid #f1f5f9 !important;
        box-shadow:0 1px 4px rgba(0,0,0,.06), 0 6px 20px rgba(0,0,0,.04) !important;
    }
    .reg-card-header {
        background:#fafbff !important;
        border-bottom:1px solid #ededf5 !important;
        border-radius:.875rem .875rem 0 0 !important;
    }

    /* Column divider */
    .col-divider {
        border-left: 1px solid #ededf5;
    }

    /* Section labels */
    .section-label {
        font-size:.68rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.08em; color:var(--c); margin-bottom:.65rem;
        display:block;
    }

    /* Form controls */
    .form-control:focus, .form-select:focus {
        border-color: var(--c);
        box-shadow: 0 0 0 .2rem rgba(79,70,229,.15);
    }

    /* Category button cards */
    .cat-btn-wrap { position:relative; }
    .cat-btn-wrap input[type="radio"] { position:absolute; opacity:0; width:0; height:0; }
    .cat-card {
        border: 1.5px solid #e2e8f0;
        border-radius: .625rem;
        padding: .75rem 1rem;
        cursor: pointer;
        transition: all .15s;
        background: #fff;
        display: flex;
        align-items: center;
        gap: .75rem;
    }
    .cat-card:hover { border-color: var(--c-mid); background: var(--c-light); }
    .cat-card .cat-icon {
        width: 2.25rem; height: 2.25rem;
        border-radius: .5rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 1.05rem;
    }
    .cat-card .cat-label { font-size:.85rem; font-weight:600; color:#374151; }
    .cat-card .cat-desc  { font-size:.72rem; color:#6b7280; }

    /* Active states per category */
    input[value="opd"]:checked ~ .cat-card            { border-color:#3b82f6; background:#eff6ff; }
    input[value="opd"]:checked ~ .cat-card .cat-icon  { background:#dbeafe; color:#1d4ed8; }
    input[value="opd"]:checked ~ .cat-card .cat-label { color:#1d4ed8; }

    input[value="new_clinic_visit"]:checked ~ .cat-card            { border-color:#16a34a; background:#f0fdf4; }
    input[value="new_clinic_visit"]:checked ~ .cat-card .cat-icon  { background:#dcfce7; color:#15803d; }
    input[value="new_clinic_visit"]:checked ~ .cat-card .cat-label { color:#15803d; }

    input[value="urgent"]:checked ~ .cat-card            { border-color:#ef4444; background:#fef2f2; }
    input[value="urgent"]:checked ~ .cat-card .cat-icon  { background:#fee2e2; color:#dc2626; }
    input[value="urgent"]:checked ~ .cat-card .cat-label { color:#dc2626; }

    /* Category detail panel */
    .cat-detail-panel {
        border-radius:.625rem;
        border:1px solid #e2e8f0;
        background:#fafbff;
        padding:1rem;
    }

    /* Duplicate alert */
    #ajax-duplicate-alert { border-radius:.625rem; }

    /* Submit btn */
    .btn-submit {
        background: var(--c); color: white; border: none;
        border-radius: .5rem; font-weight: 600;
        padding: .55rem 1.5rem;
    }
    .btn-submit:hover { background: var(--c-dark); color: white; }
</style>
@endpush

@section('content')

{{-- ── Role Banner ─────────────────────────────────────────────────────────── --}}
<div class="role-banner">
    <div class="d-flex align-items-center justify-content-between position-relative" style="z-index:1;">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ url()->previous() }}"
               class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 text-white"
               style="width:2.75rem;height:2.75rem;background:rgba(255,255,255,.18);backdrop-filter:blur(4px);text-decoration:none;">
                <i class="bi bi-arrow-left" style="font-size:1.1rem;"></i>
            </a>
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:2.75rem;height:2.75rem;background:rgba(255,255,255,.18);backdrop-filter:blur(4px);">
                <i class="bi bi-person-plus-fill" style="font-size:1.2rem;color:white;"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-white">Register New Patient</h5>
                <p class="mb-0 small text-white" style="opacity:.82;">
                    <i class="bi bi-building me-1"></i>{{ $unitView->unit->name }}
                    <span class="mx-2" style="opacity:.5;">·</span>
                    <i class="bi bi-geo-alt me-1"></i>{{ $unitView->unit->institution->name }}
                </p>
            </div>
        </div>
        <div class="text-end d-none d-sm-block position-relative" style="z-index:1;">
            <div class="small text-white" style="opacity:.75;">{{ now()->format('l') }}</div>
            <div class="fw-bold text-white">{{ now()->format('d M Y') }}</div>
        </div>
    </div>
</div>

{{-- ── Duplicate patient alert (server-side) ────────────────────────────────── --}}
@if(session('duplicate_patient'))
    @php $dup = session('duplicate_patient'); @endphp
    <div class="alert alert-warning border-0 shadow-sm mb-3 rounded-3" id="server-duplicate-alert">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning mt-1"></i>
            <div class="flex-grow-1">
                <strong>Patient already registered</strong>
                <div class="mt-1 small">
                    <span class="me-3"><i class="bi bi-person me-1"></i>{{ $dup->name }}</span>
                    <span class="me-3"><code>{{ $dup->phn }}</code></span>
                    @if($dup->nic) <span class="me-3">NIC: {{ $dup->nic }}</span> @endif
                    @if($dup->mobile) <span>Mobile: {{ $dup->mobile }}</span> @endif
                </div>
                @if(session('duplicate_already_in_queue'))
                    <div class="mt-2 text-success fw-medium small">
                        <i class="bi bi-check2-circle me-1"></i>Already in today's queue
                    </div>
                @else
                    <form method="POST"
                          action="{{ route('clinical.patients.add-to-queue', [$unitView->id, $dup->id]) }}"
                          class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">
                            <i class="bi bi-plus-circle me-1"></i>Add to today's queue
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- ── Main form card ───────────────────────────────────────────────────────── --}}
<div class="card reg-card">
    <div class="card-header reg-card-header px-4 py-3 d-flex align-items-center gap-2">
        <i class="bi bi-clipboard2-pulse-fill" style="color:var(--c);"></i>
        <span class="fw-semibold text-dark">Patient Registration Form</span>
    </div>
    <div class="card-body p-0">
        <form method="POST" action="{{ route('clinical.patients.store', $unitView->id) }}" novalidate>
            @csrf

            <div class="row g-0">

                {{-- ══════════════════════════════════════════════════════════
                     LEFT COLUMN — Personal & Contact Details
                     ══════════════════════════════════════════════════════════ --}}
                <div class="col-lg-6 p-4">

                    {{-- Personal Details --}}
                    <span class="section-label"><i class="bi bi-person-vcard me-1"></i>Personal Details</span>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label form-label-sm mb-1">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   placeholder="As in identity document" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label form-label-sm mb-1">PHN <span class="text-muted">(optional)</span></label>
                            <input type="text" name="phn" value="{{ old('phn') }}"
                                   class="form-control form-control-sm @error('phn') is-invalid @enderror"
                                   placeholder="Patient Health Number">
                            @error('phn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label form-label-sm mb-1">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select form-select-sm @error('gender') is-invalid @enderror" required>
                                <option value="">— Select —</option>
                                <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label form-label-sm mb-1">Date of Birth</label>
                            <input type="date" name="dob" id="dob-input"
                                   value="{{ old('dob') }}"
                                   class="form-control form-control-sm @error('dob') is-invalid @enderror"
                                   max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                            @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text" style="font-size:.7rem;">Leave blank to enter age manually</div>
                        </div>
                        <div class="col-sm-6" id="age-row">
                            <label class="form-label form-label-sm mb-1">Age (years)</label>
                            <input type="number" name="age" id="age-input"
                                   value="{{ old('age') }}"
                                   class="form-control form-control-sm @error('age') is-invalid @enderror"
                                   min="0" max="150" placeholder="e.g. 35">
                            @error('age') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Contact & Identity (adults) --}}
                    <div id="adult-section">
                        <span class="section-label mt-3"><i class="bi bi-id-card me-1"></i>Contact &amp; Identity</span>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label form-label-sm mb-1">NIC Number</label>
                                <input type="text" name="nic" id="nic-input"
                                       value="{{ old('nic') }}"
                                       class="form-control form-control-sm @error('nic') is-invalid @enderror"
                                       placeholder="National Identity Card" autocomplete="off">
                                @error('nic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div id="nic-duplicate-info" class="mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label form-label-sm mb-1">Mobile Number</label>
                                <input type="text" name="mobile" id="mobile-input"
                                       value="{{ old('mobile') }}"
                                       class="form-control form-control-sm @error('mobile') is-invalid @enderror"
                                       placeholder="e.g. 0771234567" autocomplete="off">
                                @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div id="mobile-duplicate-info" class="mt-1" style="display:none;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Guardian Details (under-16) --}}
                    <div id="guardian-section" style="display:none;">
                        <span class="section-label mt-3">
                            <i class="bi bi-people me-1"></i>Guardian Details
                            <span class="badge bg-warning text-dark ms-1" style="font-size:.6rem;font-weight:600;">Under 16</span>
                        </span>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label form-label-sm mb-1">Guardian Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_mobile" id="guardian-mobile-input"
                                       value="{{ old('guardian_mobile') }}"
                                       class="form-control form-control-sm @error('guardian_mobile') is-invalid @enderror"
                                       placeholder="Guardian's mobile">
                                @error('guardian_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label form-label-sm mb-1">Guardian NIC <span class="text-muted">(optional)</span></label>
                                <input type="text" name="guardian_nic"
                                       value="{{ old('guardian_nic') }}"
                                       class="form-control form-control-sm @error('guardian_nic') is-invalid @enderror"
                                       placeholder="Guardian's NIC">
                                @error('guardian_nic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Address --}}
                    <span class="section-label mt-3"><i class="bi bi-house me-1"></i>Address <span class="text-danger">*</span></span>
                    <div class="mb-0">
                        <textarea name="address" rows="2"
                                  class="form-control form-control-sm @error('address') is-invalid @enderror"
                                  placeholder="House No., Street, City, District" required>{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>

                {{-- ══════════════════════════════════════════════════════════
                     RIGHT COLUMN — Visit Category & Clinical Details
                     ══════════════════════════════════════════════════════════ --}}
                <div class="col-lg-6 p-4 col-divider">

                    <span class="section-label"><i class="bi bi-clipboard2-check me-1"></i>Visit Category <span class="text-danger">*</span></span>

                    <div class="d-flex flex-column gap-2 mb-3">

                        {{-- OPD --}}
                        <label class="cat-btn-wrap">
                            <input type="radio" name="category" value="opd"
                                   {{ old('category', 'opd') === 'opd' ? 'checked' : '' }} required>
                            <div class="cat-card">
                                <div class="cat-icon" style="background:#dbeafe;color:#1d4ed8;">
                                    <i class="bi bi-hospital"></i>
                                </div>
                                <div>
                                    <div class="cat-label">OPD</div>
                                    <div class="cat-desc">Out-patient department visit</div>
                                </div>
                            </div>
                        </label>

                        {{-- New Clinic Visit --}}
                        <label class="cat-btn-wrap">
                            <input type="radio" name="category" value="new_clinic_visit"
                                   {{ old('category') === 'new_clinic_visit' ? 'checked' : '' }}>
                            <div class="cat-card">
                                <div class="cat-icon" style="background:#dcfce7;color:#15803d;">
                                    <i class="bi bi-person-plus-fill"></i>
                                </div>
                                <div>
                                    <div class="cat-label">New Clinic Visit</div>
                                    <div class="cat-desc">First-time clinic attendance</div>
                                </div>
                            </div>
                        </label>

                        {{-- Urgent --}}
                        <label class="cat-btn-wrap">
                            <input type="radio" name="category" value="urgent"
                                   {{ old('category') === 'urgent' ? 'checked' : '' }}>
                            <div class="cat-card">
                                <div class="cat-icon" style="background:#fee2e2;color:#dc2626;">
                                    <i class="bi bi-person-badge-fill"></i>
                                </div>
                                <div>
                                    <div class="cat-label">Urgent</div>
                                    <div class="cat-desc">Priority / emergency referral</div>
                                </div>
                            </div>
                        </label>

                    </div>
                    @error('category') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                    {{-- ── Clinical Details panel ── --}}
                    <div class="cat-detail-panel">
                        <span class="section-label" style="margin-bottom:.5rem;"><i class="bi bi-activity me-1"></i>Clinical Details</span>

                        <div class="row g-2 mb-2">
                            <div class="col-12" id="opd-number-wrap">
                                <label class="form-label form-label-sm mb-1">OPD Number</label>
                                <input type="text" name="opd_number" value="{{ old('opd_number') }}"
                                       class="form-control form-control-sm @error('opd_number') is-invalid @enderror"
                                       placeholder="OPD ticket / reference" maxlength="20">
                                @error('opd_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-sm-6">
                                <label class="form-label form-label-sm mb-1">Height (cm)</label>
                                <input type="number" name="height" value="{{ old('height') }}"
                                       class="form-control form-control-sm @error('height') is-invalid @enderror"
                                       placeholder="e.g. 165" min="1" max="300" step="0.1">
                                @error('height') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label form-label-sm mb-1">Weight (kg)</label>
                                <input type="number" name="weight" value="{{ old('weight') }}"
                                       class="form-control form-control-sm @error('weight') is-invalid @enderror"
                                       placeholder="e.g. 68" min="1" max="500" step="0.1">
                                @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="form-label form-label-sm mb-1">Blood Pressure (mmHg)</label>
                            <div class="row g-2 mb-1">
                                <div class="col-sm-6">
                                    <input type="number" name="bp_systolic" value="{{ old('bp_systolic') }}"
                                           class="form-control form-control-sm @error('bp_systolic') is-invalid @enderror"
                                           placeholder="Systolic" min="0" max="300">
                                    @error('bp_systolic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-sm-6">
                                    <input type="number" name="bp_diastolic" value="{{ old('bp_diastolic') }}"
                                           class="form-control form-control-sm @error('bp_diastolic') is-invalid @enderror"
                                           placeholder="Diastolic" min="0" max="300">
                                    @error('bp_diastolic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="form-text mb-1" style="font-size:.7rem;">Systolic / Diastolic</div>
                            <input type="datetime-local" name="bp_recorded_at" id="bp-recorded-at"
                                   value="{{ old('bp_recorded_at') }}"
                                   class="form-control form-control-sm @error('bp_recorded_at') is-invalid @enderror">
                            @error('bp_recorded_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text" style="font-size:.7rem;">BP recorded date &amp; time</div>
                        </div>
                    </div>

                    {{-- ── AJAX duplicate alert ── --}}
                    <div id="ajax-duplicate-alert" class="alert alert-warning border-0 mt-3" style="display:none;border-radius:.625rem !important;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-warning mt-1"></i>
                            <div id="ajax-duplicate-body" class="flex-grow-1 small"></div>
                        </div>
                    </div>

                    {{-- ── Actions ── --}}
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('clinical.show', $unitView->id) }}" class="btn btn-sm btn-outline-secondary px-3">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-submit btn-sm" id="submit-btn">
                            <i class="bi bi-person-plus-fill me-1"></i>Register &amp; Add to Queue
                        </button>
                    </div>

                </div>
            </div>{{-- /row --}}
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    // Pre-fill BP recorded-at with local date/time (only when the field is empty — not on old() re-fill)
    const bpRecordedAt = document.getElementById('bp-recorded-at');
    if (bpRecordedAt && !bpRecordedAt.value) {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        bpRecordedAt.value = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) +
                             'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
    }

    const checkUrl  = "{{ route('clinical.patients.check-duplicate', $unitView->id) }}";
    const addUrl    = (pid) => "{{ url('/clinical/' . $unitView->id . '/add-to-queue') }}/" + pid;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ── Age / DOB toggling ───────────────────────────────────────────────────

    const dobInput        = document.getElementById('dob-input');
    const ageInput        = document.getElementById('age-input');
    const ageRow          = document.getElementById('age-row');
    const adultSection    = document.getElementById('adult-section');
    const guardianSection = document.getElementById('guardian-section');
    const guardianMobile  = document.getElementById('guardian-mobile-input');

    function calcAge(dob) {
        const today = new Date(), birth = new Date(dob);
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        return age;
    }

    function checkIfMinor(age) {
        if (age !== null && age < 16) {
            adultSection.style.display    = 'none';
            guardianSection.style.display = '';
            guardianMobile.required       = true;
        } else {
            adultSection.style.display    = '';
            guardianSection.style.display = 'none';
            guardianMobile.required       = false;
        }
    }

    dobInput.addEventListener('change', function () {
        if (this.value) {
            const age = calcAge(this.value);
            ageInput.value = age;
            ageRow.style.display = 'none';
            checkIfMinor(age);
        } else {
            ageRow.style.display = '';
            checkIfMinor(parseInt(ageInput.value) || null);
        }
    });

    ageInput.addEventListener('input', function () {
        checkIfMinor(parseInt(this.value) || null);
    });

    if (dobInput.value) {
        ageRow.style.display = 'none';
        checkIfMinor(calcAge(dobInput.value));
    } else if (ageInput.value) {
        checkIfMinor(parseInt(ageInput.value));
    }

    // ── Duplicate detection ──────────────────────────────────────────────────

    let foundPatient = null;

    function checkDuplicate(field, value) {
        if (!value) return;
        const body = field === 'nic' ? { nic: value } : { mobile: value };

        fetch(checkUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        })
        .then(r => r.json())
        .then(data => {
            if (!data.found) {
                document.getElementById('ajax-duplicate-alert').style.display = 'none';
                foundPatient = null;
                return;
            }

            foundPatient = data;
            const p = data.patient;
            const inQueue = data.already_in_queue;

            let html = `<strong>Patient already registered</strong><br>
                <span class="me-2"><i class="bi bi-person me-1"></i>${p.name}</span>
                <code>${p.phn}</code>`;
            if (p.nic)    html += `<span class="ms-2">NIC: ${p.nic}</span>`;
            if (p.mobile) html += `<span class="ms-2">Mobile: ${p.mobile}</span>`;

            if (inQueue) {
                html += `<div class="mt-2 text-success fw-medium">
                    <i class="bi bi-check2-circle me-1"></i>Already in today's queue</div>`;
            } else {
                html += `<div class="mt-2">
                    <button type="button" class="btn btn-sm btn-warning" id="ajax-add-btn"
                        data-patient-id="${p.id}">
                        <i class="bi bi-plus-circle me-1"></i>Add to today's queue
                    </button></div>`;
            }

            document.getElementById('ajax-duplicate-body').innerHTML = html;
            document.getElementById('ajax-duplicate-alert').style.display = '';

            const addBtn = document.getElementById('ajax-add-btn');
            if (addBtn) {
                addBtn.addEventListener('click', function () {
                    addBtn.disabled = true;
                    fetch(addUrl(this.dataset.patientId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(r => {
                        if (r.redirected) { window.location.href = r.url; }
                        else { window.location.href = "{{ route('clinical.show', $unitView->id) }}"; }
                    });
                });
            }
        });
    }

    document.getElementById('nic-input').addEventListener('blur', function () {
        checkDuplicate('nic', this.value.trim());
    });
    document.getElementById('mobile-input').addEventListener('blur', function () {
        checkDuplicate('mobile', this.value.trim());
    });

})();
</script>
@endpush
