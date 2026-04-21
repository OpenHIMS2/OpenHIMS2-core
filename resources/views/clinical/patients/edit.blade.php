@extends('layouts.clinical')
@section('title', $pageTitle ?? 'Edit Patient')

@push('styles')
<style>
    .form-section { border-left: 3px solid #dee2e6; padding-left: 1rem; margin-bottom: 1.5rem; }
    .form-section .section-title { font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: #6c757d; margin-bottom: .75rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">

        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('clinical.show', $unitView->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Edit Patient</h4>
                <p class="text-muted mb-0 small">
                    {{ $unitView->unit->name }} &bull; {{ $unitView->unit->institution->name }}
                </p>
            </div>
            <div class="ms-auto text-end">
                <code class="text-muted small">{{ $patient->phn ?? '—' }}</code>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <form method="POST"
                      action="{{ route('clinical.patients.update', [$unitView->id, $patient->id]) }}"
                      novalidate>
                    @csrf
                    @method('PATCH')

                    {{-- SECTION: Personal Details --}}
                    <div class="form-section">
                        <div class="section-title">Personal Details</div>

                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   value="{{ old('name', $patient->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="As in identity document" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">PHN <span class="text-muted small">(optional)</span></label>
                            <input type="text" name="phn"
                                   value="{{ old('phn', $patient->phn) }}"
                                   class="form-control @error('phn') is-invalid @enderror"
                                   placeholder="Patient Health Number">
                            @error('phn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" id="dob-input"
                                       value="{{ old('dob', $patient->dob?->format('Y-m-d')) }}"
                                       class="form-control @error('dob') is-invalid @enderror"
                                       max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                                @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Leave blank to enter age manually</div>
                            </div>
                            <div class="col-sm-6" id="age-row">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" id="age-input"
                                       value="{{ old('age', $patient->age) }}"
                                       class="form-control @error('age') is-invalid @enderror"
                                       min="0" max="150" placeholder="Years">
                                @error('age') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">— Select —</option>
                                @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('gender', $patient->gender) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- SECTION: Contact & Identity (adults) --}}
                    <div class="form-section" id="adult-section">
                        <div class="section-title">Contact &amp; Identity</div>

                        <div class="mb-3" id="nic-row">
                            <label class="form-label">NIC Number</label>
                            <input type="text" name="nic" id="nic-input"
                                   value="{{ old('nic', $patient->nic) }}"
                                   class="form-control @error('nic') is-invalid @enderror"
                                   placeholder="National Identity Card number" autocomplete="off">
                            @error('nic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3" id="mobile-row">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile" id="mobile-input"
                                   value="{{ old('mobile', $patient->mobile) }}"
                                   class="form-control @error('mobile') is-invalid @enderror"
                                   placeholder="e.g. 0771234567" autocomplete="off">
                            @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- SECTION: Address --}}
                    <div class="form-section">
                        <div class="section-title">Address <span class="text-danger">*</span></div>

                        <div class="mb-3">
                            <label class="form-label">Residential Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="3"
                                      class="form-control @error('address') is-invalid @enderror"
                                      placeholder="House No., Street, City, District" required>{{ old('address', $patient->address) }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- SECTION: Guardian Details (under-16) --}}
                    <div class="form-section" id="guardian-section" style="display:none;">
                        <div class="section-title">
                            Guardian Details
                            <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem;">Under 16</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guardian Mobile <span class="text-danger">*</span></label>
                            <input type="text" name="guardian_mobile" id="guardian-mobile-input"
                                   value="{{ old('guardian_mobile', $patient->guardian_mobile) }}"
                                   class="form-control @error('guardian_mobile') is-invalid @enderror"
                                   placeholder="Guardian's mobile number">
                            @error('guardian_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guardian NIC <span class="text-muted small">(optional)</span></label>
                            <input type="text" name="guardian_nic"
                                   value="{{ old('guardian_nic', $patient->guardian_nic) }}"
                                   class="form-control @error('guardian_nic') is-invalid @enderror"
                                   placeholder="Guardian's NIC number">
                            @error('guardian_nic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Inline duplicate alert (AJAX — only for changed NIC/mobile) --}}
                    <div id="ajax-duplicate-alert" class="alert alert-warning border-0 mb-3" style="display:none;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-warning mt-1"></i>
                            <div id="ajax-duplicate-body" class="flex-grow-1 small"></div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('clinical.show', $unitView->id) }}"
                           class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const checkUrl   = "{{ route('clinical.patients.check-duplicate', $unitView->id) }}";
    const excludeId  = {{ $patient->id }};
    const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;

    // ── Age / DOB toggling ───────────────────────────────────────────────────

    const dobInput        = document.getElementById('dob-input');
    const ageInput        = document.getElementById('age-input');
    const ageRow          = document.getElementById('age-row');
    const adultSection    = document.getElementById('adult-section');
    const guardianSection = document.getElementById('guardian-section');
    const guardianMobile  = document.getElementById('guardian-mobile-input');
    const nicRow          = document.getElementById('nic-row');
    const mobileRow       = document.getElementById('mobile-row');

    function calcAge(dob) {
        const today = new Date();
        const birth = new Date(dob);
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        return age;
    }

    function checkIfMinor(age) {
        if (age !== null && age < 16) {
            adultSection.style.display    = 'none';
            guardianSection.style.display = '';
            guardianMobile.required = true;
        } else {
            adultSection.style.display    = '';
            guardianSection.style.display = 'none';
            guardianMobile.required = false;
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

    // Initialize on load
    if (dobInput.value) {
        ageRow.style.display = 'none';
        checkIfMinor(calcAge(dobInput.value));
    } else if (ageInput.value) {
        checkIfMinor(parseInt(ageInput.value));
    }

    // ── Duplicate check (only fires if value changed from original) ──────────

    const origNic    = document.getElementById('nic-input').defaultValue;
    const origMobile = document.getElementById('mobile-input').defaultValue;

    function checkDuplicate(field, value) {
        // Skip if value unchanged (no point checking own record)
        if (field === 'nic'    && value === origNic)    return;
        if (field === 'mobile' && value === origMobile) return;
        if (!value) {
            document.getElementById('ajax-duplicate-alert').style.display = 'none';
            return;
        }

        const body = field === 'nic'
            ? { nic: value, exclude_id: excludeId }
            : { mobile: value, exclude_id: excludeId };

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
                return;
            }
            const p = data.patient;
            let html = `<strong>This ${field.toUpperCase()} belongs to another patient</strong><br>
                <span class="me-3"><i class="bi bi-person me-1"></i>${p.name}</span>
                <code>${p.phn ?? ''}</code>`;
            if (p.nic)    html += `<span class="ms-2">NIC: ${p.nic}</span>`;
            if (p.mobile) html += `<span class="ms-2">Mobile: ${p.mobile}</span>`;

            document.getElementById('ajax-duplicate-body').innerHTML = html;
            document.getElementById('ajax-duplicate-alert').style.display = '';
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
