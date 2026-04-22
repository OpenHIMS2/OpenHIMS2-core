@extends('layouts.admin')
@section('title', 'Create User')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Create User</h4>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger py-2 small mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                    <select name="role" id="roleSelect" class="form-select" required>
                        <option value="user"  {{ old('role','user') == 'user'  ? 'selected' : '' }}>Clinical User</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
                <div class="col-md-6" id="institutionField">
                    <label class="form-label fw-medium">Institution</label>
                    <select name="institution_id" id="institutionSelect" class="form-select">
                        <option value="">— None —</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="unitsSection" style="display:none;" class="mb-3">
                <label class="form-label fw-medium">Assign Units</label>
                <div id="unitsContainer" class="border rounded p-3 bg-light">
                    <span class="text-muted small">Loading...</span>
                </div>
            </div>

            <div id="viewsSection" style="display:none;" class="mb-4">
                <label class="form-label fw-medium">Assign Views</label>
                <div id="viewsContainer" class="border rounded p-3 bg-light">
                    <span class="text-muted small">Loading...</span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>Create User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const selectedUnitIds = @json(old('unit_ids', []));
const selectedViewIds = @json(old('view_ids', []));

const institutionSelect = document.getElementById('institutionSelect');
const roleSelect        = document.getElementById('roleSelect');
const unitsSection      = document.getElementById('unitsSection');
const unitsContainer    = document.getElementById('unitsContainer');
const viewsSection      = document.getElementById('viewsSection');
const viewsContainer    = document.getElementById('viewsContainer');
const institutionField  = document.getElementById('institutionField');

roleSelect.addEventListener('change', () => {
    institutionField.style.display = roleSelect.value === 'admin' ? 'none' : '';
    if (roleSelect.value === 'admin') {
        unitsSection.style.display = 'none';
        viewsSection.style.display = 'none';
    }
});

institutionSelect.addEventListener('change', () => loadUnits(institutionSelect.value));

function loadUnits(institutionId) {
    if (!institutionId) {
        unitsSection.style.display = 'none';
        viewsSection.style.display = 'none';
        return;
    }
    fetch(`{{ url('admin/users/units-for-institution') }}/${institutionId}`)
        .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(units => {
            if (!units.length) {
                unitsContainer.innerHTML = '<span class="text-muted small">No units in this institution.</span>';
                unitsSection.style.display = 'block';
                viewsSection.style.display = 'none';
                return;
            }
            unitsContainer.innerHTML = units.map(u =>
                `<div class="form-check form-check-inline me-3 mb-1">
                    <input class="form-check-input unit-cb" type="checkbox"
                           name="unit_ids[]" value="${u.id}" id="uc_${u.id}"
                           ${selectedUnitIds.map(String).includes(String(u.id)) ? 'checked' : ''}>
                    <label class="form-check-label small" for="uc_${u.id}">
                        <span class="badge bg-secondary me-1">${u.unit_template.code}</span>${u.name}
                    </label>
                </div>`
            ).join('');
            unitsSection.style.display = 'block';
            document.querySelectorAll('.unit-cb').forEach(cb => cb.addEventListener('change', loadViews));
            loadViews();
        })
        .catch(() => {
            unitsContainer.innerHTML = '<span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Failed to load units. Please refresh.</span>';
            unitsSection.style.display = 'block';
        });
}

function loadViews() {
    const checked = [...document.querySelectorAll('.unit-cb:checked')].map(el => el.value);
    if (!checked.length) { viewsSection.style.display = 'none'; return; }

    const params = new URLSearchParams();
    checked.forEach(id => params.append('unit_ids[]', id));

    fetch(`{{ route('admin.users.views-for-units') }}?${params}`)
        .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(views => {
            if (!views.length) {
                viewsContainer.innerHTML = '<span class="text-muted small">No views configured for selected units. Go to Views Management to add views.</span>';
                viewsSection.style.display = 'block';
                return;
            }
            viewsContainer.innerHTML = views.map(v =>
                `<div class="form-check form-check-inline me-3 mb-1">
                    <input class="form-check-input" type="checkbox"
                           name="view_ids[]" value="${v.id}" id="vc_${v.id}"
                           ${selectedViewIds.map(String).includes(String(v.id)) ? 'checked' : ''}>
                    <label class="form-check-label small" for="vc_${v.id}">
                        ${v.view_template.name} <span class="text-muted">(${v.unit.name})</span>
                    </label>
                </div>`
            ).join('');
            viewsSection.style.display = 'block';
        })
        .catch(() => {
            viewsContainer.innerHTML = '<span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Failed to load views. Please refresh.</span>';
            viewsSection.style.display = 'block';
        });
}

// Init: if institution already selected (e.g. after form validation error), load its units
if (institutionSelect.value) loadUnits(institutionSelect.value);
</script>
@endpush
