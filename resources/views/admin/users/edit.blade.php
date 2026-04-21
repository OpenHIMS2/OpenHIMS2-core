@extends('layouts.admin')
@section('title', 'Edit User')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit User</h4>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf @method('PUT')

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
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">New Password <span class="text-muted fw-normal small">(leave blank to keep)</span></label>
                    <input type="password" name="password" class="form-control" minlength="8">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                    <select name="role" id="roleSelect" class="form-select" required>
                        <option value="user"  {{ old('role', $user->role) == 'user'  ? 'selected' : '' }}>Clinical User</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
                <div class="col-md-6" id="institutionField">
                    <label class="form-label fw-medium">Institution</label>
                    <select name="institution_id" id="institutionSelect" class="form-select">
                        <option value="">— None —</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}"
                                {{ old('institution_id', $user->institution_id) == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="unitsSection" style="{{ $units->isEmpty() ? 'display:none;' : '' }}" class="mb-3">
                <label class="form-label fw-medium">Assign Units</label>
                <div id="unitsContainer" class="border rounded p-3 bg-light">
                    @foreach($units as $u)
                        <div class="form-check form-check-inline me-3 mb-1">
                            <input class="form-check-input unit-cb" type="checkbox"
                                   name="unit_ids[]" value="{{ $u->id }}" id="uc_{{ $u->id }}"
                                   {{ in_array((string)$u->id, $userUnitIds) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="uc_{{ $u->id }}">
                                <span class="badge bg-secondary me-1">{{ $u->unitTemplate->code }}</span>{{ $u->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="viewsSection" style="{{ $views->isEmpty() ? 'display:none;' : '' }}" class="mb-4">
                <label class="form-label fw-medium">Assign Views</label>
                <div id="viewsContainer" class="border rounded p-3 bg-light">
                    @foreach($views as $v)
                        <div class="form-check form-check-inline me-3 mb-1">
                            <input class="form-check-input" type="checkbox"
                                   name="view_ids[]" value="{{ $v->id }}" id="vc_{{ $v->id }}"
                                   {{ in_array((string)$v->id, $userViewIds) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="vc_{{ $v->id }}">
                                {{ $v->viewTemplate->name }}
                                <span class="text-muted">({{ $v->unit->name }})</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>Update User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const selectedUnitIds = @json($userUnitIds);
const selectedViewIds = @json($userViewIds);

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
        .then(r => r.json())
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
                           ${selectedUnitIds.includes(String(u.id)) ? 'checked' : ''}>
                    <label class="form-check-label small" for="uc_${u.id}">
                        <span class="badge bg-secondary me-1">${u.unit_template.code}</span>${u.name}
                    </label>
                </div>`
            ).join('');
            unitsSection.style.display = 'block';
            document.querySelectorAll('.unit-cb').forEach(cb => cb.addEventListener('change', loadViews));
            loadViews();
        });
}

function loadViews() {
    const checked = [...document.querySelectorAll('.unit-cb:checked')].map(el => el.value);
    if (!checked.length) { viewsSection.style.display = 'none'; return; }

    const params = new URLSearchParams();
    checked.forEach(id => params.append('unit_ids[]', id));

    fetch(`{{ route('admin.users.views-for-units') }}?${params}`)
        .then(r => r.json())
        .then(views => {
            if (!views.length) {
                viewsContainer.innerHTML = '<span class="text-muted small">No views for selected units.</span>';
                viewsSection.style.display = 'block';
                return;
            }
            viewsContainer.innerHTML = views.map(v =>
                `<div class="form-check form-check-inline me-3 mb-1">
                    <input class="form-check-input" type="checkbox"
                           name="view_ids[]" value="${v.id}" id="vc_${v.id}"
                           ${selectedViewIds.includes(String(v.id)) ? 'checked' : ''}>
                    <label class="form-check-label small" for="vc_${v.id}">
                        ${v.view_template.name} <span class="text-muted">(${v.unit.name})</span>
                    </label>
                </div>`
            ).join('');
            viewsSection.style.display = 'block';
        });
}

// Pre-populate: if institution already set, attach change listeners to existing checkboxes
document.querySelectorAll('.unit-cb').forEach(cb => cb.addEventListener('change', loadViews));
</script>
@endpush
