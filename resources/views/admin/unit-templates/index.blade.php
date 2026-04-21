@extends('layouts.admin')
@section('title', 'Unit Templates')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Unit Templates</h4>
    <p class="text-muted small mt-1">Static list of clinical unit types available in the system.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Code</th>
                    <th>Unit Template Name</th>
                    <th class="text-center">Units Created</th>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<p class="text-muted small mt-3">
    <i class="bi bi-info-circle me-1"></i>
    Unit templates are predefined in the system and cannot be added or removed from this interface.
</p>
@endsection
