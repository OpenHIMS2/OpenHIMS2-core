@extends('layouts.admin')
@section('title', 'Drugs Management')

@push('styles')
<style>
    .term-list .term-row { display:flex; align-items:center; justify-content:space-between; padding:.3rem .75rem; border-bottom:1px solid #f1f3f5; font-size:.85rem; }
    .term-list .term-row:last-child { border-bottom:none; }
    .term-list .term-row:hover { background:#f8f9fa; }
    .term-text { flex:1; word-break:break-word; }
    .empty-terms { color:#9ca3af; font-size:.8rem; padding:.6rem .75rem; font-style:italic; }
    .duplicate-warning { font-size:.72rem; color:#dc3545; margin-top:.15rem; display:none; }
    .term-pagination { display:none; align-items:center; justify-content:space-between; padding:.35rem .75rem; border-top:1px solid #e9ecef; background:#f8f9fa; font-size:.78rem; color:#6b7280; }
    .term-pagination .page-btns { display:flex; gap:.25rem; }
    .default-table td { vertical-align:middle; font-size:.875rem; }
    /* drug name autocomplete in defaults form */
    .drug-dd-wrap { position:relative; }
    .drug-dd { position:absolute; top:100%; left:0; right:0; z-index:50; background:#fff; border:1px solid #dee2e6; border-radius:.375rem; max-height:200px; overflow-y:auto; display:none; box-shadow:0 4px 12px rgba(0,0,0,.08); }
    .drug-dd .dd-item { padding:.45rem .75rem; cursor:pointer; font-size:.875rem; }
    .drug-dd .dd-item:hover { background:#f1f5f9; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-capsule-pill me-2 text-success"></i>Drugs Management
    </h4>
    <span class="text-muted small">{{ $drugs->count() }} drugs &bull; {{ $defaults->count() }} with defaults</span>
</div>

<ul class="nav nav-tabs mb-4" id="drugMgmtTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="drug-names-tab"
                data-bs-toggle="tab" data-bs-target="#drug-names-pane" type="button" role="tab">
            <i class="bi bi-list-ul me-1"></i>Drug Names
            <span class="badge bg-success ms-1">{{ $drugs->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="drug-defaults-tab"
                data-bs-toggle="tab" data-bs-target="#drug-defaults-pane" type="button" role="tab">
            <i class="bi bi-sliders me-1"></i>Default Management
            <span class="badge bg-secondary ms-1">{{ $defaults->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="drugMgmtTabContent">

    {{-- ── Tab 1: Drug Names ────────────────────────────────────────────────── --}}
    <div class="tab-pane fade show active" id="drug-names-pane" role="tabpanel">
        <div class="card border shadow-sm">

            {{-- Add form in header --}}
            <div class="card-header bg-f8 border-bottom py-2">
                <form action="{{ route('admin.drugs.store') }}" method="POST">
                    @csrf
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-semibold text-secondary" style="font-size:.8rem;white-space:nowrap;">
                            <i class="bi bi-capsule me-1 text-success"></i>Add New Drug
                        </span>
                        <div class="flex-grow-1" style="max-width:340px;">
                            <div class="input-group input-group-sm">
                                <input type="text" name="name" id="drug-name-input"
                                       class="form-control" placeholder="Drug name…"
                                       autocomplete="off" required maxlength="200">
                                <button type="submit" id="drug-name-submit" class="btn btn-success btn-sm px-3">
                                    <i class="bi bi-plus-lg"></i> Save
                                </button>
                            </div>
                            <div class="duplicate-warning" id="drug-dup-warning">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>
                                <span id="drug-dup-msg"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Drug list --}}
            <div class="term-list" id="drug-term-list">
                @forelse($drugs as $drug)
                <div class="term-row" data-name="{{ strtolower($drug->name) }}">
                    <span class="term-text">{{ $drug->name }}</span>
                    <form action="{{ route('admin.drugs.destroy', $drug) }}" method="POST" class="ms-2 flex-shrink-0">
                        @csrf @method('DELETE')
                        <button type="button"
                                class="btn btn-outline-danger btn-sm py-0 px-1"
                                data-name="{{ $drug->name }}"
                                onclick="confirmDialog({
                                    title:'Delete Drug',
                                    body:'Delete &quot;' + this.dataset.name + '&quot;? This will also remove its default.',
                                    confirmText:'Delete',
                                    confirmClass:'btn-danger',
                                    icon:'bi-trash3-fill text-danger'
                                }, () => this.closest('form').submit())">
                            <i class="bi bi-trash3" style="font-size:.75rem;"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="empty-terms" id="drug-list-empty">No drugs yet — add one above.</div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="term-pagination" id="drug-pagination">
                <span class="page-info" id="drug-page-info"></span>
                <div class="page-btns">
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2" id="drug-prev">
                        <i class="bi bi-chevron-left" style="font-size:.7rem;"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2" id="drug-next">
                        <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Tab 2: Default Management ────────────────────────────────────────── --}}
    <div class="tab-pane fade" id="drug-defaults-pane" role="tabpanel">

        {{-- Add/update default form --}}
        <div class="card border shadow-sm mb-4">
            <div class="card-header bg-f8 border-bottom py-2">
                <span class="fw-semibold text-secondary" style="font-size:.8rem;">
                    <i class="bi bi-sliders me-1 text-success"></i>Set Default for a Drug
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.drugs.defaults.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="drug_name_id" id="default-drug-id">
                    <div class="row g-2 align-items-end">
                        <div class="col-sm-3">
                            <label class="form-label form-label-sm mb-1">Drug Name</label>
                            <div class="drug-dd-wrap">
                                <input type="text" id="default-drug-name" class="form-control form-control-sm"
                                       placeholder="Type to search…" autocomplete="off" required>
                                <div class="drug-dd" id="default-drug-dd"></div>
                            </div>
                            <div class="form-text text-warning" id="default-drug-warn" style="display:none;font-size:.7rem;">
                                <i class="bi bi-pencil-square me-1"></i>Existing default will be updated.
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label form-label-sm mb-1">Route / Type</label>
                            <select name="type" id="default-type" class="form-select form-select-sm">
                                @foreach($types as $t)
                                    <option value="{{ $t }}" @selected($t === 'Oral')>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <label class="form-label form-label-sm mb-1">Dose</label>
                            <input type="text" name="dose" class="form-control form-control-sm"
                                   placeholder="e.g. 500" required maxlength="50">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label form-label-sm mb-1">Unit</label>
                            <select name="unit" class="form-select form-select-sm">
                                @foreach($units as $u)
                                    <option value="{{ $u }}">{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label form-label-sm mb-1">Frequency</label>
                            <select name="frequency" class="form-select form-select-sm">
                                @foreach($freqs as $f)
                                    <option value="{{ $f }}">{{ $f }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label form-label-sm mb-1">Duration</label>
                            <div class="dur-group input-group input-group-sm" id="admin-dur-group">
                                <input type="text" inputmode="numeric" class="form-control form-control-sm dur-qty" value="30" style="max-width:58px;">
                                <select class="form-select form-select-sm dur-unit">
                                    <option value="days">days</option>
                                    <option value="weeks">weeks</option>
                                    <option value="months">months</option>
                                </select>
                                <input type="hidden" name="duration" value="30 days">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" id="default-submit" class="btn btn-sm btn-success" disabled>
                                <i class="bi bi-check-lg me-1"></i>Save Default
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Existing defaults table --}}
        @if($defaults->isEmpty())
        <div class="text-center py-5 text-muted border rounded">
            <i class="bi bi-sliders" style="font-size:2.5rem;opacity:.15;"></i>
            <p class="mt-2 small mb-0">No defaults configured yet.</p>
        </div>
        @else
        <div class="card border shadow-sm">
            <div class="card-header bg-f8 py-2 fw-semibold" style="font-size:.8rem;">
                <i class="bi bi-table me-1 text-success"></i>Configured Defaults
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 default-table">
                    <thead class="table-light">
                        <tr>
                            <th>Drug</th>
                            <th>Route</th>
                            <th>Dose</th>
                            <th>Unit</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($defaults as $drugWithDefault)
                        <tr>
                            <td class="fw-semibold">{{ $drugWithDefault->name }}</td>
                            <td>{{ $drugWithDefault->default->type }}</td>
                            <td>{{ $drugWithDefault->default->dose }}</td>
                            <td>{{ $drugWithDefault->default->unit }}</td>
                            <td>{{ $drugWithDefault->default->frequency }}</td>
                            <td>{{ $drugWithDefault->default->duration ?? '—' }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.drugs.defaults.destroy', $drugWithDefault->default) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1"
                                            data-name="{{ $drugWithDefault->name }}"
                                            onclick="confirmDialog({
                                                title:'Remove Default',
                                                body:'Remove default for &quot;' + this.dataset.name + '&quot;?',
                                                confirmText:'Remove',
                                                confirmClass:'btn-danger',
                                                icon:'bi-trash3-fill text-danger'
                                            }, () => this.closest('form').submit())">
                                        <i class="bi bi-trash3" style="font-size:.75rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tab persistence ──────────────────────────────────────────────────────
    const STORAGE_KEY = 'drugMgmtActiveTab';
    const savedTab = localStorage.getItem(STORAGE_KEY);
    if (savedTab) {
        const tabEl = document.getElementById(savedTab);
        if (tabEl) new bootstrap.Tab(tabEl).show();
    }
    document.querySelectorAll('#drugMgmtTabs [data-bs-toggle="tab"]').forEach(function (btn) {
        btn.addEventListener('shown.bs.tab', function () {
            localStorage.setItem(STORAGE_KEY, this.id);
        });
    });

    // ── Tab 1: Pagination + duplicate check ─────────────────────────────────
    const PER_PAGE    = 12;
    const allRows     = Array.from(document.querySelectorAll('#drug-term-list .term-row'));
    const pagination  = document.getElementById('drug-pagination');
    const pageInfo    = document.getElementById('drug-page-info');
    const btnPrev     = document.getElementById('drug-prev');
    const btnNext     = document.getElementById('drug-next');
    const nameInput   = document.getElementById('drug-name-input');
    const submitBtn   = document.getElementById('drug-name-submit');
    const dupWarning  = document.getElementById('drug-dup-warning');
    const dupMsg      = document.getElementById('drug-dup-msg');

    let filtered = allRows.slice(), currentPage = 1;

    function totalPages() { return Math.max(1, Math.ceil(filtered.length / PER_PAGE)); }

    function render() {
        allRows.forEach(r => r.style.display = 'none');
        if (filtered.length === 0) { if (pagination) pagination.style.display = 'none'; return; }
        const start = (currentPage - 1) * PER_PAGE;
        filtered.slice(start, Math.min(start + PER_PAGE, filtered.length))
                .forEach(r => r.style.display = 'flex');
        if (totalPages() > 1) {
            pagination.style.display = 'flex';
            pageInfo.textContent = `Page ${currentPage} of ${totalPages()} (${filtered.length} drugs)`;
            btnPrev.disabled = currentPage === 1;
            btnNext.disabled = currentPage === totalPages();
        } else {
            pagination.style.display = 'none';
        }
    }

    if (btnPrev) btnPrev.addEventListener('click', () => { if (currentPage > 1) { currentPage--; render(); } });
    if (btnNext) btnNext.addEventListener('click', () => { if (currentPage < totalPages()) { currentPage++; render(); } });

    if (nameInput) {
        nameInput.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            filtered = q ? allRows.filter(r => r.dataset.name.includes(q)) : allRows.slice();
            currentPage = 1;
            render();

            const exact = allRows.some(r => r.dataset.name === q);
            if (q && exact) {
                dupMsg.textContent = '"' + this.value.trim() + '" already exists.';
                dupWarning.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                dupWarning.style.display = 'none';
                submitBtn.disabled = false;
            }
        });
    }

    render();

    // ── Tab 2: Drug name autocomplete for defaults form ──────────────────────
    const searchUrl    = "{{ route('drugs.search') }}";
    const defaultInput = document.getElementById('default-drug-name');
    const defaultDd    = document.getElementById('default-drug-dd');
    const defaultIdEl  = document.getElementById('default-drug-id');
    const defaultWarn  = document.getElementById('default-drug-warn');
    const defaultSave  = document.getElementById('default-submit');

    // drug IDs that already have a default (for "will be updated" warning)
    const drugsWithDefaults = new Set(@json($defaults->pluck('id')->toArray()));
    // name → id map for all drugs
    const drugMap = @json($drugs->pluck('id', 'name'));

    function applyDrugSelection(name) {
        const id = drugMap[name];
        if (id) {
            defaultIdEl.value    = id;
            defaultSave.disabled = false;
            defaultWarn.style.display = drugsWithDefaults.has(id) ? 'block' : 'none';
        } else {
            defaultIdEl.value    = '';
            defaultSave.disabled = true;
            defaultWarn.style.display = 'none';
        }
    }

    let ddTimer;
    if (defaultInput) {
        defaultInput.addEventListener('input', function () {
            clearTimeout(ddTimer);
            const q = this.value.trim();
            if (q.length < 1) {
                defaultIdEl.value = '';
                defaultSave.disabled = true;
                defaultWarn.style.display = 'none';
                defaultDd.style.display = 'none';
                defaultDd.innerHTML = '';
                return;
            }
            // Enable immediately if exact match exists
            applyDrugSelection(q);
            ddTimer = setTimeout(async () => {
                const items = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json()).catch(() => []);
                if (!items.length) { defaultDd.style.display = 'none'; return; }
                defaultDd.innerHTML = items.map(n =>
                    `<div class="dd-item" data-name="${escHtml(n)}">${escHtml(n)}</div>`
                ).join('');
                defaultDd.style.display = 'block';
            }, 200);
        });

        defaultDd.addEventListener('mousedown', function (e) {
            const item = e.target.closest('.dd-item');
            if (!item) return;
            e.preventDefault();
            const name = item.dataset.name;
            defaultInput.value = name;
            defaultDd.style.display = 'none';
            applyDrugSelection(name);
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#default-drug-name') && !e.target.closest('#default-drug-dd')) {
                defaultDd.style.display = 'none';
            }
        });
    }

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Duration group wiring ────────────────────────────────────────────────
    const adminDurGroup = document.getElementById('admin-dur-group');
    if (adminDurGroup) {
        const qty    = adminDurGroup.querySelector('.dur-qty');
        const unit   = adminDurGroup.querySelector('.dur-unit');
        const hidden = adminDurGroup.querySelector('input[name="duration"]');
        const syncAdminDur = () => { hidden.value = qty.value ? qty.value + ' ' + unit.value : ''; };
        qty.addEventListener('input',   syncAdminDur);
        unit.addEventListener('change', syncAdminDur);
    }
});
</script>
@endpush
