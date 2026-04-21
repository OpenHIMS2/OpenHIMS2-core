@extends('layouts.admin')
@section('title', 'Views Management')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-layers-fill me-2 text-primary"></i>Views Management</h4>

<div class="row g-3">
    {{-- Panel 1: Institutions --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-2 fw-semibold small text-uppercase text-muted">
                Institutions
            </div>
            <div class="card-body p-0" style="max-height:75vh; overflow-y:auto;">
                @if($rootInstitutions->isEmpty())
                    <p class="text-muted p-3 small mb-0">No institutions found.</p>
                @else
                    <div class="list-group list-group-flush">
                        @include('admin.partials._institution_tree', [
                            'institutions' => $rootInstitutions,
                            'depth'        => 0,
                            'selectedId'   => $selectedInstitution?->id,
                            'paramName'    => 'institution_id',
                            'baseRoute'    => route('admin.views.index'),
                        ])
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Panel 2: Units --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-2 fw-semibold small text-uppercase text-muted">
                Units
                @if($selectedInstitution)
                    <span class="fw-normal text-body">— {{ $selectedInstitution->name }}</span>
                @endif
            </div>
            <div class="card-body p-0" style="max-height:75vh; overflow-y:auto;">
                @if(!$selectedInstitution)
                    <div class="text-center text-muted py-4 px-3">
                        <i class="bi bi-arrow-left-circle" style="font-size:2rem; opacity:.3;"></i>
                        <p class="mt-2 small">Select an institution</p>
                    </div>
                @elseif($units->isEmpty())
                    <p class="text-muted p-3 small mb-0">No units in this institution.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($units as $unit)
                        <a href="{{ route('admin.views.index', ['institution_id' => $selectedInstitution->id, 'unit_id' => $unit->id]) }}"
                           class="list-group-item list-group-item-action d-flex align-items-center gap-2
                                  {{ ($selectedUnit?->id == $unit->id) ? 'active' : '' }}"
                           style="font-size:.875rem;">
                            <span class="badge bg-secondary">{{ $unit->unitTemplate->code }}</span>
                            {{ $unit->name }}
                        </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Panel 3: Views for selected unit --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-2 fw-semibold small text-uppercase text-muted">
                Views
                @if($selectedUnit)
                    <span class="fw-normal text-body">— {{ $selectedUnit->name }}</span>
                @endif
            </div>
            <div class="card-body" style="max-height:75vh; overflow-y:auto;">
                @if(!$selectedUnit)
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-arrow-left-circle" style="font-size:2rem; opacity:.3;"></i>
                        <p class="mt-2 small">Select a unit</p>
                    </div>
                @else
                    {{-- Add View Form --}}
                    <form action="{{ route('admin.views.store') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="unit_id" value="{{ $selectedUnit->id }}">
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label form-label-sm fw-medium mb-1">View Name</label>
                                <input type="text" name="name" class="form-control form-control-sm"
                                       placeholder="e.g., GMC Doctor View" required>
                            </div>
                            <div class="col-8">
                                <label class="form-label form-label-sm fw-medium mb-1">View Template</label>
                                <select name="view_template_id" class="form-select form-select-sm" required>
                                    <option value="">Select template...</option>
                                    @foreach($viewTemplates as $vt)
                                        <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 d-flex align-items-end">
                                <button class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-plus-lg me-1"></i>Add View
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Views list --}}
                    @if($unitViews->isEmpty())
                        <p class="text-muted small">No views assigned to this unit yet.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($unitViews as $uv)
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                                <div>
                                    <div class="fw-medium">{{ $uv->name }}</div>
                                    <small class="text-muted">{{ $uv->viewTemplate->name }}</small>
                                </div>
                                <form action="{{ route('admin.views.destroy', $uv) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm py-0"
                                            onclick="confirmDialog({title:'Delete View', body:'Delete this view? Users assigned to it will lose access.', confirmText:'Delete', confirmClass:'btn-danger', icon:'bi-trash3-fill text-danger'}, () => this.closest('form').submit())">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
