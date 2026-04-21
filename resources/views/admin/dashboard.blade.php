@extends('layouts.admin')
@section('title', 'Overview')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Overview</h4>
        <small class="text-muted">Welcome back, {{ Auth::user()->name }}</small>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-diagram-3-fill text-primary fs-4"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold">{{ $institutionCount }}</div>
                    <div class="text-muted small">Institutions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 p-3">
                    <i class="bi bi-building text-success fs-4"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold">{{ $unitCount }}</div>
                    <div class="text-muted small">Units</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                    <i class="bi bi-layers-fill text-warning fs-4"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold">{{ $viewCount }}</div>
                    <div class="text-muted small">Clinical Views</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-info bg-opacity-10 p-3">
                    <i class="bi bi-people-fill text-info fs-4"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold">{{ $userCount }}</div>
                    <div class="text-muted small">Clinical Users</div>
                </div>
            </div>
        </div>
    </div>
</div>

<h6 class="text-muted fw-semibold mb-3">Quick Links</h6>
<div class="row g-3">
    @foreach([
        ['route' => 'admin.hierarchy.index',      'icon' => 'bi-diagram-3-fill',       'color' => 'primary', 'label' => 'Hierarchy Management',  'desc' => 'Manage institution parent-child structure'],
        ['route' => 'admin.unit-templates.index', 'icon' => 'bi-grid-3x3-gap-fill',    'color' => 'secondary','label' => 'Unit Templates',         'desc' => 'View available unit types'],
        ['route' => 'admin.view-templates.index', 'icon' => 'bi-layout-text-sidebar',  'color' => 'secondary','label' => 'View Templates',         'desc' => 'View available view types'],
        ['route' => 'admin.units.index',          'icon' => 'bi-building',             'color' => 'success', 'label' => 'Units Management',      'desc' => 'Assign units to institutions'],
        ['route' => 'admin.views.index',          'icon' => 'bi-layers-fill',          'color' => 'warning', 'label' => 'Views Management',      'desc' => 'Assign views to units'],
        ['route' => 'admin.users.index',          'icon' => 'bi-people-fill',          'color' => 'info',    'label' => 'User Management',       'desc' => 'Create and assign clinical users'],
        ['route' => 'admin.system.index',         'icon' => 'bi-gear-fill',            'color' => 'dark',    'label' => 'System Management',     'desc' => 'Application configuration'],
    ] as $item)
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="{{ route($item['route']) }}" class="card border-0 shadow-sm text-decoration-none h-100 card-hover">
            <div class="card-body">
                <i class="bi {{ $item['icon'] }} text-{{ $item['color'] }} fs-4 mb-2 d-block"></i>
                <div class="fw-semibold text-dark">{{ $item['label'] }}</div>
                <div class="text-muted small">{{ $item['desc'] }}</div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endsection
