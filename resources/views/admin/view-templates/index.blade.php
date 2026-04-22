@extends('layouts.admin')
@section('title', 'View Templates')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-layout-text-sidebar me-2 text-primary"></i>View Templates</h4>
        <p class="text-muted small mt-1">Clinical view templates grouped by unit type. System templates are protected.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addViewTemplateModal">
        <i class="bi bi-plus-lg me-1"></i> Add View Template
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@foreach($unitTemplates as $unitTemplate)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-2">
        <span class="badge bg-primary">{{ $unitTemplate->code }}</span>
        <span class="fw-semibold">{{ $unitTemplate->name }}</span>
        @if(!$unitTemplate->is_system)
            <span class="badge bg-info text-dark ms-1">Custom</span>
        @endif
        <span class="badge bg-light text-dark border ms-auto">{{ $unitTemplate->viewTemplates->count() }} views</span>
        <button class="btn btn-sm btn-outline-primary ms-2"
                data-bs-toggle="modal" data-bs-target="#addViewTemplateModal"
                data-unit-id="{{ $unitTemplate->id }}"
                data-unit-code="{{ $unitTemplate->code }}"
                data-unit-name="{{ $unitTemplate->name }}">
            <i class="bi bi-plus-lg"></i> Add View
        </button>
    </div>
    <div class="card-body p-0">
        @if($unitTemplate->viewTemplates->isEmpty())
            <p class="text-muted p-3 mb-0 small">No view templates defined.</p>
        @else
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Code</th>
                        <th>View Template Name</th>
                        <th>Blade Path</th>
                        <th class="text-center">Type</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unitTemplate->viewTemplates as $vt)
                    <tr>
                        <td class="ps-4">
                            <code class="small text-primary">{{ $vt->code }}</code>
                        </td>
                        <td class="fw-medium">{{ $vt->name }}</td>
                        <td><code class="text-secondary small">{{ $vt->blade_path }}</code></td>
                        <td class="text-center">
                            @if($vt->is_system)
                                <span class="badge bg-secondary">System</span>
                            @else
                                <span class="badge bg-info text-dark">Custom</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            @unless($vt->is_system)
                            <form method="POST" action="{{ route('admin.view-templates.destroy', $vt) }}"
                                  onsubmit="return confirm('Delete view template \'{{ $vt->name }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        @if($vt->unit_views_count) disabled title="Cannot delete — unit views exist" @endif>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted small"><i class="bi bi-lock me-1"></i>Protected</span>
                            @endunless
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endforeach

<p class="text-muted small mt-2">
    <i class="bi bi-info-circle me-1"></i>
    When a View Template is created, its clinical page file is automatically generated with full developer documentation.
    System templates are seeded and cannot be deleted.
</p>

{{-- ── Add View Template Modal ──────────────────────────────────────── --}}
<div class="modal fade" id="addViewTemplateModal" tabindex="-1" aria-labelledby="addViewTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('admin.view-templates.store') }}">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="addViewTemplateModalLabel">
                        <i class="bi bi-plus-circle me-2 text-primary"></i>Add View Template
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit Template <span class="text-danger">*</span></label>
                        <select name="unit_template_id" id="vtUnitTemplateSelect" class="form-select" required>
                            <option value="">— Select unit template —</option>
                            @foreach($unitTemplates as $ut)
                            <option value="{{ $ut->id }}"
                                    data-code="{{ $ut->code }}"
                                    {{ old('unit_template_id') == $ut->id ? 'selected' : '' }}>
                                {{ $ut->name }} ({{ $ut->code }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">View Template Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="vtName" class="form-control" required
                               placeholder="e.g. PHYSIO - Doctor View" maxlength="100"
                               value="{{ old('name') }}">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="vtCode" class="form-control" required
                                   placeholder="e.g. physio-doctor" maxlength="50"
                                   value="{{ old('code') }}">
                            <div class="form-text">Lowercase, unique slug (max 50 chars).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Blade Path <span class="text-danger">*</span></label>
                            <input type="text" name="blade_path" id="vtBladePath" class="form-control font-monospace" required
                                   placeholder="e.g. clinical.physio.doctor" maxlength="100"
                                   value="{{ old('blade_path') }}">
                            <div class="form-text">
                                Dot-notation path. Maps to
                                <code id="vtBladePreview" class="text-success">resources/views/…</code>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 py-2 small mt-3 mb-0">
                        <i class="bi bi-lightbulb me-1"></i>
                        After saving, create the blade file at the path shown above.
                        Until the file exists, users assigned to this view will see the
                        <strong>developer guide page</strong> with step-by-step instructions.
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Create View Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const unitSelect  = document.getElementById('vtUnitTemplateSelect');
    const nameInput   = document.getElementById('vtName');
    const codeInput   = document.getElementById('vtCode');
    const bladeInput  = document.getElementById('vtBladePath');
    const bladePreview = document.getElementById('vtBladePreview');
    const modal       = document.getElementById('addViewTemplateModal');

    function toSlug(str) {
        return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }

    function updateBladeSuggestion() {
        const unitOpt = unitSelect.options[unitSelect.selectedIndex];
        const unitCode = unitOpt ? (unitOpt.dataset.code || '').toLowerCase() : '';
        const name     = nameInput.value.trim();

        // Build suggestion only if both are present and blade path is empty / was auto-set
        if (unitCode && name && !bladeInput.dataset.manuallyEdited) {
            // Strip unit code prefix from name if present (e.g. "PHYSIO - Doctor View" → "doctor-view")
            let suffix = name;
            const upperCode = unitOpt.dataset.code || '';
            const re = new RegExp('^' + upperCode + '[\\s\\-–—]*', 'i');
            suffix = suffix.replace(re, '').trim();
            const slug = toSlug(suffix);
            bladeInput.value = slug ? 'clinical.' + unitCode + '.' + slug : '';

            // Also suggest code
            if (!codeInput.dataset.manuallyEdited && slug) {
                codeInput.value = unitCode + '-' + slug;
            }
        }
        updatePreview();
    }

    function updatePreview() {
        const path = bladeInput.value.trim();
        bladePreview.textContent = path
            ? 'resources/views/' + path.replace(/\./g, '/') + '.blade.php'
            : 'resources/views/…';
    }

    unitSelect.addEventListener('change', updateBladeSuggestion);
    nameInput.addEventListener('input', updateBladeSuggestion);

    bladeInput.addEventListener('input', function () {
        this.dataset.manuallyEdited = '1';
        updatePreview();
    });
    codeInput.addEventListener('input', function () {
        this.dataset.manuallyEdited = '1';
    });

    // Pre-select unit template when launched via row "Add View" button
    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (btn && btn.dataset.unitId) {
            unitSelect.value = btn.dataset.unitId;
            delete bladeInput.dataset.manuallyEdited;
            delete codeInput.dataset.manuallyEdited;
            updateBladeSuggestion();
        }
        updatePreview();
    });

    updatePreview();

    // Re-open modal if server returned validation errors
    @if($errors->any() && old('unit_template_id'))
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(modal).show();
    });
    @endif
})();
</script>
@endpush
