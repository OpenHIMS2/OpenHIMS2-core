@extends('layouts.clinical')
@section('title', $pageTitle ?? 'GP — Pharmacist')

@push('styles')
<style>
/* ── Layout ─────────────────────────────────────────────────────────────── */
.ph-queue-col  { max-height: calc(100vh - 260px); overflow-y: auto; }
.ph-rx-col     { max-height: calc(100vh - 260px); overflow-y: auto; }
.ph-stock-wrap { max-height: calc(100vh - 380px); overflow-y: auto; }

/* ── Patient cards ───────────────────────────────────────────────────────── */
.patient-card {
    cursor: pointer;
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    transition: border-color .15s, box-shadow .15s;
    background: #fff;
}
.patient-card:hover  { border-color: #94a3b8; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
.patient-card.active { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,.15); background:#f0f7ff; }
.patient-card.dispensed-card { opacity: .65; background: #f8fdf8; }
.patient-card .visit-num {
    width: 2.2rem; height: 2.2rem; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .85rem; flex-shrink: 0;
}

/* ── Category colour system ──────────────────────────────────────────────── */
.cat-opd   .visit-num { background:#dbeafe; color:#1d4ed8; }
.cat-new   .visit-num { background:#dcfce7; color:#15803d; }
.cat-rec   .visit-num { background:#e0f2fe; color:#0369a1; }
.cat-urg   .visit-num { background:#fee2e2; color:#b91c1c; }

/* ── Drug status toggle buttons ──────────────────────────────────────────── */
.btn-presc { font-size: .7rem; padding: .2rem .5rem; }
.btn-xs    { font-size: .7rem; padding: .15rem .45rem; border-radius: .3rem; }
.drug-qty  { width: 56px; font-size: .8rem; padding: .2rem .35rem; }
.stock-ok-badge    { font-size: .7rem; padding: .2rem .5rem; }

/* ── Section pills in drug table ─────────────────────────────────────────── */
.section-divider {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: .3rem; margin: .8rem 0 .4rem;
}

/* ── Stock status badges ─────────────────────────────────────────────────── */
.status-ok         { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
.status-low        { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
.status-depleted,
.status-out_of_stock { background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; }
.status-expired    { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; text-decoration:line-through; }

/* ── Alert cards clickable ───────────────────────────────────────────────── */
.alert-card { cursor: pointer; transition: transform .12s, box-shadow .12s; }
.alert-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }

/* ── Drug autocomplete dropdown ──────────────────────────────────────────── */
.drug-dd { position: absolute; top: 100%; left: 0; right: 0; z-index: 9999;
           background: #fff; border: 1px solid #dee2e6; border-radius: .375rem;
           max-height: 180px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.drug-dd .dd-item { padding: .45rem .75rem; cursor: pointer; font-size: .875rem; }
.drug-dd .dd-item:hover { background: #f1f5f9; }

/* ── Prescription "completed" overlay ────────────────────────────────────── */
.rx-done-banner {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    border: 1px solid #86efac; border-radius: .5rem;
    padding: .8rem 1.2rem; display: flex; align-items: center; gap: .75rem;
}

/* ── Misc ────────────────────────────────────────────────────────────────── */
.allergy-badge { background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;
                 border-radius:.3rem; padding:.1rem .5rem; font-size:.75rem; }
.section-pill  { font-size:.65rem; font-weight:700; text-transform:uppercase;
                 padding:.15rem .5rem; border-radius:.25rem; letter-spacing:.04em; }
.section-pill.clinic { background:#dbeafe; color:#1e40af; }
.section-pill.management { background:#f3e8ff; color:#6b21a8; }
.search-result-item { padding:.5rem .75rem; cursor:pointer; border-bottom:1px solid #f1f5f9; }
.search-result-item:hover { background:#f8fafc; }
.search-result-item:last-child { border-bottom: none; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  Page header                                                            --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 bg-warning bg-opacity-10 p-3">
            <i class="bi bi-capsule-pill text-warning fs-3"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0">GP Pharmacist</h4>
            <p class="text-muted mb-0 small">
                <i class="bi bi-building me-1"></i>{{ $unit->name }}
                &bull;
                <i class="bi bi-geo-alt me-1"></i>{{ $unit->institution->name }}
            </p>
        </div>
    </div>

    {{-- Header alert badges --}}
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="badge rounded-pill bg-warning text-dark py-2 px-3 d-none" id="badge-expiring"
              title="Expiring within 30 days" style="cursor:pointer;" onclick="switchToStock('expiring')">
            <i class="bi bi-calendar-x me-1"></i><span id="badge-expiring-count">0</span> Expiring
        </span>
        <span class="badge rounded-pill bg-info text-dark py-2 px-3 d-none" id="badge-low"
              title="Near out of stock" style="cursor:pointer;" onclick="switchToStock('low')">
            <i class="bi bi-exclamation-triangle me-1"></i><span id="badge-low-count">0</span> Low
        </span>
        <span class="badge rounded-pill bg-danger py-2 px-3 d-none" id="badge-oos"
              title="Out of stock" style="cursor:pointer;" onclick="switchToStock('oos')">
            <i class="bi bi-x-circle me-1"></i><span id="badge-oos-count">0</span> OOS
        </span>
        <button class="btn btn-sm btn-outline-secondary" onclick="loadQueue(); loadAlerts();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  Main nav tabs                                                          --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<ul class="nav nav-tabs" id="mainTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="tab-queue-btn"
                data-bs-toggle="tab" data-bs-target="#tab-queue" type="button">
            <i class="bi bi-people-fill me-1 text-primary"></i>
            Queue &amp; Dispense
            <span id="queue-pending-badge" class="badge bg-danger ms-1 d-none">0</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-stock-btn"
                data-bs-toggle="tab" data-bs-target="#tab-stock" type="button">
            <i class="bi bi-boxes me-1 text-success"></i>
            My Stock
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-restock-btn"
                data-bs-toggle="tab" data-bs-target="#tab-restock" type="button">
            <i class="bi bi-box-arrow-in-down me-1 text-warning"></i>
            Restock
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-log-btn"
                data-bs-toggle="tab" data-bs-target="#tab-log" type="button">
            <i class="bi bi-journal-text me-1 text-secondary"></i>
            Log
        </button>
    </li>
</ul>

<div class="tab-content border border-top-0 rounded-bottom shadow-sm bg-white p-3">

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  TAB 1 — Queue & Dispense                                              --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade show active" id="tab-queue" role="tabpanel">
<div class="row g-0">

    {{-- ── Left: Queue column ──────────────────────────────────────────── --}}
    <div class="col-lg-4 pe-lg-3 border-end">

        {{-- Search bar --}}
        <div class="mb-2 position-relative">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                <input type="search" id="patient-search"
                       class="form-control border-start-0"
                       placeholder="Queue no., clinic no., or name…">
            </div>
            <div id="search-results" class="position-absolute start-0 end-0 bg-white border rounded shadow"
                 style="display:none; z-index:1000; top:100%; max-height:250px; overflow-y:auto;"></div>
        </div>

        {{-- Category filter pills --}}
        <div class="d-flex flex-wrap gap-1 mb-2" id="cat-filters">
            <button class="btn btn-sm btn-warning px-2 py-1" data-cat="all">
                All <span class="badge bg-white text-dark ms-1" id="cnt-all">0</span>
            </button>
            <button class="btn btn-sm btn-outline-primary px-2 py-1" data-cat="opd">
                OPD <span class="badge bg-primary ms-1" id="cnt-opd">0</span>
            </button>
            <button class="btn btn-sm btn-outline-success px-2 py-1" data-cat="new_clinic_visit">
                New <span class="badge bg-success ms-1" id="cnt-new">0</span>
            </button>
            <button class="btn btn-sm btn-outline-info px-2 py-1 text-dark" data-cat="recurrent_clinic_visit">
                Recurrent <span class="badge bg-info ms-1" id="cnt-rec">0</span>
            </button>
            <button class="btn btn-sm btn-outline-danger px-2 py-1" data-cat="urgent">
                Urgent <span class="badge bg-danger ms-1" id="cnt-urg">0</span>
            </button>
        </div>

        {{-- Queue list --}}
        <div id="queue-list" class="ph-queue-col">
            <div class="text-center text-muted py-5 small">
                <div class="spinner-border spinner-border-sm mb-2"></div><br>Loading queue…
            </div>
        </div>
    </div>

    {{-- ── Right: Prescription panel ───────────────────────────────────── --}}
    <div class="col-lg-8 ps-lg-3 ph-rx-col" id="rx-panel">
        <div class="text-center text-muted py-5">
            <i class="bi bi-receipt" style="font-size:3.5rem; opacity:.15;"></i>
            <p class="mt-3 mb-0">Select a patient from the queue<br>to view and process their prescription.</p>
        </div>
    </div>

</div>
</div><!-- /tab-queue -->

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  TAB 2 — Stock Management                                              --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade" id="tab-stock" role="tabpanel">

    {{-- Alert summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 alert-card bg-warning bg-opacity-10" onclick="loadStock('expiring')">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-warning bg-opacity-25 p-2">
                        <i class="bi bi-calendar-x text-warning fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-3" id="count-expiring">—</div>
                        <div class="small text-muted">Expiring within 30 days</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 alert-card bg-info bg-opacity-10" onclick="loadStock('low')">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-info bg-opacity-25 p-2">
                        <i class="bi bi-exclamation-triangle text-info fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-3" id="count-low">—</div>
                        <div class="small text-muted">Near out of stock</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 alert-card bg-danger bg-opacity-10" onclick="loadStock('oos')">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-danger bg-opacity-25 p-2">
                        <i class="bi bi-x-circle text-danger fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-3" id="count-oos">—</div>
                        <div class="small text-muted">Out of stock</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add drug to stock --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom fw-semibold small">
            <i class="bi bi-plus-circle-fill text-success me-2"></i>Add Drug to Stock
        </div>
        <div class="card-body py-2">
            <div id="stock-form-feedback" class="alert py-1 px-2 small mb-2" style="display:none;"></div>
            <form id="add-stock-form" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label small mb-1">Drug Name <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" name="drug_name" id="stock-drug-name"
                               class="form-control form-control-sm"
                               placeholder="Type to search…" autocomplete="off" required>
                        <div id="stock-drug-dd" class="drug-dd" style="display:none;"></div>
                        <div id="stock-drug-name-feedback" class="invalid-feedback" style="font-size:.75rem;"></div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Qty <span class="text-danger">*</span></label>
                    <input type="number" name="initial_amount"
                           class="form-control form-control-sm" placeholder="e.g. 100" min="1" required>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control form-control-sm">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Low-stock Alert</label>
                    <input type="number" name="low_stock_threshold"
                           class="form-control form-control-sm" value="10" min="1">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Notes</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Optional">
                </div>
                <div class="col-sm-1">
                    <button type="submit" id="add-stock-btn" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Stock table with filter tabs --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex gap-1 flex-wrap" id="stock-filter-tabs">
                    <button class="btn btn-sm btn-warning" data-filter="all">All Stock</button>
                    <button class="btn btn-sm btn-outline-warning" data-filter="expiring">
                        <i class="bi bi-calendar-x me-1"></i>Expiring Soon
                    </button>
                    <button class="btn btn-sm btn-outline-info" data-filter="low">
                        <i class="bi bi-exclamation-triangle me-1"></i>Near OOS
                    </button>
                    <button class="btn btn-sm btn-outline-danger" data-filter="oos">
                        <i class="bi bi-x-circle me-1"></i>Out of Stock
                    </button>
                </div>
                <button class="btn btn-sm btn-outline-secondary" onclick="loadStock(currentStockFilter)">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="ph-stock-wrap" id="stock-table-wrap">
                <div class="text-center text-muted py-4 small">
                    <div class="spinner-border spinner-border-sm mb-1"></div><br>Loading stock…
                </div>
            </div>
        </div>
    </div>

</div><!-- /tab-stock -->

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  TAB 3 — Restock                                                        --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade" id="tab-restock" role="tabpanel">

    {{-- Add new drug --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom fw-semibold small">
            <i class="bi bi-plus-circle-fill text-primary me-2"></i>Add New Drug to Stock
        </div>
        <div class="card-body py-2">
            <div id="restock-new-feedback" class="alert py-1 px-2 small mb-2" style="display:none;"></div>
            <form id="restock-new-form" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label small mb-1">Drug Name <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" name="drug_name" id="restock-new-drug-name"
                               class="form-control form-control-sm"
                               placeholder="Type to search…" autocomplete="off" required>
                        <div id="restock-new-drug-dd" class="drug-dd" style="display:none;"></div>
                        <div class="invalid-feedback" style="font-size:.75rem;">Already in stock — use the table below to restock.</div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Qty <span class="text-danger">*</span></label>
                    <input type="number" name="initial_amount"
                           class="form-control form-control-sm" placeholder="e.g. 100" min="1" required>
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control form-control-sm">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Low-stock Alert</label>
                    <input type="number" name="low_stock_threshold"
                           class="form-control form-control-sm" value="10" min="1">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small mb-1">Notes</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Optional">
                </div>
                <div class="col-sm-1">
                    <button type="submit" id="restock-new-btn" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Restock existing drugs --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span class="fw-semibold small">
                    <i class="bi bi-box-arrow-in-down text-success me-2"></i>Restock Existing Drugs
                    <span class="text-muted fw-normal ms-1" style="font-size:.75rem;">— enter add quantity and click <i class="bi bi-plus-lg"></i></span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <input type="search" id="restock-search"
                           class="form-control form-control-sm"
                           placeholder="Filter drugs…" style="width:200px;">
                    <button class="btn btn-sm btn-outline-secondary" onclick="loadRestock()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div style="max-height: calc(100vh - 480px); overflow-y: auto;" id="restock-table-wrap">
                <div class="text-center text-muted py-4 small">
                    <div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…
                </div>
            </div>
        </div>
    </div>

</div><!-- /tab-restock -->

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  TAB 4 — Log                                                            --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade" id="tab-log" role="tabpanel">

    {{-- Date range filter --}}
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <label class="form-label small mb-0 fw-semibold text-muted">Date range:</label>
        <input type="date" id="log-from" class="form-control form-control-sm" style="width:150px;">
        <span class="text-muted small">to</span>
        <input type="date" id="log-to"   class="form-control form-control-sm" style="width:150px;">
        <button class="btn btn-sm btn-outline-secondary" onclick="loadLog()">
            <i class="bi bi-search me-1"></i>Filter
        </button>
        <button class="btn btn-sm btn-link text-muted p-0 ms-1" onclick="setLogToday(); loadLog();">
            Today
        </button>
    </div>

    {{-- Consumption --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom fw-semibold small">
            <i class="bi bi-capsule-pill text-danger me-2"></i>Drug Consumption
            <span class="text-muted fw-normal ms-1">— dispensed from this pharmacy's stock</span>
        </div>
        <div class="card-body p-0">
            <div id="log-consumption-wrap" style="max-height:260px;overflow-y:auto;">
                <div class="text-center text-muted py-4 small">
                    <div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…
                </div>
            </div>
        </div>
    </div>

    {{-- Restock History --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom fw-semibold small">
            <i class="bi bi-box-arrow-in-down text-success me-2"></i>Restock History
        </div>
        <div class="card-body p-0">
            <div id="log-restock-wrap" style="max-height:260px;overflow-y:auto;">
                <div class="text-center text-muted py-4 small">
                    <div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…
                </div>
            </div>
        </div>
    </div>

    {{-- Expired Stock --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom fw-semibold small">
            <i class="bi bi-calendar-x text-muted me-2"></i>Expired Stock
            <span class="text-muted fw-normal ms-1">— all expired items currently in your stock list</span>
        </div>
        <div class="card-body p-0">
            <div id="log-expired-wrap" style="max-height:220px;overflow-y:auto;">
                <div class="text-center text-muted py-4 small">
                    <div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…
                </div>
            </div>
        </div>
    </div>

</div><!-- /tab-log -->

</div><!-- /tab-content -->

@endsection

@push('scripts')
<script>
// ──────────────────────────────────────────────────────────────────────────────
// Route constants (injected from Blade)
// ──────────────────────────────────────────────────────────────────────────────
const ROUTES = {
    queue:       '{{ route("clinical.pharmacist.queue",       $unitView->id) }}',
    search:      '{{ route("clinical.pharmacist.search",      $unitView->id) }}',
    alerts:      '{{ route("clinical.pharmacist.alerts",      $unitView->id) }}',
    visitDetail: id => `{{ route("clinical.pharmacist.visit-detail", [$unitView->id, "__ID__"]) }}`.replace('__ID__', id),
    dispense:    id => `{{ route("clinical.pharmacist.dispense",     [$unitView->id, "__ID__"]) }}`.replace('__ID__', id),
    stockIndex:  '{{ route("clinical.pharmacist.stock.index", $unitView->id) }}',
    stockStore:  '{{ route("clinical.pharmacist.stock.store", $unitView->id) }}',
    stockOos:    id => `{{ route("clinical.pharmacist.stock.toggle-oos", [$unitView->id, "__ID__"]) }}`.replace('__ID__', id),
    stockDel:     id => `{{ route("clinical.pharmacist.stock.destroy",    [$unitView->id, "__ID__"]) }}`.replace('__ID__', id),
    stockRestock: id => `{{ route("clinical.pharmacist.stock.restock",   [$unitView->id, "__ID__"]) }}`.replace('__ID__', id),
    drugSearch:  '{{ route("drugs.search") }}',
    log:         '{{ route("clinical.pharmacist.log", $unitView->id) }}',
};
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ──────────────────────────────────────────────────────────────────────────────
// State
// ──────────────────────────────────────────────────────────────────────────────
let queueData        = {};   // { opd: [...], new_clinic_visit: [...], ... }
let queueCounts      = {};
let currentCat       = 'all';
let currentVisitId   = null;
let currentStockFilter = 'all';
let allStockData       = [];   // cached for duplicate check + live filter

// ──────────────────────────────────────────────────────────────────────────────
// Helpers
// ──────────────────────────────────────────────────────────────────────────────
async function apiFetch(url, opts = {}) {
    const res = await fetch(url, {
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(opts.headers ?? {}),
        },
        ...opts,
    });
    if (!res.ok) {
        const text = await res.text();
        let errMsg = `HTTP ${res.status}`;
        try { errMsg = JSON.parse(text).message ?? errMsg; } catch (_) {}
        console.error('[Pharmacy] API error', res.status, url, text.slice(0, 300));
        throw new Error(errMsg);
    }
    const text = await res.text();
    try {
        return JSON.parse(text);
    } catch (_) {
        console.error('[Pharmacy] Non-JSON response from', url, text.slice(0, 300));
        throw new Error('Server returned an unexpected response.');
    }
}

function catMeta(cat) {
    const m = {
        opd:                    { label: 'OPD',                 cls: 'cat-opd', badge: 'bg-primary',   text: 'OPD' },
        new_clinic_visit:       { label: 'New Clinic',          cls: 'cat-new', badge: 'bg-success',   text: 'NEW' },
        recurrent_clinic_visit: { label: 'Recurrent Clinic',    cls: 'cat-rec', badge: 'bg-info text-dark', text: 'REC' },
        urgent:                 { label: 'Urgent',              cls: 'cat-urg', badge: 'bg-danger',     text: 'URG' },
    };
    return m[cat] ?? { label: cat, cls: '', badge: 'bg-secondary', text: cat.substring(0,3).toUpperCase() };
}

function esc(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ──────────────────────────────────────────────────────────────────────────────
// Queue
// ──────────────────────────────────────────────────────────────────────────────
async function loadQueue() {
    try {
        const data = await apiFetch(ROUTES.queue);
        queueData   = data.queue;
        queueCounts = data.counts;
        renderQueueCounts();
        renderQueueList();
    } catch(e) {
        document.getElementById('queue-list').innerHTML =
            `<div class="alert alert-danger m-2 small py-2">Failed to load queue: ${esc(e.message)}</div>`;
    }
}

function renderQueueCounts() {
    const c = queueCounts;
    document.getElementById('cnt-all').textContent = c.total ?? 0;
    document.getElementById('cnt-opd').textContent = c.opd ?? 0;
    document.getElementById('cnt-new').textContent = c.new_clinic_visit ?? 0;
    document.getElementById('cnt-rec').textContent = c.recurrent_clinic_visit ?? 0;
    document.getElementById('cnt-urg').textContent = c.urgent ?? 0;

    const pending = c.pending ?? 0;
    const badge   = document.getElementById('queue-pending-badge');
    badge.textContent = pending;
    badge.classList.toggle('d-none', pending === 0);
}

function renderQueueList() {
    const list = document.getElementById('queue-list');

    let visibleItems = [];
    if (currentCat === 'all') {
        ['opd','new_clinic_visit','recurrent_clinic_visit','urgent'].forEach(cat => {
            (queueData[cat] ?? []).forEach(v => visibleItems.push(v));
        });
    } else {
        visibleItems = queueData[currentCat] ?? [];
    }

    if (visibleItems.length === 0) {
        list.innerHTML = `<div class="text-center text-muted py-4 small">
            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No patients in this queue.</div>`;
        return;
    }

    // Group by category when viewing 'all'
    if (currentCat === 'all') {
        let html = '';
        ['opd','new_clinic_visit','recurrent_clinic_visit','urgent'].forEach(cat => {
            const items = queueData[cat] ?? [];
            if (!items.length) return;
            const meta = catMeta(cat);
            html += `<div class="section-divider mt-2">${esc(meta.label)} <span class="badge ${meta.badge} ms-1">${items.length}</span></div>`;
            items.forEach(v => { html += renderPatientCard(v); });
        });
        list.innerHTML = html;
    } else {
        list.innerHTML = visibleItems.map(v => renderPatientCard(v)).join('');
    }

    // Re-highlight active card
    if (currentVisitId) {
        document.querySelectorAll('.patient-card').forEach(c => {
            c.classList.toggle('active', parseInt(c.dataset.id) === currentVisitId);
        });
    }
}

function renderPatientCard(v) {
    const meta     = catMeta(v.category);
    const ref      = v.clinic_number ? `Clinic #${esc(v.clinic_number)}`
                   : v.opd_number    ? `OPD #${esc(v.opd_number)}`
                   : '';
    const unitTag  = v.unit_name
                   ? `<span class="badge bg-secondary bg-opacity-25 text-secondary border" style="font-size:.6rem;">${esc(v.unit_name)}</span>`
                   : '';
    const disBadge = v.dispensed
        ? `<span class="badge bg-success-subtle text-success border border-success-subtle ms-auto"><i class="bi bi-check2-circle me-1"></i>Dispensed</span>`
        : `<span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-auto">Pending</span>`;

    return `<div class="patient-card mb-2 p-2 ${meta.cls} ${v.dispensed ? 'dispensed-card' : ''}"
                 data-id="${v.id}" onclick="openVisit(${v.id})">
        <div class="d-flex align-items-center gap-2">
            <div class="visit-num">${esc(v.visit_number)}</div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="fw-semibold text-truncate small">${esc(v.patient_name)}</div>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    ${ref ? `<span class="text-muted" style="font-size:.7rem;">${ref}</span>` : ''}
                    ${unitTag}
                </div>
            </div>
            ${disBadge}
        </div>
    </div>`;
}

// ──────────────────────────────────────────────────────────────────────────────
// Patient search
// ──────────────────────────────────────────────────────────────────────────────
let searchTimer = null;
document.getElementById('patient-search').addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (!q) { document.getElementById('search-results').style.display = 'none'; return; }
    searchTimer = setTimeout(() => doSearch(q), 280);
});

document.getElementById('patient-search').addEventListener('blur', function() {
    setTimeout(() => { document.getElementById('search-results').style.display = 'none'; }, 180);
});

async function doSearch(q) {
    try {
        const results = await apiFetch(ROUTES.search + '?q=' + encodeURIComponent(q));
        const el = document.getElementById('search-results');
        if (!results.length) { el.style.display = 'none'; return; }

        el.innerHTML = results.map(v => {
            const ref  = v.clinic_number ? `Clinic #${esc(v.clinic_number)}`
                       : v.opd_number    ? `OPD #${esc(v.opd_number)}` : '';
            const meta = catMeta(v.category);
            return `<div class="search-result-item" onclick="openVisit(${v.id}); document.getElementById('patient-search').value=''; document.getElementById('search-results').style.display='none';">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge ${meta.badge}" style="font-size:.65rem;">#${esc(v.visit_number)}</span>
                    <span class="fw-semibold small">${esc(v.patient_name)}</span>
                    ${ref ? `<span class="text-muted" style="font-size:.75rem;">${ref}</span>` : ''}
                    ${v.dispensed ? '<span class="badge bg-success ms-auto" style="font-size:.65rem;">Done</span>' : ''}
                </div>
            </div>`;
        }).join('');
        el.style.display = 'block';
    } catch(e) { /* silent */ }
}

// ──────────────────────────────────────────────────────────────────────────────
// Category filter
// ──────────────────────────────────────────────────────────────────────────────
document.querySelectorAll('#cat-filters button').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('#cat-filters button').forEach(b => {
            b.className = b.className
                .replace('btn-warning','btn-outline-warning')
                .replace('btn-primary','btn-outline-primary')
                .replace('btn-success','btn-outline-success')
                .replace('btn-info','btn-outline-info')
                .replace('btn-danger','btn-outline-danger');
            if (b.dataset.cat === 'all') b.className = b.className.replace('btn-outline-warning','btn-outline-secondary');
        });
        // Set active style
        let activeCls = this.dataset.cat === 'all' ? 'btn-warning'
                      : this.dataset.cat === 'opd' ? 'btn-primary'
                      : this.dataset.cat === 'new_clinic_visit' ? 'btn-success'
                      : this.dataset.cat === 'recurrent_clinic_visit' ? 'btn-info'
                      : 'btn-danger';
        this.className = this.className
            .replace('btn-outline-secondary','')
            .replace('btn-outline-warning','')
            .replace('btn-outline-primary','')
            .replace('btn-outline-success','')
            .replace('btn-outline-info','')
            .replace('btn-outline-danger','')
            .trim() + ' ' + activeCls;
        currentCat = this.dataset.cat;
        renderQueueList();
    });
});

// ──────────────────────────────────────────────────────────────────────────────
// Prescription receipt
// ──────────────────────────────────────────────────────────────────────────────
async function openVisit(visitId) {
    currentVisitId = visitId;

    // Highlight card
    document.querySelectorAll('.patient-card').forEach(c =>
        c.classList.toggle('active', parseInt(c.dataset.id) === visitId));

    // Switch to queue tab if on stock tab
    const queueTabBtn = document.getElementById('tab-queue-btn');
    if (!queueTabBtn.classList.contains('active')) {
        bootstrap.Tab.getOrCreateInstance(queueTabBtn).show();
    }

    const rxPanel = document.getElementById('rx-panel');
    rxPanel.innerHTML = `<div class="text-center text-muted py-5"><div class="spinner-border text-primary mb-2"></div><br>Loading prescription…</div>`;

    try {
        const data = await apiFetch(ROUTES.visitDetail(visitId));
        renderRxPanel(data);
    } catch(e) {
        rxPanel.innerHTML = `<div class="alert alert-danger m-2">Failed to load prescription: ${esc(e.message)}</div>`;
    }
}

// Frequency → doses per day
function freqPerDay(freq) {
    const map = { mane: 1, nocte: 1, bd: 2, tds: 3, daily: 1, EOD: 0.5, SOS: 0 };
    return map[freq] ?? 0;
}

// Duration string → days  (e.g. "5 days", "2 weeks", "1 months")
function durationToDays(dur) {
    if (!dur) return 0;
    const m = String(dur).match(/^(\d+(?:\.\d+)?)\s*(day|days|week|weeks|month|months)$/i);
    if (!m) return 0;
    const n = parseFloat(m[1]);
    const u = m[2].toLowerCase();
    if (u.startsWith('week'))  return n * 7;
    if (u.startsWith('month')) return n * 30;
    return n;
}

function calcQty(drug) {
    const fpd  = freqPerDay(drug.frequency);
    const days = durationToDays(drug.duration);
    if (!fpd || !days) return 1;
    return Math.max(1, Math.ceil(fpd * days));
}

function renderRxPanel(data) {
    const { visit, patient, note, clinic_drugs, management_drugs } = data;
    const meta  = catMeta(visit.category);
    const allDrugs = management_drugs;

    // ── Patient header ──
    let header = `
    <div class="d-flex align-items-start gap-3 mb-3">
        <div class="visit-num ${meta.cls}" style="width:2.6rem;height:2.6rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.95rem;font-weight:700;flex-shrink:0;">
            ${esc(visit.visit_number)}
        </div>
        <div class="flex-grow-1">
            <h5 class="fw-bold mb-1">${esc(patient.name)}</h5>
            <div class="d-flex flex-wrap gap-1 align-items-center">
                <span class="badge ${meta.badge}">${esc(visit.cat_label)}</span>
                ${visit.unit_name     ? `<span class="badge bg-secondary bg-opacity-25 text-secondary border">${esc(visit.unit_name)}</span>` : ''}
                ${visit.clinic_number ? `<span class="badge bg-light text-dark border">Clinic #${esc(visit.clinic_number)}</span>` : ''}
                ${visit.opd_number    ? `<span class="badge bg-light text-dark border">OPD #${esc(visit.opd_number)}</span>` : ''}
                <span class="text-muted small">
                    ${patient.age ? patient.age + 'y' : ''} ${patient.gender ? '/ ' + patient.gender : ''}
                    ${patient.phn ? ' &bull; PHN: ' + esc(patient.phn) : ''}
                </span>
            </div>
        </div>
        <button class="btn btn-sm btn-outline-secondary flex-shrink-0" onclick="closeRx()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>`;

    // ── Dispensed banner ──
    if (visit.dispensed) {
        header += `<div class="rx-done-banner mb-3">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div><strong>Prescription Completed</strong> <span class="text-muted small ms-2">This visit has been dispensed.</span></div>
        </div>`;
    }

    // ── Vitals row ──
    let vitals = '';
    if (visit.height || visit.weight) {
        vitals = `<div class="d-flex flex-wrap gap-3 mb-2 small text-muted">`;
        if (visit.height) vitals += `<span><i class="bi bi-rulers me-1"></i>${esc(visit.height)} cm</span>`;
        if (visit.weight) vitals += `<span><i class="bi bi-speedometer2 me-1"></i>${esc(visit.weight)} kg</span>`;
        vitals += `</div>`;
    }

    // ── Allergies ──
    let allergyHtml = '';
    if (patient.allergies && patient.allergies.length) {
        allergyHtml = `<div class="alert alert-danger py-2 mb-2 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div class="small"><strong>Allergies:</strong> ${patient.allergies.map(a => `<span class="allergy-badge">${esc(a)}</span>`).join(' ')}</div>
        </div>`;
    }

    // ── Collapsible History ──
    let historyHtml = '';
    if (note) {
        const complaints = (note.presenting_complaints ?? []).join(', ');
        const pmh        = (note.past_medical_history   ?? []).join(', ');
        const mgmt       = (note.management_instruction ?? []).join(' | ');

        if (complaints || pmh || mgmt) {
            historyHtml = `
            <div class="accordion accordion-flush mb-2" id="acc-history">
                <div class="accordion-item border rounded mb-1">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed py-2 small fw-semibold" type="button"
                                data-bs-toggle="collapse" data-bs-target="#hist-collapse">
                            <i class="bi bi-journal-text me-2 text-muted"></i>History &amp; Examination
                        </button>
                    </h2>
                    <div id="hist-collapse" class="accordion-collapse collapse">
                        <div class="accordion-body py-2 small">`;

            if (complaints) historyHtml += `<div class="mb-1"><span class="fw-semibold text-muted">Complaints:</span> ${esc(complaints)}</div>`;
            if (pmh)        historyHtml += `<div class="mb-1"><span class="fw-semibold text-muted">PMH:</span> ${esc(pmh)}</div>`;

            // Exam findings
            const examFields = {
                general_looking:       'General',
                cardiology_findings:   'CVS',
                respiratory_findings:  'RS',
                abdominal_findings:    'Abdomen',
            };
            let examParts = [];
            for (const [field, label] of Object.entries(examFields)) {
                const val = (note[field] ?? []).join(', ');
                if (val) examParts.push(`<strong>${label}:</strong> ${esc(val)}`);
            }
            if (examParts.length) historyHtml += `<div class="mb-1">${examParts.join(' &nbsp;|&nbsp; ')}</div>`;
            if (mgmt) historyHtml += `<div class="mb-1"><span class="fw-semibold text-muted">Instructions:</span> ${esc(mgmt)}</div>`;

            historyHtml += `</div></div></div></div>`;
        }
    }

    // ── Drug table builder ──
    function buildDrugRows(drugs) {
        if (!drugs.length) return `<tr><td colspan="4" class="text-muted text-center small py-2">No drugs prescribed.</td></tr>`;

        return drugs.map(d => {
            const stockBadge = d.in_stock
                ? `<span class="badge stock-ok-badge status-ok"><i class="bi bi-check-lg me-1"></i>${d.stock_remaining} in stock</span>`
                : `<span class="badge stock-ok-badge" style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;"><i class="bi bi-x-lg me-1"></i>Not listed</span>`;

            const alreadyDone = visit.dispensed;
            const initStatus  = d.dispensed_status ?? (d.in_stock ? 'prescribed' : 'os');
            const initQty     = d.dispensed_qty ?? calcQty(d);

            const prescChecked = initStatus === 'prescribed' ? 'checked' : '';
            const osChecked    = initStatus === 'os' ? 'checked' : '';
            const disabled     = alreadyDone ? 'disabled' : '';

            const dur = d.duration ? `<span class="text-muted" style="font-size:.7rem;">${esc(d.duration)}</span>` : '';

            return `<tr data-drug-id="${d.id}" data-stock-id="${d.stock_id ?? ''}"
                        data-in-stock="${d.in_stock ? '1' : '0'}">
                <td style="max-width:180px;">
                    <div class="fw-semibold small text-truncate">${esc(d.formatted)}</div>
                    ${dur}
                </td>
                <td>${stockBadge}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check drug-status" name="dstat-${d.id}"
                               id="presc-${d.id}" value="prescribed" ${prescChecked} ${disabled}>
                        <label class="btn btn-outline-success btn-xs" for="presc-${d.id}">Presc.</label>
                        <input type="radio" class="btn-check drug-status" name="dstat-${d.id}"
                               id="os-${d.id}" value="os" ${osChecked} ${disabled}>
                        <label class="btn btn-outline-danger btn-xs" for="os-${d.id}">OS</label>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control drug-qty" value="${initQty}"
                           min="1" data-drug-id="${d.id}" ${disabled}
                           title="Quantity to dispense">
                </td>
            </tr>`;
        }).join('');
    }

    // ── Drug table (management only) ──
    const drugTableHeader = `
        <thead class="table-light">
            <tr>
                <th style="width:42%;">Drug</th>
                <th>Stock</th>
                <th class="text-center">Status</th>
                <th style="width:60px;">Qty</th>
            </tr>
        </thead>`;

    const drugsHtml = `
    <div class="mb-2">
        <table class="table table-sm table-hover align-middle mb-0">
            ${drugTableHeader}
            <tbody>${buildDrugRows(management_drugs)}</tbody>
        </table>
    </div>`;

    // ── Complete button ──
    let footerHtml = '';
    if (!visit.dispensed && allDrugs.length > 0) {
        footerHtml = `
        <div class="d-flex justify-content-end mt-3 pt-2 border-top gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="closeRx()">Cancel</button>
            <button class="btn btn-success" id="complete-rx-btn" onclick="completePrescription(${visit.id})">
                <i class="bi bi-check-circle-fill me-1"></i>Complete Prescription
            </button>
        </div>`;
    } else if (!visit.dispensed && allDrugs.length === 0) {
        footerHtml = `
        <div class="d-flex justify-content-end mt-3 pt-2 border-top gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="closeRx()">Close</button>
            <button class="btn btn-success" onclick="markNoMeds(${visit.id})">
                <i class="bi bi-check-circle me-1"></i>No Medications — Mark Done
            </button>
        </div>`;
    }

    document.getElementById('rx-panel').innerHTML =
        header + vitals + allergyHtml + historyHtml + drugsHtml + footerHtml;
}

function closeRx() {
    currentVisitId = null;
    document.querySelectorAll('.patient-card').forEach(c => c.classList.remove('active'));
    document.getElementById('rx-panel').innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="bi bi-receipt" style="font-size:3.5rem; opacity:.15;"></i>
            <p class="mt-3 mb-0">Select a patient from the queue<br>to view and process their prescription.</p>
        </div>`;
}

// ──────────────────────────────────────────────────────────────────────────────
// Complete prescription
// ──────────────────────────────────────────────────────────────────────────────
async function completePrescription(visitId) {
    const rows = document.querySelectorAll('#rx-panel tr[data-drug-id]');
    const drugs = [];

    rows.forEach(row => {
        const drugId  = parseInt(row.dataset.drugId);
        const stockId = row.dataset.stockId ? parseInt(row.dataset.stockId) : null;
        const status  = row.querySelector('.drug-status:checked')?.value ?? 'os';
        const qty     = parseInt(row.querySelector('.drug-qty')?.value ?? 1);
        drugs.push({ drug_id: drugId, status, qty, stock_id: status === 'prescribed' ? stockId : null });
    });

    if (!drugs.length) {
        alert('No drugs to dispense.');
        return;
    }

    const btn = document.getElementById('complete-rx-btn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing…'; }

    try {
        await apiFetch(ROUTES.dispense(visitId), {
            method: 'POST',
            body: JSON.stringify({ drugs }),
        });
        // Reload prescription (now shows dispensed state)
        await loadQueue();
        await openVisit(visitId);
        await loadAlerts();
    } catch(e) {
        alert('Error: ' + e.message);
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Complete Prescription'; }
    }
}

async function markNoMeds(visitId) {
    if (!confirm('Mark this visit as dispensed with no medications?')) return;
    try {
        // Send empty drugs array — server will just mark as dispensed
        await apiFetch(ROUTES.dispense(visitId), {
            method: 'POST',
            body: JSON.stringify({ drugs: [] }),
        });
        await loadQueue();
        await openVisit(visitId);
    } catch(e) {
        // If validation fails for empty drugs, just reload queue
        await loadQueue();
        closeRx();
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// Alert badges
// ──────────────────────────────────────────────────────────────────────────────
async function loadAlerts() {
    try {
        const data = await apiFetch(ROUTES.alerts);

        const setCount = (id, count) => {
            document.getElementById(id).textContent = count;
        };

        setCount('badge-expiring-count', data.expiring_soon);
        setCount('badge-low-count',      data.near_oos);
        setCount('badge-oos-count',      data.out_of_stock);
        setCount('count-expiring',       data.expiring_soon);
        setCount('count-low',            data.near_oos);
        setCount('count-oos',            data.out_of_stock);

        document.getElementById('badge-expiring').classList.toggle('d-none', data.expiring_soon === 0);
        document.getElementById('badge-low').classList.toggle('d-none',      data.near_oos === 0);
        document.getElementById('badge-oos').classList.toggle('d-none',      data.out_of_stock === 0);
    } catch(e) { /* silent */ }
}

function switchToStock(filter) {
    bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-stock-btn')).show();
    loadStock(filter);
    // Highlight the right tab button
    document.querySelectorAll('#stock-filter-tabs button').forEach(b => {
        b.className = b.className.replace(/\bbtn-\w+\b/g, match =>
            match === 'btn' ? 'btn' : (match.startsWith('btn-outline-') ? match : 'btn-outline-' + match.replace('btn-','')));
    });
}

// ──────────────────────────────────────────────────────────────────────────────
// Stock management
// ──────────────────────────────────────────────────────────────────────────────
async function loadStock(filter = 'all') {
    currentStockFilter = filter;

    // Sync filter tab button styles
    document.querySelectorAll('#stock-filter-tabs button').forEach(btn => {
        const isActive = btn.dataset.filter === filter;
        const base     = btn.dataset.filter === 'all'      ? 'warning'
                       : btn.dataset.filter === 'expiring' ? 'warning'
                       : btn.dataset.filter === 'low'      ? 'info'
                       : 'danger';
        btn.className  = isActive ? `btn btn-sm btn-${base}` : `btn btn-sm btn-outline-${base}`;
    });

    const wrap = document.getElementById('stock-table-wrap');
    wrap.innerHTML = `<div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…</div>`;

    try {
        const data = await apiFetch(ROUTES.stockIndex + '?filter=' + filter);
        renderStockTable(data.stocks);
    } catch(e) {
        wrap.innerHTML = `<div class="alert alert-danger m-3 small py-2">Error: ${esc(e.message)}</div>`;
    }
}

function filterStockByName(q) {
    const rows = document.querySelectorAll('#stock-table-wrap tbody tr[data-stock-id]');
    const term = q.toLowerCase();
    rows.forEach(row => {
        const name = row.querySelector('td:first-child .fw-semibold')?.textContent?.toLowerCase() ?? '';
        row.style.display = (!term || name.includes(term)) ? '' : 'none';
    });
}

function renderStockTable(stocks) {
    allStockData = stocks;
    const wrap = document.getElementById('stock-table-wrap');

    if (!stocks.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-5 small">
            <i class="bi bi-box-seam fs-2 d-block mb-2 opacity-25"></i>No items found.</div>`;
        return;
    }

    const statusLabel = {
        ok:           ['✓ OK',        'status-ok'],
        low:          ['⚠ Low',        'status-low'],
        depleted:     ['✗ Depleted',   'status-depleted'],
        out_of_stock: ['✗ OOS',        'status-out_of_stock'],
        expired:      ['✗ Expired',    'status-expired'],
    };

    let html = `
    <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
            <tr>
                <th>Drug Name</th>
                <th class="text-center">Initial</th>
                <th class="text-center">Remaining</th>
                <th>Expiry</th>
                <th class="text-center">Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>`;

    stocks.forEach(s => {
        const [sLabel, sCls] = statusLabel[s.stock_status] ?? ['—', ''];
        const expiryText = s.expiry_display
            ? (s.days_until_expiry !== null && s.days_until_expiry < 0
                ? `<span class="text-danger fw-semibold small">${esc(s.expiry_display)} <span class="badge bg-danger">Expired</span></span>`
                : s.days_until_expiry !== null && s.days_until_expiry <= 30
                    ? `<span class="text-warning fw-semibold small">${esc(s.expiry_display)} <span class="badge bg-warning text-dark">${s.days_until_expiry}d</span></span>`
                    : `<span class="small">${esc(s.expiry_display)}</span>`)
            : '<span class="text-muted small">—</span>';

        const oosLabel = s.is_out_of_stock ? 'Mark In Stock' : 'Mark OOS';
        const oosIcon  = s.is_out_of_stock ? 'bi-check-circle' : 'bi-slash-circle';
        const pctUsed  = s.initial_amount > 0 ? Math.round((1 - s.remaining / s.initial_amount) * 100) : 0;
        const pctColor = s.remaining <= 0 ? 'bg-danger'
                       : s.stock_status === 'low' ? 'bg-warning'
                       : 'bg-success';

        html += `
        <tr data-stock-id="${s.id}">
            <td>
                <div class="fw-semibold small">${esc(s.drug_name)}</div>
                ${s.notes ? `<div class="text-muted" style="font-size:.7rem;">${esc(s.notes)}</div>` : ''}
            </td>
            <td class="text-center small">${s.initial_amount}</td>
            <td class="text-center">
                <div class="fw-semibold small">${s.remaining}</div>
                <div class="progress mt-1" style="height:4px;width:50px;margin:0 auto;">
                    <div class="progress-bar ${pctColor}" style="width:${100 - pctUsed}%"></div>
                </div>
            </td>
            <td>${expiryText}</td>
            <td class="text-center">
                <span class="badge ${sCls} stock-ok-badge">${sLabel}</span>
            </td>
            <td class="text-end">
                <button class="btn btn-xs btn-outline-secondary me-1"
                        onclick="toggleOos(${s.id}, this)"
                        title="${oosLabel}">
                    <i class="bi ${oosIcon}"></i>
                </button>
                <button class="btn btn-xs btn-outline-danger"
                        onclick="deleteStock(${s.id})"
                        title="Remove from stock">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        </tr>`;
    });

    html += '</tbody></table>';
    wrap.innerHTML = html;

    // Re-apply live filter if user has typed something in the add-drug field
    const filterTerm = document.getElementById('stock-drug-name').value.trim();
    if (filterTerm) filterStockByName(filterTerm);
}

async function toggleOos(stockId, btn) {
    const origHtml = btn.innerHTML;
    btn.disabled   = true;
    btn.innerHTML  = '<span class="spinner-border spinner-border-sm" style="width:.7rem;height:.7rem;"></span>';

    try {
        const data = await apiFetch(ROUTES.stockOos(stockId), { method: 'PATCH', body: '{}' });
        await loadStock(currentStockFilter);
        await loadAlerts();
    } catch(e) {
        alert('Error: ' + e.message);
        btn.disabled  = false;
        btn.innerHTML = origHtml;
    }
}

async function deleteStock(stockId) {
    if (!confirm('Remove this drug from your stock list? This cannot be undone.')) return;
    try {
        await apiFetch(ROUTES.stockDel(stockId), { method: 'DELETE', body: '{}' });
        await loadStock(currentStockFilter);
        await loadAlerts();
    } catch(e) {
        alert('Error: ' + e.message);
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// Add stock form
// ──────────────────────────────────────────────────────────────────────────────
document.getElementById('add-stock-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn  = document.getElementById('add-stock-btn');

    // Strip empty strings so nullable fields are omitted rather than sent as ""
    const fd   = new FormData(this);
    const body = {};
    fd.forEach((val, key) => { if (val !== '') body[key] = val; });

    if (!body.drug_name || !body.initial_amount) {
        showStockError('Drug name and quantity are required.');
        return;
    }

    // Duplicate check
    const nameToAdd = (body.drug_name).toLowerCase().trim();
    const duplicate = allStockData.find(s => s.drug_name.toLowerCase().trim() === nameToAdd);
    if (duplicate) {
        showStockError(`"${duplicate.drug_name}" is already in your stock (${duplicate.remaining} remaining). Use the Restock tab to add more quantity to an existing drug.`);
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:.7rem;height:.7rem;"></span>';

    try {
        const res = await apiFetch(ROUTES.stockStore, { method: 'POST', body: JSON.stringify(body) });
        this.reset();
        document.getElementById('stock-drug-name').value = '';
        filterStockByName('');   // clear live filter
        // Always switch to 'all' filter so the new item is visible
        await loadStock('all');
        await loadAlerts();
        showStockSuccess(`"${res.stock?.drug_name ?? body.drug_name}" added to stock.`);
    } catch(err) {
        showStockError(err.message || 'Failed to add drug. Please check the form and try again.');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<i class="bi bi-plus-lg"></i>';
    }
});

function showStockSuccess(msg) {
    const el = document.getElementById('stock-form-feedback');
    el.className = 'alert alert-success alert-sm py-1 px-2 small mb-2';
    el.textContent = '✓ ' + msg;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function showStockError(msg) {
    const el = document.getElementById('stock-form-feedback');
    el.className = 'alert alert-danger alert-sm py-1 px-2 small mb-2';
    el.textContent = '✗ ' + msg;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 6000);
}

// Drug name autocomplete for stock form
let stockDrugTimer = null;
const stockInput = document.getElementById('stock-drug-name');
const stockDd    = document.getElementById('stock-drug-dd');

function checkStockDuplicate(name) {
    const feedbackEl = document.getElementById('stock-drug-name-feedback');
    const nameNorm   = name.toLowerCase().trim();
    const duplicate  = nameNorm ? allStockData.find(s => s.drug_name.toLowerCase().trim() === nameNorm) : null;
    if (duplicate) {
        stockInput.classList.add('is-invalid');
        feedbackEl.textContent = `"${duplicate.drug_name}" already exists (${duplicate.remaining} remaining). Use the Restock tab instead.`;
        document.getElementById('add-stock-btn').disabled = true;
    } else {
        stockInput.classList.remove('is-invalid');
        feedbackEl.textContent = '';
        document.getElementById('add-stock-btn').disabled = false;
    }
}

stockInput.addEventListener('input', function() {
    clearTimeout(stockDrugTimer);
    const q = this.value.trim();
    // Live-filter the stock table as user types
    filterStockByName(q);
    // Instant duplicate check
    checkStockDuplicate(q);
    if (q.length < 2) { stockDd.style.display = 'none'; return; }
    stockDrugTimer = setTimeout(async () => {
        try {
            const res  = await fetch(ROUTES.drugSearch + '?q=' + encodeURIComponent(q));
            const data = await res.json();
            if (!data.length) { stockDd.style.display = 'none'; return; }
            stockDd.innerHTML = data.slice(0, 12).map(item => {
                const name = item.name ?? item;
                // Use data-name + onmousedown/preventDefault so the input never
                // loses focus and the value is set before any blur can interfere.
                return `<div class="dd-item" data-name="${esc(name)}"
                             onmousedown="event.preventDefault(); selectStockDrug(this.dataset.name)">
                            ${esc(name)}
                        </div>`;
            }).join('');
            stockDd.style.display = 'block';
        } catch(e) { /* silent */ }
    }, 250);
});

// Hide dropdown on blur; keep table filter until form is submitted or cleared
stockInput.addEventListener('blur', () => {
    setTimeout(() => { stockDd.style.display = 'none'; }, 150);
});

// Hide on Escape
stockInput.addEventListener('keydown', e => {
    if (e.key === 'Escape') stockDd.style.display = 'none';
});

function selectStockDrug(name) {
    stockInput.value = name;
    stockDd.style.display = 'none';
    stockInput.focus();
    checkStockDuplicate(name);
    filterStockByName(name);
}

// ──────────────────────────────────────────────────────────────────────────────
// Stock filter tabs
// ──────────────────────────────────────────────────────────────────────────────
document.querySelectorAll('#stock-filter-tabs button').forEach(btn => {
    btn.addEventListener('click', function() { loadStock(this.dataset.filter); });
});

// ──────────────────────────────────────────────────────────────────────────────
// Bootstrap tab events
// ──────────────────────────────────────────────────────────────────────────────
document.getElementById('tab-stock-btn').addEventListener('shown.bs.tab', function() {
    loadStock(currentStockFilter);
    loadAlerts();
});

// ──────────────────────────────────────────────────────────────────────────────
// Restock tab
// ──────────────────────────────────────────────────────────────────────────────
let restockData = [];

async function loadRestock() {
    const wrap = document.getElementById('restock-table-wrap');
    wrap.innerHTML = `<div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…</div>`;
    try {
        const data = await apiFetch(ROUTES.stockIndex + '?filter=all');
        restockData = data.stocks;
        // Keep allStockData in sync so the My Stock duplicate check also works
        allStockData = data.stocks;
        renderRestockTable();
    } catch(e) {
        wrap.innerHTML = `<div class="alert alert-danger m-3 small py-2">Error: ${esc(e.message)}</div>`;
    }
}

function renderRestockTable() {
    const wrap       = document.getElementById('restock-table-wrap');
    const searchTerm = document.getElementById('restock-search').value.trim().toLowerCase();
    const filtered   = searchTerm ? restockData.filter(s => s.drug_name.toLowerCase().includes(searchTerm)) : restockData;

    if (!filtered.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-5 small">
            <i class="bi bi-box-seam fs-2 d-block mb-2 opacity-25"></i>No drugs in stock.</div>`;
        return;
    }

    let html = `
    <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
            <tr>
                <th>Drug Name</th>
                <th class="text-center">Remaining</th>
                <th>Expiry</th>
                <th class="text-center" style="width:110px;">Add Qty</th>
                <th style="width:140px;">New Expiry</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>`;

    filtered.forEach(s => {
        const expiryText = s.expiry_display
            ? `<span class="small">${esc(s.expiry_display)}</span>`
            : '<span class="text-muted small">—</span>';

        html += `
        <tr data-stock-id="${s.id}">
            <td><div class="fw-semibold small">${esc(s.drug_name)}</div>${s.notes ? `<div class="text-muted" style="font-size:.7rem;">${esc(s.notes)}</div>` : ''}</td>
            <td class="text-center small">${s.remaining}</td>
            <td>${expiryText}</td>
            <td>
                <input type="number" class="form-control form-control-sm restock-qty"
                       placeholder="Qty" min="1">
            </td>
            <td>
                <input type="date" class="form-control form-control-sm restock-expiry">
            </td>
            <td class="text-end">
                <button class="btn btn-xs btn-success" onclick="doRestock(${s.id}, this)" title="Add to stock">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </td>
        </tr>`;
    });

    html += '</tbody></table>';
    wrap.innerHTML = html;
}

async function doRestock(stockId, btn) {
    const row    = btn.closest('tr');
    const qtyEl  = row.querySelector('.restock-qty');
    const expiry = row.querySelector('.restock-expiry').value;
    const qty    = parseInt(qtyEl.value);

    if (!qty || qty < 1) {
        qtyEl.classList.add('is-invalid');
        qtyEl.focus();
        return;
    }
    qtyEl.classList.remove('is-invalid');

    const origHtml = btn.innerHTML;
    btn.disabled   = true;
    btn.innerHTML  = '<span class="spinner-border spinner-border-sm" style="width:.7rem;height:.7rem;"></span>';

    try {
        const body = { add_amount: qty };
        if (expiry) body.expiry_date = expiry;
        await apiFetch(ROUTES.stockRestock(stockId), { method: 'PATCH', body: JSON.stringify(body) });
        await loadRestock();
        await loadAlerts();
    } catch(e) {
        alert('Error: ' + e.message);
        btn.disabled  = false;
        btn.innerHTML = origHtml;
    }
}

// Restock tab shown — load the table
document.getElementById('tab-restock-btn').addEventListener('shown.bs.tab', function() {
    loadRestock();
});

// Restock live search (local filter, no extra API call)
document.getElementById('restock-search').addEventListener('input', function() {
    renderRestockTable();
});

// Restock tab — Add New Drug form
let restockNewDrugTimer = null;
const restockNewInput = document.getElementById('restock-new-drug-name');
const restockNewDd    = document.getElementById('restock-new-drug-dd');

restockNewInput.addEventListener('input', function() {
    clearTimeout(restockNewDrugTimer);
    const q = this.value.trim();
    // Duplicate check
    const isDup = q ? allStockData.some(s => s.drug_name.toLowerCase().trim() === q.toLowerCase().trim()) : false;
    if (isDup) {
        restockNewInput.classList.add('is-invalid');
        document.getElementById('restock-new-btn').disabled = true;
    } else {
        restockNewInput.classList.remove('is-invalid');
        document.getElementById('restock-new-btn').disabled = false;
    }
    if (q.length < 2) { restockNewDd.style.display = 'none'; return; }
    restockNewDrugTimer = setTimeout(async () => {
        try {
            const res  = await fetch(ROUTES.drugSearch + '?q=' + encodeURIComponent(q));
            const data = await res.json();
            if (!data.length) { restockNewDd.style.display = 'none'; return; }
            restockNewDd.innerHTML = data.slice(0, 12).map(item => {
                const name = item.name ?? item;
                return `<div class="dd-item" data-name="${esc(name)}"
                             onmousedown="event.preventDefault(); selectRestockNewDrug(this.dataset.name)">
                            ${esc(name)}
                        </div>`;
            }).join('');
            restockNewDd.style.display = 'block';
        } catch(e) { /* silent */ }
    }, 250);
});

restockNewInput.addEventListener('blur', () => {
    setTimeout(() => { restockNewDd.style.display = 'none'; }, 150);
});

function selectRestockNewDrug(name) {
    restockNewInput.value = name;
    restockNewDd.style.display = 'none';
    restockNewInput.focus();
    // Trigger duplicate check
    const isDup = allStockData.some(s => s.drug_name.toLowerCase().trim() === name.toLowerCase().trim());
    if (isDup) {
        restockNewInput.classList.add('is-invalid');
        document.getElementById('restock-new-btn').disabled = true;
    } else {
        restockNewInput.classList.remove('is-invalid');
        document.getElementById('restock-new-btn').disabled = false;
    }
}

document.getElementById('restock-new-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn        = document.getElementById('restock-new-btn');
    const feedbackEl = document.getElementById('restock-new-feedback');

    const fd   = new FormData(this);
    const body = {};
    fd.forEach((val, key) => { if (val !== '') body[key] = val; });

    if (!body.drug_name || !body.initial_amount) {
        feedbackEl.className = 'alert alert-danger py-1 px-2 small mb-2';
        feedbackEl.textContent = '✗ Drug name and quantity are required.';
        feedbackEl.style.display = 'block';
        return;
    }

    const nameNorm = body.drug_name.toLowerCase().trim();
    const duplicate = allStockData.find(s => s.drug_name.toLowerCase().trim() === nameNorm);
    if (duplicate) {
        feedbackEl.className = 'alert alert-warning py-1 px-2 small mb-2';
        feedbackEl.textContent = `"${duplicate.drug_name}" already exists (${duplicate.remaining} remaining). Use the Restock Existing Drugs table below to add quantity.`;
        feedbackEl.style.display = 'block';
        restockNewInput.classList.add('is-invalid');
        return;
    }

    btn.disabled  = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:.7rem;height:.7rem;"></span>';

    try {
        const res = await apiFetch(ROUTES.stockStore, { method: 'POST', body: JSON.stringify(body) });
        this.reset();
        restockNewInput.value = '';
        restockNewInput.classList.remove('is-invalid');
        feedbackEl.className = 'alert alert-success py-1 px-2 small mb-2';
        feedbackEl.textContent = `✓ "${res.stock?.drug_name ?? body.drug_name}" added to stock.`;
        feedbackEl.style.display = 'block';
        await loadRestock();
        await loadAlerts();
        setTimeout(() => { feedbackEl.style.display = 'none'; }, 4000);
    } catch(err) {
        feedbackEl.className = 'alert alert-danger py-1 px-2 small mb-2';
        feedbackEl.textContent = '✗ ' + (err.message || 'Failed to add drug.');
        feedbackEl.style.display = 'block';
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<i class="bi bi-plus-lg"></i>';
    }
});

// ──────────────────────────────────────────────────────────────────────────────
// Log tab
// ──────────────────────────────────────────────────────────────────────────────
function setLogToday() {
    const today = new Date().toISOString().slice(0, 10);
    document.getElementById('log-from').value = today;
    document.getElementById('log-to').value   = today;
}

async function loadLog() {
    const from = document.getElementById('log-from').value;
    const to   = document.getElementById('log-to').value;

    const url = ROUTES.log + '?from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to);

    ['log-consumption-wrap', 'log-restock-wrap', 'log-expired-wrap'].forEach(id => {
        document.getElementById(id).innerHTML =
            `<div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…</div>`;
    });

    try {
        const data = await apiFetch(url);
        renderLogConsumption(data.consumption);
        renderLogRestock(data.restock);
        renderLogExpired(data.expired);
    } catch(e) {
        ['log-consumption-wrap', 'log-restock-wrap', 'log-expired-wrap'].forEach(id => {
            document.getElementById(id).innerHTML =
                `<div class="alert alert-danger m-3 small py-2">Error: ${esc(e.message)}</div>`;
        });
    }
}

function renderLogConsumption(rows) {
    const wrap = document.getElementById('log-consumption-wrap');
    if (!rows.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-4 small"><i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No dispensing records for this period.</div>`;
        return;
    }
    let html = `
    <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
            <tr>
                <th>Date</th>
                <th>Drug</th>
                <th class="text-center">Qty Dispensed</th>
                <th class="text-center">Patients</th>
            </tr>
        </thead>
        <tbody>`;
    rows.forEach(r => {
        html += `<tr>
            <td class="small text-muted">${esc(r.date)}</td>
            <td class="fw-semibold small">${esc(r.drug_name)}</td>
            <td class="text-center"><span class="badge bg-danger bg-opacity-75">${r.total_qty}</span></td>
            <td class="text-center small text-muted">${r.patient_count}</td>
        </tr>`;
    });
    html += '</tbody></table>';
    wrap.innerHTML = html;
}

function renderLogRestock(rows) {
    const wrap = document.getElementById('log-restock-wrap');
    if (!rows.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-4 small"><i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No restock events for this period.</div>`;
        return;
    }
    let html = `
    <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
            <tr>
                <th>Date &amp; Time</th>
                <th>Drug</th>
                <th class="text-center">Action</th>
                <th class="text-center">Amount</th>
                <th>Expiry</th>
                <th>By</th>
            </tr>
        </thead>
        <tbody>`;
    rows.forEach(r => {
        const actionBadge = r.action === 'new_stock'
            ? `<span class="badge bg-primary bg-opacity-75">New Stock</span>`
            : `<span class="badge bg-success bg-opacity-75">Restock</span>`;
        html += `<tr>
            <td class="small text-muted">${esc(r.date)}</td>
            <td class="fw-semibold small">${esc(r.drug_name)}</td>
            <td class="text-center">${actionBadge}</td>
            <td class="text-center"><span class="badge bg-success">+${r.amount}</span></td>
            <td class="small">${r.expiry_date ? esc(r.expiry_date) : '<span class="text-muted">—</span>'}</td>
            <td class="small text-muted">${esc(r.performed_by)}</td>
        </tr>`;
    });
    html += '</tbody></table>';
    wrap.innerHTML = html;
}

function renderLogExpired(rows) {
    const wrap = document.getElementById('log-expired-wrap');
    if (!rows.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-4 small"><i class="bi bi-check-circle fs-2 d-block mb-2 opacity-25 text-success"></i>No expired items.</div>`;
        return;
    }
    let html = `
    <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
            <tr>
                <th>Drug</th>
                <th>Expiry Date</th>
                <th class="text-center">Remaining Qty</th>
            </tr>
        </thead>
        <tbody>`;
    rows.forEach(r => {
        html += `<tr>
            <td class="fw-semibold small">${esc(r.drug_name)}</td>
            <td><span class="text-danger small">${esc(r.expiry_date)}</span></td>
            <td class="text-center"><span class="badge bg-secondary">${r.remaining}</span></td>
        </tr>`;
    });
    html += '</tbody></table>';
    wrap.innerHTML = html;
}

// Log tab shown — initialise date range and load
document.getElementById('tab-log-btn').addEventListener('shown.bs.tab', function() {
    if (!document.getElementById('log-from').value) setLogToday();
    loadLog();
});

// ──────────────────────────────────────────────────────────────────────────────
// Init
// ──────────────────────────────────────────────────────────────────────────────
setLogToday();
loadQueue();
loadAlerts();

// Auto-refresh queue every 45 seconds
setInterval(() => {
    if (!document.hidden) loadQueue();
}, 45000);
</script>
@endpush
