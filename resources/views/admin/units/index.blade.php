@extends('layouts.admin')
@section('title', 'Units Management')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-building me-2 text-primary"></i>Units Management</h4>

<div class="row g-3">
    {{-- Left: Institution tree --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-2 fw-semibold small text-uppercase text-muted">
                Institutions
            </div>
            <div class="card-body p-0" style="max-height:70vh; overflow-y:auto;">
                @if($rootInstitutions->isEmpty())
                    <p class="text-muted p-3 mb-0 small">No institutions. Add from Hierarchy Management.</p>
                @else
                    <div class="list-group list-group-flush">
                        @include('admin.partials._institution_tree', [
                            'institutions' => $rootInstitutions,
                            'depth'        => 0,
                            'selectedId'   => $selectedInstitution?->id,
                            'paramName'    => 'institution_id',
                            'baseRoute'    => route('admin.units.index'),
                        ])
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Units for selected institution --}}
    <div class="col-md-8">
        @if($selectedInstitution)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-2">
                    <span class="fw-semibold">
                        <i class="bi bi-building text-muted me-1"></i>{{ $selectedInstitution->name }}
                    </span>
                </div>
                <div class="card-body">
                    {{-- Add Unit Form --}}
                    <form action="{{ route('admin.units.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="institution_id" value="{{ $selectedInstitution->id }}">
                        <div class="row g-2 align-items-end mb-4">
                            <div class="col-4">
                                <label class="form-label form-label-sm fw-medium mb-1">Unit Name</label>
                                <input type="text" name="name" class="form-control form-control-sm"
                                       placeholder="e.g., GMC Akurana" required>
                            </div>
                            <div class="col-2">
                                <label class="form-label form-label-sm fw-medium mb-1">Unit No.</label>
                                <input type="text" name="unit_number" class="form-control form-control-sm"
                                       placeholder="e.g., 01">
                            </div>
                            <div class="col-4">
                                <label class="form-label form-label-sm fw-medium mb-1">Unit Type</label>
                                <select name="unit_template_id" class="form-select form-select-sm" required>
                                    <option value="">Select type...</option>
                                    @foreach($unitTemplates as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-plus-lg me-1"></i>Add
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Units Table --}}
                    @if($units->isEmpty())
                        <p class="text-muted small">No units added to this institution yet.</p>
                    @else
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Unit Name</th>
                                    <th>Type</th>
                                    <th style="width:130px;">Unit No.</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($units as $unit)
                                <tr>
                                    <td class="fw-medium">{{ $unit->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $unit->unitTemplate->code }}</span>
                                        <span class="text-muted small ms-1">{{ $unit->unitTemplate->name }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.units.update', $unit) }}" method="POST"
                                              class="d-flex gap-1 align-items-center">
                                            @csrf @method('PATCH')
                                            <input type="text" name="unit_number"
                                                   value="{{ $unit->unit_number }}"
                                                   class="form-control form-control-sm py-0"
                                                   style="width:70px;"
                                                   placeholder="—">
                                            <button class="btn btn-outline-secondary btn-sm py-0 px-1" title="Save">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm py-0"
                                                    data-confirm-body="Delete &quot;{{ $unit->name }}&quot;?"
                                                    onclick="confirmDialog({title:'Delete Unit', body:this.dataset.confirmBody, confirmText:'Delete', confirmClass:'btn-danger', icon:'bi-trash3-fill text-danger'}, () => this.closest('form').submit())">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-arrow-left-circle" style="font-size:3rem; opacity:.3;"></i>
                    <p class="mt-3 mb-0">Select an institution from the left to manage its units.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
