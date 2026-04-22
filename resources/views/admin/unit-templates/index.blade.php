@extends('layouts.admin')
@section('title', 'Unit Templates')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Unit Templates</h4>
        <p class="text-muted small mt-1">Clinical unit types available in the system. System templates are protected.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUnitTemplateModal">
        <i class="bi bi-plus-lg me-1"></i> Add Unit Template
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

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Code</th>
                    <th>Unit Template Name</th>
                    <th class="text-center">Units Created</th>
                    <th class="text-center">Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td class="ps-4">
                        <span class="badge bg-primary fs-6 fw-semibold">{{ $template->code }}</span>
                    </td>
                    <td class="fw-medium">{{ $template->name }}</td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">{{ $template->units_count }}</span>
                    </td>
                    <td class="text-center">
                        @if($template->is_system)
                            <span class="badge bg-secondary">System</span>
                        @else
                            <span class="badge bg-info text-dark">Custom</span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        @unless($template->is_system)
                        <form method="POST" action="{{ route('admin.unit-templates.destroy', $template) }}"
                              onsubmit="return confirm('Delete unit template \'{{ $template->name }}\'? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    @if($template->units_count) disabled title="Cannot delete — units exist" @endif>
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
    </div>
</div>

<p class="text-muted small mt-3">
    <i class="bi bi-info-circle me-1"></i>
    System templates are seeded and cannot be deleted. Custom templates can be removed if no units are using them.
</p>

{{-- ── Add Unit Template Modal ─────────────────────────────────────── --}}
<div class="modal fade" id="addUnitTemplateModal" tabindex="-1" aria-labelledby="addUnitTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('admin.unit-templates.store') }}">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="addUnitTemplateModalLabel">
                        <i class="bi bi-plus-circle me-2 text-primary"></i>Add Unit Template
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Template Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                               placeholder="e.g. Physiotherapy Clinic" maxlength="100"
                               value="{{ old('name') }}">
                        <div class="form-text">A descriptive name for this type of clinical unit.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" required
                               placeholder="e.g. PHYSIO" maxlength="20"
                               value="{{ old('code') }}"
                               style="letter-spacing:.05em;">
                        <div class="form-text">Short uppercase identifier (max 20 chars). Must be unique.</div>
                    </div>
                    <div class="alert alert-info border-0 py-2 small mb-0">
                        <i class="bi bi-lightbulb me-1"></i>
                        After creating the unit template you can add View Templates to it from the
                        <strong>View Templates</strong> page.
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Create Unit Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-uppercase the code field as user types
    document.querySelector('input[name="code"]')?.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });
    // Re-open modal if validation failed
    @if($errors->any() && old('name'))
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('addUnitTemplateModal')).show();
    });
    @endif
</script>
@endpush
