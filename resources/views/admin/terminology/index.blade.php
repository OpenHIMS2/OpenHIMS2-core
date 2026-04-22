@extends('layouts.admin')
@section('title', 'Terminology Management')

@push('styles')
<style>
/* ── Layout ─────────────────────────────────────── */
.term-card { display:flex; flex-direction:column; height:100%; }
.term-card .card-header { padding:.5rem .75rem; background:#f8f9fa; border-bottom:1px solid #dee2e6; }
.term-list .term-row { display:flex; align-items:center; justify-content:space-between; padding:.28rem .75rem; border-bottom:1px solid #f1f3f5; font-size:.84rem; }
.term-list .term-row:last-child { border-bottom:none; }
.term-list .term-row:hover { background:#f8f9fa; }
.term-list .term-text { flex:1; word-break:break-word; }
.empty-terms { color:#9ca3af; font-size:.8rem; padding:.6rem .75rem; font-style:italic; }
.filter-empty-msg { display:none; color:#6b7280; font-size:.8rem; padding:.6rem .75rem; font-style:italic; }
.term-pagination { display:none; align-items:center; justify-content:space-between; padding:.35rem .75rem; border-top:1px solid #e9ecef; background:#f8f9fa; font-size:.78rem; color:#6b7280; }
.term-pagination .page-btns { display:flex; gap:.25rem; }
.duplicate-warning { font-size:.72rem; color:#dc3545; margin-top:.15rem; display:none; }
.term-row.term-highlight { background:#fff8e1; }
/* ── Category slug badge ─────────────────────────── */
.slug-badge { font-family:'Consolas',monospace; font-size:.68rem; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; border-radius:.3rem; padding:1px 6px; cursor:pointer; transition:background .12s,color .12s; user-select:all; }
.slug-badge:hover { background:#e0e7ef; color:#1e3a5f; }
.slug-badge.copied { background:#dcfce7; color:#166534; border-color:#bbf7d0; }
/* ── Guide panel ─────────────────────────────────── */
.guide-panel { background:linear-gradient(135deg,#f0f9ff,#e0f2fe); border:1px solid #bae6fd; border-radius:.75rem; }
.guide-code { background:#0f172a; border-radius:.5rem; padding:.9rem 1.1rem; position:relative; }
.guide-code pre { margin:0; font-size:.78rem; line-height:1.8; font-family:'Consolas','Fira Code',monospace; white-space:pre; overflow-x:auto; color:#e2e8f0; }
.g-copy { position:absolute; top:.5rem; right:.55rem; font-size:.68rem; padding:.15rem .45rem; border-radius:.3rem; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.18); color:#94a3b8; cursor:pointer; transition:all .12s; }
.g-copy:hover { background:rgba(255,255,255,.2); color:#e2e8f0; }
.g-copy.copied { background:rgba(34,197,94,.2); color:#86efac; border-color:rgba(34,197,94,.3); }
.kw{color:#93c5fd}.str{color:#86efac}.var{color:#fde68a}.cmt{color:#475569}.attr{color:#fdba74}
</style>
@endpush

@section('content')

{{-- ── Page header ──────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-journal-medical me-2 text-primary"></i>Terminology Management
        </h4>
        <p class="text-muted small mt-1 mb-0">
            {{ $categories->count() }} boxes &middot;
            {{ $terms->flatten()->count() }} total terms
        </p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm"
                type="button" data-bs-toggle="collapse" data-bs-target="#guidePanel">
            <i class="bi bi-book me-1"></i>Implementation Guide
        </button>
        <button class="btn btn-primary btn-sm"
                data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-lg me-1"></i>Add Custom Box
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2 small">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show py-2 small">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show py-2 small">
    @foreach($errors->all() as $e)<div><i class="bi bi-dot"></i>{{ $e }}</div>@endforeach
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Implementation Guide ─────────────────────────────────────────────── --}}
<div class="collapse mb-4" id="guidePanel">
<div class="guide-panel p-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-plug-fill text-info fs-5"></i>
        <h6 class="fw-bold mb-0 text-primary">Implementation Guide — How to use terminology boxes in clinical pages</h6>
    </div>

    <div class="row g-4">

        {{-- Step 1 --}}
        <div class="col-lg-5">
            <div class="d-flex align-items-start gap-2 mb-2">
                <span class="badge bg-primary rounded-pill px-2">1</span>
                <h6 class="fw-semibold mb-0 small">Copy the category slug from any box header</h6>
            </div>
            <p class="text-muted small mb-0 ps-4">
                Each box has a <code>slug</code> badge in its header — click it to copy.
                The slug is what you put in <code>data-category</code>.
            </p>
        </div>

        {{-- Step 2 --}}
        <div class="col-lg-7">
            <div class="d-flex align-items-start gap-2 mb-2">
                <span class="badge bg-primary rounded-pill px-2">2</span>
                <h6 class="fw-semibold mb-0 small">Add this HTML to your blade file — no JS needed</h6>
            </div>
            <div class="guide-code" id="gc-basic">
                <button class="g-copy" onclick="copyGuide('gc-basic',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
                <pre><code><span class="cmt">&lt;!-- Simple: search all categories --&gt;</span>
&lt;input type=<span class="str">"text"</span>
       class=<span class="str">"form-control <span class="attr">terminology-search</span>"</span>
       name=<span class="str">"finding"</span>
       placeholder=<span class="str">"Start typing..."</span>&gt;

<span class="cmt">&lt;!-- With category filter --&gt;</span>
&lt;input type=<span class="str">"text"</span>
       class=<span class="str">"form-control <span class="attr">terminology-search</span>"</span>
       <span class="attr">data-category</span>=<span class="str">"presenting_complaints"</span>
       name=<span class="str">"complaint"</span>
       placeholder=<span class="str">"Type a complaint..."</span>&gt;</code></pre>
            </div>
        </div>

        {{-- Advanced: AJAX / multi-select --}}
        <div class="col-lg-6">
            <div class="d-flex align-items-start gap-2 mb-2">
                <span class="badge bg-secondary rounded-pill px-2">+</span>
                <h6 class="fw-semibold mb-0 small">AJAX call — fetch terms manually from JavaScript</h6>
            </div>
            <div class="guide-code" id="gc-ajax">
                <button class="g-copy" onclick="copyGuide('gc-ajax',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
                <pre><code>fetch(<span class="str">`/terminology/search?category=presenting_complaints&q=${query}`</span>)
    .then(r =&gt; r.json())
    .then(terms =&gt; {
        <span class="cmt">// terms = ["Headache", "Chest pain", ...]</span>
        terms.forEach(t =&gt; console.log(t));
    });</code></pre>
            </div>
        </div>

        {{-- Blade form with label --}}
        <div class="col-lg-6">
            <div class="d-flex align-items-start gap-2 mb-2">
                <span class="badge bg-secondary rounded-pill px-2">+</span>
                <h6 class="fw-semibold mb-0 small">Full form group with label</h6>
            </div>
            <div class="guide-code" id="gc-group">
                <button class="g-copy" onclick="copyGuide('gc-group',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
                <pre><code>&lt;div class=<span class="str">"mb-3"</span>&gt;
    &lt;label class=<span class="str">"form-label fw-medium"</span>&gt;
        Presenting Complaint
    &lt;/label&gt;
    &lt;input type=<span class="str">"text"</span>
           class=<span class="str">"form-control terminology-search"</span>
           data-category=<span class="str">"presenting_complaints"</span>
           name=<span class="str">"complaint"</span>
           placeholder=<span class="str">"Start typing..."</span>&gt;
&lt;/div&gt;</code></pre>
            </div>
        </div>

        {{-- API reference --}}
        <div class="col-12">
            <div class="d-flex align-items-start gap-2 mb-2">
                <span class="badge bg-info text-dark rounded-pill px-2">API</span>
                <h6 class="fw-semibold mb-0 small">Search endpoint reference</h6>
            </div>
            <div class="row g-2 small">
                <div class="col-md-6">
                    <div class="rounded p-2" style="background:#f8fafc;border:1px solid #e2e8f0">
                        <div class="text-muted mb-1">Request</div>
                        <code>GET /terminology/search?category={slug}&amp;q={query}</code><br>
                        <span class="text-muted">Auth required &middot; <code>q</code> is optional</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="rounded p-2" style="background:#f8fafc;border:1px solid #e2e8f0">
                        <div class="text-muted mb-1">Response</div>
                        <code>["Headache", "Chest pain", "Cough", ...]</code><br>
                        <span class="text-muted">JSON array of matching term strings</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- All category slugs --}}
        <div class="col-12">
            <div class="d-flex align-items-start gap-2 mb-2">
                <span class="badge bg-success rounded-pill px-2"><i class="bi bi-list-ul"></i></span>
                <h6 class="fw-semibold mb-0 small">All available category slugs — click to copy</h6>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($categories as $cat)
                <div class="d-flex align-items-center gap-1 rounded px-2 py-1"
                     style="background:#f8fafc;border:1px solid #e2e8f0">
                    @if(!$cat->is_system)
                    <span class="badge bg-warning text-dark" style="font-size:.6rem">Custom</span>
                    @endif
                    <span class="text-muted" style="font-size:.75rem">{{ $cat->name }}:</span>
                    <span class="slug-badge" onclick="copySlug(this)" title="Click to copy slug">{{ $cat->slug }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
</div>

{{-- ── Category grid ─────────────────────────────────────────────────────── --}}
@php
    $systemCats = $categories->where('is_system', true);
    $customCats = $categories->where('is_system', false);
@endphp

{{-- System categories --}}
@if($systemCats->isNotEmpty())
<div class="d-flex align-items-center gap-2 mb-2">
    <span class="badge bg-secondary"><i class="bi bi-shield-lock me-1"></i>System Boxes</span>
    <span class="text-muted small">{{ $systemCats->count() }} built-in terminology boxes</span>
</div>
<div class="row row-cols-1 row-cols-lg-2 g-3 mb-4">
    @foreach($systemCats as $cat)
    <div class="col">
        @include('admin.terminology._category_card', ['cat' => $cat, 'terms' => $terms])
    </div>
    @endforeach
</div>
@endif

{{-- Custom categories --}}
@if($customCats->isNotEmpty())
<div class="d-flex align-items-center gap-2 mb-2">
    <span class="badge bg-warning text-dark"><i class="bi bi-stars me-1"></i>Custom Boxes</span>
    <span class="text-muted small">{{ $customCats->count() }} custom terminology boxes</span>
</div>
<div class="row row-cols-1 row-cols-lg-2 g-3 mb-4">
    @foreach($customCats as $cat)
    <div class="col">
        @include('admin.terminology._category_card', ['cat' => $cat, 'terms' => $terms])
    </div>
    @endforeach
</div>
@endif

@if($customCats->isEmpty())
<div class="alert border-0 py-3 small mb-0" style="background:#fefce8;border:1px solid #fef08a!important">
    <i class="bi bi-info-circle me-2 text-warning"></i>
    No custom terminology boxes yet. Click <strong>Add Custom Box</strong> to create one, then use its slug in your clinical pages.
</div>
@endif

{{-- ── Add Custom Category Modal ─────────────────────────────────────────── --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('admin.terminology.categories.store') }}">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2 text-primary"></i>Add Custom Terminology Box
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Box Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="catName"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Orthopaedic Findings"
                               maxlength="100" required value="{{ old('name') }}">
                        <div class="form-text">Displayed as the box title in admin.</div>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Slug <span class="text-danger">*</span>
                            <span class="badge bg-light text-muted border ms-1" style="font-size:.68rem">auto-generated</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light font-monospace small text-muted">data-category=</span>
                            <input type="text" name="slug" id="catSlug"
                                   class="form-control font-monospace @error('slug') is-invalid @enderror"
                                   placeholder="orthopaedic_findings"
                                   maxlength="50" pattern="[a-z][a-z0-9_]*" required
                                   value="{{ old('slug') }}">
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-text">
                            Lowercase letters, numbers, underscores. Used as <code>data-category</code> in HTML.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description <span class="text-muted fw-normal small">(optional)</span></label>
                        <input type="text" name="description"
                               class="form-control form-control-sm"
                               placeholder="What clinical information this box stores…"
                               maxlength="255" value="{{ old('description') }}">
                    </div>

                    <div class="alert border-0 py-2 small" style="background:#f0fdf4;border:1px solid #bbf7d0!important">
                        <i class="bi bi-lightbulb-fill me-2 text-success"></i>
                        After creating, use the slug in your clinical blade file:<br>
                        <code class="small">class="terminology-search" data-category="<span id="slugPreview">your_slug</span>"</code>
                    </div>

                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Create Box
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Slug auto-generation ──────────────────────────────────────────────────
const catName    = document.getElementById('catName');
const catSlug    = document.getElementById('catSlug');
const slugPreview = document.getElementById('slugPreview');
let slugManual   = false;

function toSlug(str) {
    return str.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
}

catName.addEventListener('input', function () {
    if (!slugManual) {
        const s = toSlug(this.value);
        catSlug.value = s;
        slugPreview.textContent = s || 'your_slug';
    }
});
catSlug.addEventListener('input', function () {
    slugManual = true;
    slugPreview.textContent = this.value || 'your_slug';
});

// Reset on modal open
document.getElementById('addCategoryModal').addEventListener('show.bs.modal', function () {
    slugManual = false;
});

// ── Re-open modal on validation error ────────────────────────────────────
@if($errors->any() && old('name'))
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('addCategoryModal')).show();
});
@endif

// ── Copy slug badge ───────────────────────────────────────────────────────
function copySlug(el) {
    navigator.clipboard.writeText(el.textContent.trim()).then(() => {
        el.classList.add('copied');
        const orig = el.textContent;
        el.textContent = '✓ copied';
        setTimeout(() => { el.textContent = orig; el.classList.remove('copied'); }, 1500);
    });
}

// ── Copy guide code blocks ────────────────────────────────────────────────
function copyGuide(blockId, btn) {
    const pre = document.getElementById(blockId).querySelector('pre');
    navigator.clipboard.writeText(pre.innerText).then(() => {
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
        btn.classList.add('copied');
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy';
            btn.classList.remove('copied');
        }, 2000);
    });
}

// ── Per-card term logic ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const PER_PAGE = 8;

    function initCard(card) {
        const form        = card.querySelector('.term-add-form');
        const input       = card.querySelector('.term-input');
        const submitBtn   = card.querySelector('.term-submit');
        const termList    = card.querySelector('.term-list');
        const pagination  = card.querySelector('.term-pagination');
        const pageInfo    = card.querySelector('.page-info');
        const btnPrev     = card.querySelector('.btn-prev');
        const btnNext     = card.querySelector('.btn-next');
        const filterEmpty = card.querySelector('.filter-empty-msg');
        const dupWarning  = card.querySelector('.duplicate-warning');
        const dupMsg      = card.querySelector('.dup-msg');
        const csrfToken   = form.querySelector('input[name="_token"]').value;

        let allRows = Array.from(termList.querySelectorAll('.term-row'));
        let filtered = allRows.slice();
        let currentPage = 1;

        function totalPages() { return Math.max(1, Math.ceil(filtered.length / PER_PAGE)); }

        function render() {
            allRows = Array.from(termList.querySelectorAll('.term-row'));
            const emptyEl = termList.querySelector('.empty-terms');

            if (filtered.length === 0 && allRows.length === 0) {
                if (emptyEl) emptyEl.style.display = 'block';
                filterEmpty.style.display = 'none';
                pagination.style.display  = 'none';
                return;
            }
            if (emptyEl) emptyEl.style.display = 'none';
            allRows.forEach(r => r.style.display = 'none');

            if (filtered.length === 0) {
                filterEmpty.style.display = 'block';
                pagination.style.display  = 'none';
                return;
            }
            filterEmpty.style.display = 'none';
            if (currentPage > totalPages()) currentPage = totalPages();

            const start = (currentPage - 1) * PER_PAGE;
            filtered.slice(start, Math.min(start + PER_PAGE, filtered.length))
                    .forEach(r => r.style.display = 'flex');

            if (totalPages() > 1) {
                pagination.style.display = 'flex';
                pageInfo.textContent     = `Page ${currentPage} of ${totalPages()} (${filtered.length} terms)`;
                btnPrev.disabled         = currentPage === 1;
                btnNext.disabled         = currentPage === totalPages();
            } else {
                pagination.style.display = 'none';
            }
        }

        function refreshFiltered() {
            allRows = Array.from(termList.querySelectorAll('.term-row'));
            const lower = input.value.trim().toLowerCase();
            filtered = lower ? allRows.filter(r =>
                r.querySelector('.term-text').textContent.trim().toLowerCase().includes(lower)
            ) : allRows.slice();
        }

        btnPrev.addEventListener('click', () => { if (currentPage > 1) { currentPage--; render(); } });
        btnNext.addEventListener('click', () => { if (currentPage < totalPages()) { currentPage++; render(); } });

        input.addEventListener('input', function () {
            const query = this.value.trim();
            const lower = query.toLowerCase();
            allRows = Array.from(termList.querySelectorAll('.term-row'));

            if (lower === '') {
                filtered = allRows.slice();
                allRows.forEach(r => r.classList.remove('term-highlight'));
            } else {
                filtered = allRows.filter(r =>
                    r.querySelector('.term-text').textContent.trim().toLowerCase().includes(lower)
                );
                allRows.forEach(r => r.classList.remove('term-highlight'));
                filtered.forEach(r => r.classList.add('term-highlight'));
            }
            currentPage = 1;
            render();

            const exactMatch = allRows.some(r =>
                r.querySelector('.term-text').textContent.trim().toLowerCase() === lower
            );
            if (query && exactMatch) {
                dupMsg.textContent = '"' + query + '" already exists.';
                dupWarning.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                dupWarning.style.display = 'none';
                submitBtn.disabled = false;
            }
        });

        // AJAX add term
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const termValue = input.value.trim();
            if (!termValue) return;
            submitBtn.disabled = true;

            const body = new URLSearchParams();
            body.append('_token', csrfToken);
            body.append('category', form.dataset.category);
            body.append('term', termValue);

            fetch(form.dataset.url, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body,
            })
            .then(r => r.json())
            .then(function (data) {
                if (data.id) {
                    const deleteUrl = form.dataset.deleteUrlBase + '/' + data.id;
                    const row = document.createElement('div');
                    row.className = 'term-row';
                    row.innerHTML =
                        '<span class="term-text">' + data.term.replace(/</g, '&lt;') + '</span>' +
                        '<form action="' + deleteUrl + '" method="POST" class="ms-2 flex-shrink-0">' +
                            '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                            '<input type="hidden" name="_method" value="DELETE">' +
                            '<button type="button" class="btn btn-outline-danger btn-sm py-0 px-1" title="Delete"' +
                                ' data-term="' + data.term.replace(/"/g, '&quot;') + '"' +
                                ' onclick="confirmDialog({title:\'Delete Term\',body:\'Delete &quot;\' + this.dataset.term + \'&quot;?\',confirmText:\'Delete\',confirmClass:\'btn-danger\',icon:\'bi-trash3-fill text-danger\'}, () => this.closest(\'form\').submit())">' +
                                '<i class="bi bi-trash3" style="font-size:.75rem;"></i>' +
                            '</button>' +
                        '</form>';

                    allRows = Array.from(termList.querySelectorAll('.term-row'));
                    const newTerm = data.term.toLowerCase();
                    let inserted = false;
                    for (const r of allRows) {
                        if (r.querySelector('.term-text').textContent.trim().toLowerCase() > newTerm) {
                            termList.insertBefore(row, r);
                            inserted = true;
                            break;
                        }
                    }
                    if (!inserted) termList.insertBefore(row, filterEmpty);

                    input.value = '';
                    dupWarning.style.display = 'none';
                    refreshFiltered();
                    currentPage = totalPages();
                    render();
                }
                submitBtn.disabled = false;
            })
            .catch(() => { submitBtn.disabled = false; });
        });

        if (allRows.length > 0) render();
    }

    document.querySelectorAll('.term-card').forEach(initCard);
});
</script>
@endpush
