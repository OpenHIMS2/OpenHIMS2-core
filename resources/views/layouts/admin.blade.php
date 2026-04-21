<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenHIMS2 &mdash; @yield('title', 'Admin')</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        html, body { height: 100%; }
        body { display: flex; flex-direction: column; background: #f0f2f5; }
        .sidebar {
            width: 250px;
            min-width: 250px;
            background: #fff;
            border-right: 1px solid #dee2e6;
            min-height: calc(100vh - 48px);
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: #4b5563;
            padding: .45rem .75rem;
            border-radius: .375rem;
            margin-bottom: 2px;
            font-size: .875rem;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover { background: #e9ecef; color: #0d6efd; }
        .sidebar .nav-link.active { background: #dbeafe; color: #1d4ed8; font-weight: 500; }
        .sidebar .nav-link i { font-size: .95rem; }
        .content-area { flex: 1; min-width: 0; padding: 1.5rem; overflow-y: auto; }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts._navbar', [
        'navContextTitle' => Auth::user()->institution?->name ?? 'OpenHIMS2',
    ])

    <div class="d-flex flex-grow-1">
        <aside class="sidebar p-3 d-flex flex-column">
            <p class="text-uppercase text-muted fw-semibold mb-2 px-1" style="font-size:.65rem;letter-spacing:.08em;">Navigation</p>
            <nav class="nav flex-column flex-grow-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill me-2"></i>Overview
                </a>
                <a href="{{ route('admin.hierarchy.index') }}"
                   class="nav-link {{ request()->routeIs('admin.hierarchy*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3-fill me-2"></i>Hierarchy Management
                </a>
                <a href="{{ route('admin.unit-templates.index') }}"
                   class="nav-link {{ request()->routeIs('admin.unit-templates*') ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i>Unit Templates
                </a>
                <a href="{{ route('admin.view-templates.index') }}"
                   class="nav-link {{ request()->routeIs('admin.view-templates*') ? 'active' : '' }}">
                    <i class="bi bi-layout-text-sidebar me-2"></i>View Templates
                </a>
                <a href="{{ route('admin.units.index') }}"
                   class="nav-link {{ request()->routeIs('admin.units*') ? 'active' : '' }}">
                    <i class="bi bi-building me-2"></i>Units Management
                </a>
                <a href="{{ route('admin.views.index') }}"
                   class="nav-link {{ request()->routeIs('admin.views*') ? 'active' : '' }}">
                    <i class="bi bi-layers-fill me-2"></i>Views Management
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill me-2"></i>User Management
                </a>
                <a href="{{ route('admin.drugs.index') }}"
                   class="nav-link {{ request()->routeIs('admin.drugs*') ? 'active' : '' }}">
                    <i class="bi bi-capsule-pill me-2"></i>Drugs Management
                </a>
                <a href="{{ route('admin.terminology.index') }}"
                   class="nav-link {{ request()->routeIs('admin.terminology*') ? 'active' : '' }}">
                    <i class="bi bi-journal-medical me-2"></i>Terminology Management
                </a>
                <a href="{{ route('admin.system.index') }}"
                   class="nav-link {{ request()->routeIs('admin.system*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill me-2"></i>System Management
                </a>
            </nav>
            <div class="mt-auto pt-3 border-top">
                <small class="text-muted px-1" style="font-size:.65rem;">OpenHIMS2</small>
            </div>
        </aside>

        <main class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3 py-2">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3 py-2">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @include('layouts._confirm_modal')
    @stack('scripts')
</body>
</html>
