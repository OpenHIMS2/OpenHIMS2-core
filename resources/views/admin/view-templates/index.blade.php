@extends('layouts.admin')
@section('title', 'View Templates')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-layout-text-sidebar me-2 text-primary"></i>View Templates</h4>
    <p class="text-muted small mt-1">Static list of clinical view templates grouped by unit type.</p>
</div>

@foreach($unitTemplates as $unitTemplate)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-2">
        <span class="badge bg-primary">{{ $unitTemplate->code }}</span>
        <span class="fw-semibold">{{ $unitTemplate->name }}</span>
        <span class="badge bg-light text-dark border ms-auto">{{ $unitTemplate->viewTemplates->count() }} views</span>
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
    View templates are predefined in the system and cannot be modified from this interface.
</p>
@endsection
