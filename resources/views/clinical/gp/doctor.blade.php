@extends('layouts.clinical')
@section('title', $pageTitle ?? 'GP — Doctor / MO View')

@push('styles')
<style>
/* ── Stat cards ──────────────────────────────────────────────────────────── */
.stat-card { border-radius: .6rem; border: 1px solid #e2e8f0; background: #fff; }
.stat-card .stat-icon { width: 3rem; height: 3rem; border-radius: .5rem;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-card .stat-value { font-size: 1.9rem; font-weight: 800; line-height: 1; }
.stat-card .stat-label { font-size: .75rem; color: #64748b; margin-top: .15rem; }

/* ── Health breakdown bars ───────────────────────────────────────────────── */
.health-row { display: flex; align-items: center; gap: .75rem; margin-bottom: .6rem; }
.health-row .health-label { width: 80px; font-size: .8rem; font-weight: 600; }
.health-row .progress { flex: 1; height: 10px; border-radius: 99px; }
.health-row .health-count { width: 36px; text-align: right; font-size: .8rem; font-weight: 700; }

/* ── Drug rank list ──────────────────────────────────────────────────────── */
.drug-rank { display: flex; align-items: center; gap: .6rem; padding: .4rem 0;
    border-bottom: 1px solid #f1f5f9; }
.drug-rank:last-child { border-bottom: none; }
.drug-rank .rank-num { width: 1.5rem; height: 1.5rem; border-radius: 50%;
    background: #f1f5f9; color: #64748b; font-size: .7rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.drug-rank .rank-num.top3 { background: #fbbf24; color: #fff; }
.drug-rank .rank-bar { flex: 1; height: 8px; background: #f1f5f9;
    border-radius: 99px; overflow: hidden; }
.drug-rank .rank-bar-fill { height: 100%; background: #6366f1; border-radius: 99px; }
.drug-rank .rank-qty { font-size: .8rem; font-weight: 700; color: #334155;
    width: 40px; text-align: right; flex-shrink: 0; }

/* ── Stock status badges (reused from pharmacist) ────────────────────────── */
.status-ok         { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
.status-low        { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
.status-depleted,
.status-out_of_stock { background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; }
.status-expired    { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; text-decoration:line-through; }
.stock-ok-badge    { font-size: .7rem; padding: .2rem .5rem; }

/* ── Read-only notice banner ─────────────────────────────────────────────── */
.ro-banner { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: .5rem;
    padding: .5rem 1rem; font-size: .82rem; color: #1e40af; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  Page header                                                            --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 bg-primary bg-opacity-10 p-3">
            <i class="bi bi-person-badge-fill text-primary fs-3"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0">GP — Doctor / MO View</h4>
            <p class="text-muted mb-0 small">
                <i class="bi bi-building me-1"></i>{{ $unit->name }}
                &bull;
                <i class="bi bi-geo-alt me-1"></i>{{ $unit->institution->name }}
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small" id="last-updated"></span>
        <button class="btn btn-sm btn-outline-secondary" onclick="refreshAll()">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  Summary stat cards                                                     --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4" id="stat-cards">
    {{-- Total Drugs --}}
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-capsule-pill text-primary"></i>
                </div>
                <div>
                    <div class="stat-value text-primary" id="s-total-drugs">—</div>
                    <div class="stat-label">Total Drugs</div>
                </div>
            </div>
            <div class="text-muted" style="font-size:.72rem;">
                <span id="s-total-remaining">—</span> units remaining
            </div>
        </div>
    </div>
    {{-- Today Dispensed --}}
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-check-circle-fill text-success"></i>
                </div>
                <div>
                    <div class="stat-value text-success" id="s-dispensed">—</div>
                    <div class="stat-label">Dispensed Today</div>
                </div>
            </div>
            <div class="text-muted" style="font-size:.72rem;">visits completed</div>
        </div>
    </div>
    {{-- Pending Queue --}}
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-hourglass-split text-warning"></i>
                </div>
                <div>
                    <div class="stat-value text-warning" id="s-pending">—</div>
                    <div class="stat-label">Queue Pending</div>
                </div>
            </div>
            <div class="text-muted" style="font-size:.72rem;">awaiting dispensing</div>
        </div>
    </div>
    {{-- Low / OOS --}}
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="stat-icon bg-danger bg-opacity-10">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                </div>
                <div>
                    <div class="stat-value text-danger" id="s-alerts">—</div>
                    <div class="stat-label">Low / OOS</div>
                </div>
            </div>
            <div class="text-muted" style="font-size:.72rem;">
                <span id="s-low">—</span> low &bull; <span id="s-oos">—</span> out of stock
            </div>
        </div>
    </div>
    {{-- Expiring --}}
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-calendar-x text-warning"></i>
                </div>
                <div>
                    <div class="stat-value text-warning" id="s-expiring">—</div>
                    <div class="stat-label">Expiring (30d)</div>
                </div>
            </div>
            <div class="text-muted" style="font-size:.72rem;">
                <span id="s-expired">—</span> already expired
            </div>
        </div>
    </div>
    {{-- OK --}}
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-shield-check text-success"></i>
                </div>
                <div>
                    <div class="stat-value text-success" id="s-ok">—</div>
                    <div class="stat-label">Healthy Stock</div>
                </div>
            </div>
            <div class="text-muted" style="font-size:.72rem;">items fully in stock</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  Main nav tabs                                                          --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<ul class="nav nav-tabs" id="mainTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="tab-overview-btn"
                data-bs-toggle="tab" data-bs-target="#tab-overview" type="button">
            <i class="bi bi-grid-1x2-fill me-1 text-primary"></i>Overview
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-stock-btn"
                data-bs-toggle="tab" data-bs-target="#tab-stock" type="button">
            <i class="bi bi-boxes me-1 text-success"></i>Stock
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-disp-btn"
                data-bs-toggle="tab" data-bs-target="#tab-disp" type="button">
            <i class="bi bi-receipt me-1 text-info"></i>Dispensing
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-log-btn"
                data-bs-toggle="tab" data-bs-target="#tab-log" type="button">
            <i class="bi bi-journal-text me-1 text-secondary"></i>Log
        </button>
    </li>
</ul>

<div class="tab-content border border-top-0 rounded-bottom shadow-sm bg-white p-3">

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  TAB 1 — Overview                                                       --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade show active" id="tab-overview" role="tabpanel">

    {{-- Read-only notice --}}
    <div class="ro-banner mb-4">
        <i class="bi bi-eye-fill me-2"></i>
        <strong>Observer mode</strong> — You have read-only access to this pharmacy.
        Stock management and drug dispensing are handled by the assigned pharmacist.
    </div>

    <div class="row g-4">

        {{-- ── Stock health breakdown ──────────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom fw-semibold small">
                    <i class="bi bi-bar-chart-fill text-primary me-2"></i>Stock Health Breakdown
                </div>
                <div class="card-body" id="health-breakdown">
                    <div class="text-center text-muted py-4 small">
                        <div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Top consumed drugs (last 7 days) ───────────────────────── --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom fw-semibold small d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-trophy-fill text-warning me-2"></i>Top Consumed Drugs</span>
                    <span class="text-muted fw-normal" style="font-size:.72rem;">last 7 days</span>
                </div>
                <div class="card-body" id="top-drugs">
                    <div class="text-center text-muted py-4 small">
                        <div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…
                    </div>
                </div>
            </div>
        </div>

    </div>
</div><!-- /tab-overview -->

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  TAB 2 — Stock (read-only)                                              --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade" id="tab-stock" role="tabpanel">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex gap-1 flex-wrap" id="stock-filter-tabs">
            <button class="btn btn-sm btn-primary" data-filter="all">All Stock</button>
            <button class="btn btn-sm btn-outline-warning" data-filter="expiring">
                <i class="bi bi-calendar-x me-1"></i>Expiring Soon
            </button>
            <button class="btn btn-sm btn-outline-danger" data-filter="low">
                <i class="bi bi-exclamation-triangle me-1"></i>Near OOS
            </button>
            <button class="btn btn-sm btn-outline-danger" data-filter="oos">
                <i class="bi bi-x-circle me-1"></i>Out of Stock
            </button>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <input type="search" id="stock-search" class="form-control form-control-sm"
                   placeholder="Filter by name…" style="width:200px;">
            <button class="btn btn-sm btn-outline-secondary" onclick="loadStock(currentStockFilter)">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    <div id="stock-table-wrap" style="max-height: calc(100vh - 380px); overflow-y:auto;">
        <div class="text-center text-muted py-4 small">
            <div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…
        </div>
    </div>

</div><!-- /tab-stock -->

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  TAB 3 — Dispensing records                                             --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade" id="tab-disp" role="tabpanel">

    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
        <label class="form-label small mb-0 fw-semibold text-muted">Date range:</label>
        <input type="date" id="disp-from" class="form-control form-control-sm" style="width:150px;">
        <span class="text-muted small">to</span>
        <input type="date" id="disp-to"   class="form-control form-control-sm" style="width:150px;">
        <button class="btn btn-sm btn-outline-secondary" onclick="loadDispensing()">
            <i class="bi bi-search me-1"></i>Filter
        </button>
        <button class="btn btn-sm btn-link text-muted p-0 ms-1" onclick="setToday('disp-from','disp-to'); loadDispensing();">
            Today
        </button>
        <span class="ms-auto text-muted small" id="disp-count"></span>
    </div>

    <div id="disp-table-wrap" style="max-height: calc(100vh - 360px); overflow-y:auto;">
        <div class="text-center text-muted py-4 small">
            <div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…
        </div>
    </div>

</div><!-- /tab-disp -->

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{--  TAB 4 — Log                                                            --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-pane fade" id="tab-log" role="tabpanel">

    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <label class="form-label small mb-0 fw-semibold text-muted">Date range:</label>
        <input type="date" id="log-from" class="form-control form-control-sm" style="width:150px;">
        <span class="text-muted small">to</span>
        <input type="date" id="log-to"   class="form-control form-control-sm" style="width:150px;">
        <button class="btn btn-sm btn-outline-secondary" onclick="loadLog()">
            <i class="bi bi-search me-1"></i>Filter
        </button>
        <button class="btn btn-sm btn-link text-muted p-0 ms-1" onclick="setToday('log-from','log-to'); loadLog();">
            Today
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom fw-semibold small">
            <i class="bi bi-capsule-pill text-danger me-2"></i>Drug Consumption
        </div>
        <div class="card-body p-0">
            <div id="log-consumption-wrap" style="max-height:250px;overflow-y:auto;">
                <div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom fw-semibold small">
            <i class="bi bi-box-arrow-in-down text-success me-2"></i>Restock History
        </div>
        <div class="card-body p-0">
            <div id="log-restock-wrap" style="max-height:250px;overflow-y:auto;">
                <div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom fw-semibold small">
            <i class="bi bi-calendar-x text-muted me-2"></i>Expired Stock
        </div>
        <div class="card-body p-0">
            <div id="log-expired-wrap" style="max-height:200px;overflow-y:auto;">
                <div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…</div>
            </div>
        </div>
    </div>

</div><!-- /tab-log -->

</div><!-- /tab-content -->

@endsection

@push('scripts')
<script>
// ── Route constants ──────────────────────────────────────────────────────────
const ROUTES = {
    summary:    '{{ route("clinical.gp-doctor.summary",    $unitView->id) }}',
    stock:      '{{ route("clinical.gp-doctor.stock",      $unitView->id) }}',
    dispensing: '{{ route("clinical.gp-doctor.dispensing", $unitView->id) }}',
    log:        '{{ route("clinical.gp-doctor.log",        $unitView->id) }}',
};
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── State ────────────────────────────────────────────────────────────────────
let currentStockFilter = 'all';

// ── Helpers ──────────────────────────────────────────────────────────────────
async function apiFetch(url) {
    const res = await fetch(url, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    if (!res.ok) {
        let msg = `HTTP ${res.status}`;
        try { msg = (await res.json()).message ?? msg; } catch (_) {}
        throw new Error(msg);
    }
    return res.json();
}

function esc(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function setToday(fromId, toId) {
    const today = new Date().toISOString().slice(0, 10);
    document.getElementById(fromId).value = today;
    document.getElementById(toId).value   = today;
}

function setLastUpdated() {
    const t = new Date();
    document.getElementById('last-updated').textContent =
        'Updated ' + t.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

// ── Summary / Overview ────────────────────────────────────────────────────────
async function loadSummary() {
    try {
        const d = await apiFetch(ROUTES.summary);

        document.getElementById('s-total-drugs').textContent    = d.total_drugs;
        document.getElementById('s-total-remaining').textContent= d.total_remaining?.toLocaleString() ?? '—';
        document.getElementById('s-dispensed').textContent      = d.today_dispensed;
        document.getElementById('s-pending').textContent        = d.queue_pending;
        document.getElementById('s-alerts').textContent         = d.low_count + d.oos_count;
        document.getElementById('s-low').textContent            = d.low_count;
        document.getElementById('s-oos').textContent            = d.oos_count;
        document.getElementById('s-expiring').textContent       = d.expiring_count;
        document.getElementById('s-expired').textContent        = d.expired_count;
        document.getElementById('s-ok').textContent             = d.ok_count;

        renderHealthBreakdown(d);
        renderTopDrugs(d.top_drugs);
        setLastUpdated();
    } catch(e) {
        document.getElementById('health-breakdown').innerHTML =
            `<div class="alert alert-danger small py-2">Error: ${esc(e.message)}</div>`;
    }
}

function renderHealthBreakdown(d) {
    const total = d.total_drugs || 1;
    const rows = [
        { label: 'Healthy',  count: d.ok_count,       color: 'bg-success', pct: d.ok_count },
        { label: 'Low',      count: d.low_count,       color: 'bg-warning', pct: d.low_count },
        { label: 'OOS',      count: d.oos_count,       color: 'bg-danger',  pct: d.oos_count },
        { label: 'Expiring', count: d.expiring_count,  color: 'bg-warning', pct: d.expiring_count },
        { label: 'Expired',  count: d.expired_count,   color: 'bg-secondary', pct: d.expired_count },
    ];

    let html = '';
    rows.forEach(r => {
        const pct = Math.max(3, Math.round((r.pct / total) * 100));
        html += `
        <div class="health-row">
            <div class="health-label">${esc(r.label)}</div>
            <div class="progress bg-light flex-grow-1">
                <div class="progress-bar ${r.color}" style="width:${pct}%"></div>
            </div>
            <div class="health-count">${r.count}</div>
        </div>`;
    });

    // Summary pill badges
    html += `<hr class="my-3">
    <div class="d-flex flex-wrap gap-2">
        <span class="badge bg-primary rounded-pill">${d.total_drugs} drugs total</span>
        <span class="badge bg-success rounded-pill">${(d.total_remaining ?? 0).toLocaleString()} units remaining</span>
    </div>`;

    document.getElementById('health-breakdown').innerHTML = html;
}

function renderTopDrugs(drugs) {
    const el = document.getElementById('top-drugs');
    if (!drugs || !drugs.length) {
        el.innerHTML = `<div class="text-center text-muted py-4 small">
            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No dispensing data for the last 7 days.</div>`;
        return;
    }

    const maxQty = drugs[0].total_qty;
    let html = '';
    drugs.forEach((d, i) => {
        const pct = Math.round((d.total_qty / maxQty) * 100);
        html += `
        <div class="drug-rank">
            <div class="rank-num ${i < 3 ? 'top3' : ''}">${i + 1}</div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="fw-semibold small text-truncate">${esc(d.drug_name)}</div>
                <div class="rank-bar mt-1">
                    <div class="rank-bar-fill" style="width:${pct}%"></div>
                </div>
            </div>
            <div class="rank-qty">${d.total_qty}</div>
        </div>`;
    });

    el.innerHTML = html;
}

// ── Stock tab ─────────────────────────────────────────────────────────────────
async function loadStock(filter = 'all') {
    currentStockFilter = filter;

    document.querySelectorAll('#stock-filter-tabs button').forEach(btn => {
        const f    = btn.dataset.filter;
        const base = f === 'all' ? 'primary' : f === 'expiring' ? 'warning' : 'danger';
        btn.className = (f === filter)
            ? `btn btn-sm btn-${base}`
            : `btn btn-sm btn-outline-${base}`;
    });

    const wrap = document.getElementById('stock-table-wrap');
    wrap.innerHTML = `<div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…</div>`;

    try {
        const data = await apiFetch(ROUTES.stock + '?filter=' + filter);
        renderStockTable(data.stocks);
    } catch(e) {
        wrap.innerHTML = `<div class="alert alert-danger m-3 small py-2">Error: ${esc(e.message)}</div>`;
    }
}

function renderStockTable(stocks) {
    window._lastStockData = stocks;
    const wrap = document.getElementById('stock-table-wrap');
    const term = document.getElementById('stock-search').value.trim().toLowerCase();
    const rows = term ? stocks.filter(s => s.drug_name.toLowerCase().includes(term)) : stocks;

    if (!rows.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-5 small">
            <i class="bi bi-box-seam fs-2 d-block mb-2 opacity-25"></i>No items found.</div>`;
        return;
    }

    const statusLabel = {
        ok:           ['✓ OK',      'status-ok'],
        low:          ['⚠ Low',      'status-low'],
        depleted:     ['✗ Depleted', 'status-depleted'],
        out_of_stock: ['✗ OOS',      'status-out_of_stock'],
        expired:      ['✗ Expired',  'status-expired'],
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
            </tr>
        </thead>
        <tbody>`;

    rows.forEach(s => {
        const [sLabel, sCls] = statusLabel[s.stock_status] ?? ['—', ''];
        const pctUsed  = s.initial_amount > 0 ? Math.round((1 - s.remaining / s.initial_amount) * 100) : 0;
        const pctColor = s.remaining <= 0 ? 'bg-danger'
                       : s.stock_status === 'low' ? 'bg-warning' : 'bg-success';

        const expiryText = s.expiry_display
            ? (s.days_until_expiry !== null && s.days_until_expiry < 0
                ? `<span class="text-danger fw-semibold small">${esc(s.expiry_display)} <span class="badge bg-danger">Exp.</span></span>`
                : s.days_until_expiry !== null && s.days_until_expiry <= 30
                    ? `<span class="text-warning fw-semibold small">${esc(s.expiry_display)} <span class="badge bg-warning text-dark">${s.days_until_expiry}d</span></span>`
                    : `<span class="small">${esc(s.expiry_display)}</span>`)
            : '<span class="text-muted small">—</span>';

        html += `
        <tr>
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
        </tr>`;
    });

    html += '</tbody></table>';
    wrap.innerHTML = html;
}

document.querySelectorAll('#stock-filter-tabs button').forEach(btn => {
    btn.addEventListener('click', function() { loadStock(this.dataset.filter); });
});

document.getElementById('stock-search').addEventListener('input', function() {
    renderStockTable(window._lastStockData ?? []);
});

// ── Dispensing tab ────────────────────────────────────────────────────────────
async function loadDispensing() {
    const from = document.getElementById('disp-from').value;
    const to   = document.getElementById('disp-to').value;

    const wrap = document.getElementById('disp-table-wrap');
    wrap.innerHTML = `<div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…</div>`;
    document.getElementById('disp-count').textContent = '';

    try {
        const data = await apiFetch(`${ROUTES.dispensing}?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`);
        renderDispensingTable(data.records);
    } catch(e) {
        wrap.innerHTML = `<div class="alert alert-danger m-3 small py-2">Error: ${esc(e.message)}</div>`;
    }
}

const catLabel = {
    opd:                    'OPD',
    new_clinic_visit:       'New',
    recurrent_clinic_visit: 'Rec.',
    urgent:                 'Urgent',
};
const catBadge = {
    opd:                    'bg-primary',
    new_clinic_visit:       'bg-success',
    recurrent_clinic_visit: 'bg-info text-dark',
    urgent:                 'bg-danger',
};

function renderDispensingTable(records) {
    const wrap = document.getElementById('disp-table-wrap');

    if (!records.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-5 small">
            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No dispensing records for this period.</div>`;
        document.getElementById('disp-count').textContent = '0 records';
        return;
    }

    document.getElementById('disp-count').textContent = records.length + (records.length === 500 ? '+ records (limit)' : ' records');

    let html = `
    <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
            <tr>
                <th>Date &amp; Time</th>
                <th class="text-center">Visit#</th>
                <th>Patient</th>
                <th>Drug</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Status</th>
                <th>Pharmacist</th>
            </tr>
        </thead>
        <tbody>`;

    records.forEach(r => {
        const statusBadge = r.status === 'prescribed'
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:.65rem;">Dispensed</span>`
            : `<span class="badge bg-secondary-subtle text-secondary border" style="font-size:.65rem;">OS</span>`;
        const catBadgeHtml = catBadge[r.category]
            ? `<span class="badge ${catBadge[r.category]} ms-1" style="font-size:.6rem;">${catLabel[r.category] ?? r.category}</span>`
            : '';

        html += `<tr>
            <td class="text-muted small" style="white-space:nowrap;">${esc(r.dispensed_at)}</td>
            <td class="text-center">
                <span class="fw-semibold small">#${esc(r.visit_number)}</span>${catBadgeHtml}
            </td>
            <td class="fw-semibold small">${esc(r.patient_name)}</td>
            <td class="small">${esc(r.drug_name)}</td>
            <td class="text-center small fw-semibold">${r.status === 'prescribed' ? r.quantity_dispensed : '—'}</td>
            <td class="text-center">${statusBadge}</td>
            <td class="text-muted small">${esc(r.pharmacist)}</td>
        </tr>`;
    });

    html += '</tbody></table>';
    wrap.innerHTML = html;
}

// ── Log tab ───────────────────────────────────────────────────────────────────
async function loadLog() {
    const from = document.getElementById('log-from').value;
    const to   = document.getElementById('log-to').value;
    const url  = `${ROUTES.log}?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`;

    ['log-consumption-wrap','log-restock-wrap','log-expired-wrap'].forEach(id => {
        document.getElementById(id).innerHTML =
            `<div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm mb-1"></div><br>Loading…</div>`;
    });

    try {
        const data = await apiFetch(url);
        renderLogConsumption(data.consumption);
        renderLogRestock(data.restock);
        renderLogExpired(data.expired);
    } catch(e) {
        ['log-consumption-wrap','log-restock-wrap','log-expired-wrap'].forEach(id => {
            document.getElementById(id).innerHTML =
                `<div class="alert alert-danger m-3 small py-2">Error: ${esc(e.message)}</div>`;
        });
    }
}

function renderLogConsumption(rows) {
    const wrap = document.getElementById('log-consumption-wrap');
    if (!rows.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-4 small"><i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No data for this period.</div>`;
        return;
    }
    let html = `<table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
            <tr><th>Date</th><th>Drug</th><th class="text-center">Qty</th><th class="text-center">Patients</th></tr>
        </thead><tbody>`;
    rows.forEach(r => {
        html += `<tr>
            <td class="text-muted small">${esc(r.date)}</td>
            <td class="fw-semibold small">${esc(r.drug_name)}</td>
            <td class="text-center"><span class="badge bg-danger bg-opacity-75">${r.total_qty}</span></td>
            <td class="text-center small text-muted">${r.patient_count}</td>
        </tr>`;
    });
    wrap.innerHTML = html + '</tbody></table>';
}

function renderLogRestock(rows) {
    const wrap = document.getElementById('log-restock-wrap');
    if (!rows.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-4 small"><i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No restock events for this period.</div>`;
        return;
    }
    let html = `<table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
            <tr><th>Date &amp; Time</th><th>Drug</th><th class="text-center">Action</th><th class="text-center">Amount</th><th>Expiry</th><th>By</th></tr>
        </thead><tbody>`;
    rows.forEach(r => {
        const ab = r.action === 'new_stock'
            ? `<span class="badge bg-primary bg-opacity-75">New Stock</span>`
            : `<span class="badge bg-success bg-opacity-75">Restock</span>`;
        html += `<tr>
            <td class="small text-muted">${esc(r.date)}</td>
            <td class="fw-semibold small">${esc(r.drug_name)}</td>
            <td class="text-center">${ab}</td>
            <td class="text-center"><span class="badge bg-success">+${r.amount}</span></td>
            <td class="small">${r.expiry_date ? esc(r.expiry_date) : '<span class="text-muted">—</span>'}</td>
            <td class="small text-muted">${esc(r.performed_by)}</td>
        </tr>`;
    });
    wrap.innerHTML = html + '</tbody></table>';
}

function renderLogExpired(rows) {
    const wrap = document.getElementById('log-expired-wrap');
    if (!rows.length) {
        wrap.innerHTML = `<div class="text-center text-muted py-4 small"><i class="bi bi-check-circle fs-2 d-block mb-2 text-success opacity-50"></i>No expired items.</div>`;
        return;
    }
    let html = `<table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
            <tr><th>Drug</th><th>Expiry Date</th><th class="text-center">Remaining Qty</th></tr>
        </thead><tbody>`;
    rows.forEach(r => {
        html += `<tr>
            <td class="fw-semibold small">${esc(r.drug_name)}</td>
            <td><span class="text-danger small">${esc(r.expiry_date)}</span></td>
            <td class="text-center"><span class="badge bg-secondary">${r.remaining}</span></td>
        </tr>`;
    });
    wrap.innerHTML = html + '</tbody></table>';
}

// ── Tab events ────────────────────────────────────────────────────────────────
document.getElementById('tab-stock-btn').addEventListener('shown.bs.tab', () => loadStock(currentStockFilter));
document.getElementById('tab-disp-btn').addEventListener('shown.bs.tab',  () => loadDispensing());
document.getElementById('tab-log-btn').addEventListener('shown.bs.tab',   () => {
    if (!document.getElementById('log-from').value) setToday('log-from', 'log-to');
    loadLog();
});

function refreshAll() {
    loadSummary();
    if (document.getElementById('tab-stock').classList.contains('active')) loadStock(currentStockFilter);
    if (document.getElementById('tab-disp').classList.contains('active'))  loadDispensing();
}

// ── Init ──────────────────────────────────────────────────────────────────────
setToday('disp-from', 'disp-to');
setToday('log-from',  'log-to');
loadSummary();

// Auto-refresh summary every 60 seconds
setInterval(() => { if (!document.hidden) loadSummary(); }, 60000);
</script>
@endpush
