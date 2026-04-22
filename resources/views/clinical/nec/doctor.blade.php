@extends('layouts.clinical')
@section('title', $pageTitle ?? 'Clinical Page')

@push('styles')
<style>
.dev-banner{background:linear-gradient(135deg,#1e3a5f 0%,#1d4ed8 55%,#3b82f6 100%);border-radius:1rem;padding:1.4rem 1.75rem;margin-bottom:1.25rem;position:relative;overflow:hidden}
.dev-banner::before{content:'';position:absolute;top:-35%;right:-2%;width:270px;height:270px;background:rgba(255,255,255,.07);border-radius:50%}
.dev-banner::after{content:'';position:absolute;bottom:-50%;right:14%;width:150px;height:150px;background:rgba(255,255,255,.05);border-radius:50%}
.dev-nav .nav-link{color:#64748b;font-weight:500;font-size:.85rem;border-radius:.5rem .5rem 0 0;padding:.55rem 1rem;border:1px solid transparent;transition:all .15s}
.dev-nav .nav-link:hover{color:#2563eb;background:#f1f5f9}
.dev-nav .nav-link.active{color:#2563eb;background:#fff;border-color:#e2e8f0 #e2e8f0 #fff;font-weight:600}
.dev-nav .nav-link i{opacity:.75}
.code-block{background:#0f172a;border-radius:.625rem;padding:1.1rem 1.25rem;position:relative;margin-bottom:.25rem}
.code-block pre{margin:0;font-size:.78rem;line-height:1.85;font-family:'Consolas','Fira Code','Monaco',monospace;white-space:pre;overflow-x:auto}
.copy-btn{position:absolute;top:.55rem;right:.6rem;font-size:.7rem;padding:.18rem .5rem;border-radius:.375rem;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);color:#94a3b8;cursor:pointer;transition:all .15s;z-index:1;line-height:1.4}
.copy-btn:hover{background:rgba(255,255,255,.2);color:#e2e8f0}
.copy-btn.copied{background:rgba(34,197,94,.22);border-color:rgba(34,197,94,.35);color:#86efac}
.step-num{width:1.9rem;height:1.9rem;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0}
.var-row td:first-child{font-family:'Consolas',monospace;font-size:.8rem;color:#2563eb;font-weight:600;white-space:nowrap}
.var-row td{vertical-align:top;padding:.5rem .75rem;font-size:.82rem}
/* syntax colours */
.kw{color:#93c5fd}.str{color:#86efac}.var{color:#fde68a}.cmt{color:#475569}.fn{color:#c4b5fd}.op{color:#fdba74}.tx{color:#e2e8f0}
</style>
@endpush

@section('content')

{{-- ══ DEVELOPER GUIDE — auto-generated. Replace this file with your clinical page. ══ --}}

{{-- Banner --}}
<div class="dev-banner">
    <div class="d-flex align-items-center justify-content-between gap-3 position-relative" style="z-index:1">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:3rem;height:3rem;background:rgba(255,255,255,.15);backdrop-filter:blur(4px)">
                <i class="bi bi-file-earmark-code-fill text-white" style="font-size:1.4rem"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill" style="background:rgba(255,255,255,.2);font-size:.68rem;letter-spacing:.05em">DEV GUIDE</span>
                    <span class="fw-bold text-white" style="font-size:1.05rem">{{ $viewTemplate->name }}</span>
                </div>
                <div class="small" style="color:rgba(255,255,255,.72)">
                    <i class="bi bi-building me-1"></i>{{ $unit->name }}
                    <span class="mx-2" style="opacity:.4">·</span>
                    <i class="bi bi-file-text me-1"></i>
                    <code style="background:rgba(0,0,0,.3);padding:1px 7px;border-radius:4px;color:rgba(255,255,255,.88);font-size:.72rem">
                        resources/views/{{ str_replace('.', '/', $viewTemplate->blade_path) }}.blade.php
                    </code>
                </div>
            </div>
        </div>
        <div class="text-end d-none d-md-block" style="position:relative;z-index:1">
            <div class="small text-white fw-semibold">Replace this file with</div>
            <div class="small" style="color:rgba(255,255,255,.65)">your actual clinical page</div>
        </div>
    </div>
</div>

{{-- ── Tab navigation ──────────────────────────────────────────────────── --}}
<ul class="nav dev-nav border-bottom mb-0" id="devTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-start"><i class="bi bi-house me-1"></i>Getting Started</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-tpl"><i class="bi bi-braces me-1"></i>Starter Template</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-comp"><i class="bi bi-boxes me-1"></i>Components</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-search"><i class="bi bi-search-heart me-1"></i>Search Boxes</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-vars"><i class="bi bi-database me-1"></i>Variables</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-back"><i class="bi bi-server me-1"></i>Backend</a></li>
</ul>

<div class="tab-content border border-top-0 rounded-bottom shadow-sm bg-white mb-4" id="devTabContent">

{{-- ══ TAB 1 — GETTING STARTED ══════════════════════════════════════════ --}}
<div class="tab-pane fade show active p-4" id="tab-start">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="alert alert-warning border-0 py-2 mb-4" style="background:#fef9c3">
                <i class="bi bi-lightbulb-fill me-2 text-warning"></i>
                <strong>This is a developer guide.</strong>
                Replace this file's content with your clinical page when you're ready.
            </div>
            <h6 class="fw-bold mb-3">Build your page in 4 steps</h6>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex gap-3">
                    <div class="step-num bg-primary text-white">1</div>
                    <div>
                        <div class="fw-semibold mb-1">Open the blade file in your code editor</div>
                        <code class="small text-muted">resources/views/{{ str_replace('.', '/', $viewTemplate->blade_path) }}.blade.php</code>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="step-num bg-primary text-white">2</div>
                    <div>
                        <div class="fw-semibold mb-1">Copy the Starter Template</div>
                        <span class="small text-muted">Go to the <strong>Starter Template</strong> tab → hit Copy → paste it into the file, replacing everything.</span>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="step-num bg-primary text-white">3</div>
                    <div>
                        <div class="fw-semibold mb-1">Add UI components</div>
                        <span class="small text-muted">Use the <strong>Components</strong> tab to grab ready-made snippets — cards, tables, forms, modals.</span>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="step-num bg-success text-white">4</div>
                    <div>
                        <div class="fw-semibold mb-1">Wire up backend data (optional)</div>
                        <span class="small text-muted">See the <strong>Backend</strong> tab to add routes and a controller for AJAX or form submissions.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="rounded-3 p-3 h-100" style="background:#f8fafc;border:1px solid #e2e8f0">
                <div class="small text-uppercase fw-semibold text-muted mb-3" style="letter-spacing:.07em;font-size:.68rem">
                    <i class="bi bi-info-circle me-1"></i>This page's context
                </div>
                <table class="table table-sm table-borderless mb-3 small">
                    <tr><td class="text-muted pe-3" style="width:90px">View name</td><td class="fw-semibold">{{ $viewTemplate->name }}</td></tr>
                    <tr><td class="text-muted pe-3">Unit</td><td>{{ $unit->name }}</td></tr>
                    <tr><td class="text-muted pe-3">Institution</td><td>{{ $unit->institution->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Blade path</td><td><code class="small">{{ $viewTemplate->blade_path }}</code></td></tr>
                    <tr><td class="text-muted pe-3">Logged in as</td><td>{{ auth()->user()->name }}</td></tr>
                </table>
                <div class="border-top pt-3">
                    <div class="small text-muted fw-semibold mb-2">Live examples below ↓</div>
                    <div class="mb-2">
                        <label class="form-label small mb-1 text-muted">Terminology search</label>
                        <input type="text" class="form-control form-control-sm terminology-search" placeholder="Try typing a diagnosis…">
                    </div>
                    <div>
                        <label class="form-label small mb-1 text-muted">Drug search</label>
                        <input type="text" class="form-control form-control-sm drug-search" placeholder="Try typing a drug name…">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ TAB 2 — STARTER TEMPLATE ════════════════════════════════════════ --}}
<div class="tab-pane fade p-4" id="tab-tpl">
    <p class="text-muted small mb-3">
        Copy the entire block below and <strong>replace everything</strong> in your blade file with it.
        This gives you a clean, working page with a page header, session flash alerts, and a content card.
    </p>
    <div class="code-block" id="cb-tpl">
        <button class="copy-btn" onclick="copyCode('cb-tpl',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code><span class="kw">@extends</span>(<span class="str">'layouts.clinical'</span>)
<span class="kw">@section</span>(<span class="str">'title'</span>, $pageTitle)

<span class="kw">@push</span>(<span class="str">'styles'</span>)
<span class="cmt">&lt;style&gt;
    /* page-specific CSS — prefix selectors to avoid leaking */
    :root { --accent: #2563eb; --accent-light: #eff6ff; }
&lt;/style&gt;</span>
<span class="kw">@endpush</span>

<span class="kw">@section</span>(<span class="str">'content'</span>)

&lt;!-- Page header --&gt;
&lt;div class=<span class="str">"mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2"</span>&gt;
    &lt;div&gt;
        &lt;h4 class=<span class="str">"fw-bold mb-1"</span>&gt;<span class="var">{{ $pageTitle }}</span>&lt;/h4&gt;
        &lt;p class=<span class="str">"text-muted small mb-0"</span>&gt;
            <span class="var">{{ $unit->name }}</span> &amp;middot; <span class="var">{{ $unit->institution->name ?? '' }}</span>
        &lt;/p&gt;
    &lt;/div&gt;
    &lt;button class=<span class="str">"btn btn-primary btn-sm"</span>&gt;
        &lt;i class=<span class="str">"bi bi-plus-lg me-1"</span>&gt;&lt;/i&gt;New Entry
    &lt;/button&gt;
&lt;/div&gt;

&lt;!-- Main content card --&gt;
&lt;div class=<span class="str">"card border-0 shadow-sm"</span>&gt;
    &lt;div class=<span class="str">"card-header bg-white border-bottom py-3 d-flex align-items-center gap-2"</span>&gt;
        &lt;i class=<span class="str">"bi bi-clipboard2-pulse text-primary"</span>&gt;&lt;/i&gt;
        &lt;span class=<span class="str">"fw-semibold"</span>&gt;Clinical Data&lt;/span&gt;
    &lt;/div&gt;
    &lt;div class=<span class="str">"card-body p-4"</span>&gt;
        &lt;p class=<span class="str">"text-muted"</span>&gt;Add your page content here.&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;

<span class="kw">@endsection</span>

<span class="kw">@push</span>(<span class="str">'scripts'</span>)
<span class="cmt">&lt;script&gt;
    // page-specific JavaScript — Bootstrap 5, terminology-search and drug-search already loaded
&lt;/script&gt;</span>
<span class="kw">@endpush</span></code></pre>
@endverbatim
    </div>
</div>

{{-- ══ TAB 3 — COMPONENTS ══════════════════════════════════════════════ --}}
<div class="tab-pane fade p-4" id="tab-comp">
    <p class="text-muted small mb-4">
        Copy any snippet and paste it inside your <code>@section('content')</code>.
        All use Bootstrap 5 which is already loaded on every clinical page.
    </p>

    {{-- A: Flash alerts --}}
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-secondary rounded-pill">A</span>
        <h6 class="fw-bold mb-0">Flash Alerts</h6>
    </div>
    <div class="row g-3 align-items-start mb-4">
        <div class="col-md-4">
            <div class="alert alert-success py-2 mb-2 small"><i class="bi bi-check-circle me-2"></i>Patient saved successfully.</div>
            <div class="alert alert-danger py-2 mb-2 small"><i class="bi bi-exclamation-triangle me-2"></i>Something went wrong.</div>
            <div class="alert alert-info py-2 mb-0 small"><i class="bi bi-info-circle me-2"></i>Review pending.</div>
        </div>
        <div class="col-md-8">
            <div class="code-block" id="cb-alert">
                <button class="copy-btn" onclick="copyCode('cb-alert',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code><span class="kw">@if</span>(session(<span class="str">'success'</span>))
&lt;div class=<span class="str">"alert alert-success"</span>&gt;
    &lt;i class=<span class="str">"bi bi-check-circle me-2"</span>&gt;&lt;/i&gt;<span class="var">{{ session('success') }}</span>
&lt;/div&gt;
<span class="kw">@endif</span>
<span class="kw">@if</span>(session(<span class="str">'error'</span>))
&lt;div class=<span class="str">"alert alert-danger"</span>&gt;
    &lt;i class=<span class="str">"bi bi-exclamation-triangle me-2"</span>&gt;&lt;/i&gt;<span class="var">{{ session('error') }}</span>
&lt;/div&gt;
<span class="kw">@endif</span></code></pre>
@endverbatim
            </div>
        </div>
    </div>
    <hr class="my-4">

    {{-- B: Card with header --}}
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-secondary rounded-pill">B</span>
        <h6 class="fw-bold mb-0">Card with Header</h6>
    </div>
    <div class="row g-3 align-items-start mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard2-pulse text-primary"></i>
                    <span class="fw-semibold small">Card Title</span>
                    <span class="badge bg-primary ms-auto">5</span>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted small mb-0">Card body content.</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="code-block" id="cb-card">
                <button class="copy-btn" onclick="copyCode('cb-card',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code>&lt;div class=<span class="str">"card border-0 shadow-sm"</span>&gt;
    &lt;div class=<span class="str">"card-header bg-white border-bottom py-3
                d-flex align-items-center gap-2"</span>&gt;
        &lt;i class=<span class="str">"bi bi-clipboard2-pulse text-primary"</span>&gt;&lt;/i&gt;
        &lt;span class=<span class="str">"fw-semibold"</span>&gt;Card Title&lt;/span&gt;
        &lt;span class=<span class="str">"badge bg-primary ms-auto"</span>&gt;<span class="var">{{ $count }}</span>&lt;/span&gt;
    &lt;/div&gt;
    &lt;div class=<span class="str">"card-body p-4"</span>&gt;
        &lt;p&gt;Content here.&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
@endverbatim
            </div>
        </div>
    </div>
    <hr class="my-4">

    {{-- C: Data table --}}
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-secondary rounded-pill">C</span>
        <h6 class="fw-bold mb-0">Data Table (with loop)</h6>
    </div>
    <div class="row g-3 align-items-start mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light"><tr><th class="ps-3">Patient</th><th>Date</th><th class="text-end pe-3">Status</th></tr></thead>
                        <tbody>
                            <tr><td class="ps-3 fw-medium">John Silva</td><td class="text-muted small">2026-04-22</td><td class="text-end pe-3"><span class="badge bg-success">Active</span></td></tr>
                            <tr><td class="ps-3 fw-medium">Nimal Perera</td><td class="text-muted small">2026-04-21</td><td class="text-end pe-3"><span class="badge bg-warning text-dark">Pending</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="code-block" id="cb-tbl">
                <button class="copy-btn" onclick="copyCode('cb-tbl',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code>&lt;div class=<span class="str">"card border-0 shadow-sm"</span>&gt;
  &lt;div class=<span class="str">"card-body p-0"</span>&gt;
    &lt;table class=<span class="str">"table table-hover table-sm mb-0"</span>&gt;
      &lt;thead class=<span class="str">"table-light"</span>&gt;
        &lt;tr&gt;
          &lt;th class=<span class="str">"ps-3"</span>&gt;Patient&lt;/th&gt;
          &lt;th&gt;Date&lt;/th&gt;
          &lt;th class=<span class="str">"text-end pe-3"</span>&gt;Status&lt;/th&gt;
        &lt;/tr&gt;
      &lt;/thead&gt;
      &lt;tbody&gt;
        <span class="kw">@forelse</span>($items <span class="kw">as</span> $item)
        &lt;tr&gt;
          &lt;td class=<span class="str">"ps-3 fw-medium"</span>&gt;<span class="var">{{ $item->name }}</span>&lt;/td&gt;
          &lt;td class=<span class="str">"text-muted small"</span>&gt;<span class="var">{{ $item->created_at->format('Y-m-d') }}</span>&lt;/td&gt;
          &lt;td class=<span class="str">"text-end pe-3"</span>&gt;
            &lt;span class=<span class="str">"badge bg-success"</span>&gt;Active&lt;/span&gt;
          &lt;/td&gt;
        &lt;/tr&gt;
        <span class="kw">@empty</span>
        &lt;tr&gt;&lt;td colspan=<span class="str">"3"</span> class=<span class="str">"text-center text-muted py-4"</span>&gt;No records yet.&lt;/td&gt;&lt;/tr&gt;
        <span class="kw">@endforelse</span>
      &lt;/tbody&gt;
    &lt;/table&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
@endverbatim
            </div>
        </div>
    </div>
    <hr class="my-4">

    {{-- D: Form --}}
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-secondary rounded-pill">D</span>
        <h6 class="fw-bold mb-0">Form with POST</h6>
    </div>
    <div class="row g-3 align-items-start mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="mb-3"><label class="form-label fw-medium small">Notes</label><textarea class="form-control form-control-sm" rows="2" placeholder="Enter notes..."></textarea></div>
                    <div class="mb-3"><label class="form-label fw-medium small">Status</label><select class="form-select form-select-sm"><option>Active</option><option>Discharged</option></select></div>
                    <button class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Save</button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="code-block" id="cb-form">
                <button class="copy-btn" onclick="copyCode('cb-form',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code>&lt;form method=<span class="str">"POST"</span> action=<span class="var">{{ route('clinical.my-action.store', $unitView) }}</span>&gt;
    <span class="kw">@csrf</span>

    &lt;div class=<span class="str">"mb-3"</span>&gt;
        &lt;label class=<span class="str">"form-label fw-medium"</span>&gt;Notes&lt;/label&gt;
        &lt;textarea name=<span class="str">"notes"</span> class=<span class="str">"form-control"</span> rows=<span class="str">"3"</span>
            &gt;<span class="var">{{ old('notes') }}</span>&lt;/textarea&gt;
    &lt;/div&gt;

    &lt;div class=<span class="str">"mb-3"</span>&gt;
        &lt;label class=<span class="str">"form-label fw-medium"</span>&gt;Status&lt;/label&gt;
        &lt;select name=<span class="str">"status"</span> class=<span class="str">"form-select"</span>&gt;
            &lt;option value=<span class="str">"active"</span>
                <span class="kw">@selected</span>(old(<span class="str">'status'</span>) == <span class="str">'active'</span>)&gt;Active&lt;/option&gt;
            &lt;option value=<span class="str">"discharged"</span>
                <span class="kw">@selected</span>(old(<span class="str">'status'</span>) == <span class="str">'discharged'</span>)&gt;Discharged&lt;/option&gt;
        &lt;/select&gt;
    &lt;/div&gt;

    &lt;button type=<span class="str">"submit"</span> class=<span class="str">"btn btn-primary"</span>&gt;
        &lt;i class=<span class="str">"bi bi-check-lg me-1"</span>&gt;&lt;/i&gt;Save
    &lt;/button&gt;
&lt;/form&gt;</code></pre>
@endverbatim
            </div>
        </div>
    </div>
    <hr class="my-4">

    {{-- E: Modal --}}
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-secondary rounded-pill">E</span>
        <h6 class="fw-bold mb-0">Modal Dialog</h6>
    </div>
    <div class="row g-3 align-items-start mb-2">
        <div class="col-md-4">
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#demoModal">
                <i class="bi bi-box-arrow-up-right me-1"></i>Open example modal
            </button>
            <div class="modal fade" id="demoModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header"><h6 class="modal-title fw-bold">Modal Title</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body"><p class="text-muted small mb-0">Modal body content goes here.</p></div>
                        <div class="modal-footer border-top"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary btn-sm">Confirm</button></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="code-block" id="cb-modal">
                <button class="copy-btn" onclick="copyCode('cb-modal',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code><span class="cmt">&lt;!-- Trigger --&gt;</span>
&lt;button class=<span class="str">"btn btn-primary btn-sm"</span>
        data-bs-toggle=<span class="str">"modal"</span> data-bs-target=<span class="str">"#myModal"</span>&gt;Open&lt;/button&gt;

<span class="cmt">&lt;!-- Modal (place anywhere inside @section('content')) --&gt;</span>
&lt;div class=<span class="str">"modal fade"</span> id=<span class="str">"myModal"</span> tabindex=<span class="str">"-1"</span>&gt;
  &lt;div class=<span class="str">"modal-dialog modal-dialog-centered"</span>&gt;
    &lt;div class=<span class="str">"modal-content border-0 shadow"</span>&gt;
      &lt;div class=<span class="str">"modal-header"</span>&gt;
        &lt;h6 class=<span class="str">"modal-title fw-bold"</span>&gt;Title&lt;/h6&gt;
        &lt;button class=<span class="str">"btn-close"</span> data-bs-dismiss=<span class="str">"modal"</span>&gt;&lt;/button&gt;
      &lt;/div&gt;
      &lt;div class=<span class="str">"modal-body"</span>&gt;Body&lt;/div&gt;
      &lt;div class=<span class="str">"modal-footer border-top"</span>&gt;
        &lt;button class=<span class="str">"btn btn-secondary btn-sm"</span>
                data-bs-dismiss=<span class="str">"modal"</span>&gt;Cancel&lt;/button&gt;
        &lt;button class=<span class="str">"btn btn-primary btn-sm"</span>&gt;Save&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
@endverbatim
            </div>
        </div>
    </div>
</div>

{{-- ══ TAB 4 — SEARCH BOXES ════════════════════════════════════════════ --}}
<div class="tab-pane fade p-4" id="tab-search">
    <p class="text-muted small mb-4">
        The clinical layout automatically wires up two autocomplete systems.
        Just add the right CSS class to any <code>&lt;input&gt;</code> — no extra JavaScript needed.
    </p>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
            <i class="bi bi-search-heart text-success"></i>
            <span class="fw-semibold">Terminology Search</span>
            <code class="ms-2 small" style="background:#f0fdf4;padding:2px 7px;border-radius:4px;color:#15803d">class="terminology-search"</code>
        </div>
        <div class="card-body p-4">
            <p class="small text-muted mb-3">
                Searches the <em>Admin → Terminology</em> database. Filter by category with <code>data-category</code>.<br>
                Built-in categories:
                <code>diagnosis</code> <code>symptom</code> <code>procedure</code> <code>allergy</code> <code>investigation</code>
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-semibold">All terms <span class="badge bg-success ms-1" style="font-size:.65rem">LIVE</span></label>
                    <input type="text" class="form-control terminology-search" placeholder="Start typing anything…">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-semibold">Diagnosis only <span class="badge bg-success ms-1" style="font-size:.65rem">LIVE</span></label>
                    <input type="text" class="form-control terminology-search" data-category="diagnosis" placeholder="Type a diagnosis…">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-semibold">Drug search <span class="badge bg-info ms-1" style="font-size:.65rem">LIVE</span></label>
                    <input type="text" class="form-control drug-search" placeholder="Type a drug name…">
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="small fw-semibold text-muted mb-1">Terminology input</div>
                    <div class="code-block" id="cb-term">
                        <button class="copy-btn" onclick="copyCode('cb-term',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code><span class="cmt">&lt;!-- All categories --&gt;</span>
&lt;input type=<span class="str">"text"</span>
       class=<span class="str">"form-control <span class="op">terminology-search</span>"</span>
       name=<span class="str">"finding"</span>
       placeholder=<span class="str">"Start typing..."</span>&gt;

<span class="cmt">&lt;!-- Filter to one category --&gt;</span>
&lt;input type=<span class="str">"text"</span>
       class=<span class="str">"form-control <span class="op">terminology-search</span>"</span>
       <span class="var">data-category</span>=<span class="str">"diagnosis"</span>
       name=<span class="str">"diagnosis"</span>
       placeholder=<span class="str">"Type a diagnosis..."</span>&gt;

<span class="cmt">&lt;!-- Endpoint: GET /terminology/search?q=&amp;category= --&gt;</span></code></pre>
@endverbatim
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="small fw-semibold text-muted mb-1">Drug input</div>
                    <div class="code-block" id="cb-drug">
                        <button class="copy-btn" onclick="copyCode('cb-drug',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code><span class="cmt">&lt;!-- Drug autocomplete --&gt;</span>
&lt;input type=<span class="str">"text"</span>
       class=<span class="str">"form-control <span class="op">drug-search</span>"</span>
       name=<span class="str">"drug_name"</span>
       placeholder=<span class="str">"Drug name..."</span>&gt;

<span class="cmt">&lt;!-- Endpoint: GET /drugs/search?q= --&gt;</span>
<span class="cmt">&lt;!-- Multiple boxes per page: all work --&gt;</span>
<span class="cmt">&lt;!-- independently and automatically.   --&gt;</span></code></pre>
@endverbatim
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ TAB 5 — VARIABLES ════════════════════════════════════════════════ --}}
<div class="tab-pane fade p-4" id="tab-vars">
    <p class="text-muted small mb-3">
        Every clinical blade file receives these variables automatically from
        <code>ClinicalDashboardController::show()</code>. No extra setup needed.
    </p>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:190px">Variable</th>
                        <th style="width:140px">Type</th>
                        <th>What it contains / example usage</th>
                    </tr>
                </thead>
                <tbody class="var-row">
                    <tr>
                        <td class="ps-3"><code>$pageTitle</code></td>
                        <td class="text-muted">string</td>
                        <td>Currently: <em>"{{ $viewTemplate->name }} — {{ $unit->name }}"</em></td>
                    </tr>
                    <tr>
                        <td class="ps-3"><code>$unitView</code></td>
                        <td class="text-muted">UnitView</td>
                        <td>The view assignment record. Pass to routes: <code>route('...', $unitView)</code></td>
                    </tr>
                    <tr>
                        <td class="ps-3"><code>$unit</code></td>
                        <td class="text-muted">Unit</td>
                        <td><code>$unit->name</code> → <em>{{ $unit->name }}</em> &nbsp;|&nbsp; <code>$unit->institution_id</code></td>
                    </tr>
                    <tr>
                        <td class="ps-3"><code>$unit->institution</code></td>
                        <td class="text-muted">Institution</td>
                        <td><code>$unit->institution->name</code> → <em>{{ $unit->institution->name ?? '—' }}</em></td>
                    </tr>
                    <tr>
                        <td class="ps-3"><code>$unit->unitTemplate</code></td>
                        <td class="text-muted">UnitTemplate</td>
                        <td><code>$unit->unitTemplate->code</code> — template type (e.g. GMC, DC, GP)</td>
                    </tr>
                    <tr>
                        <td class="ps-3"><code>$viewTemplate</code></td>
                        <td class="text-muted">ViewTemplate</td>
                        <td><code>$viewTemplate->name</code>, <code>$viewTemplate->code</code>, <code>$viewTemplate->blade_path</code></td>
                    </tr>
                    <tr>
                        <td class="ps-3"><code>auth()->user()</code></td>
                        <td class="text-muted">User</td>
                        <td><code>auth()->user()->name</code> → <em>{{ auth()->user()->name }}</em></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="alert alert-info border-0 py-2 small" style="background:#eff6ff">
        <i class="bi bi-info-circle me-2 text-primary"></i>
        <strong>Tip:</strong> Relationships (<code>institution</code>, <code>unitTemplate</code>) are eager-loaded —
        using them will not fire extra database queries.
    </div>
</div>

{{-- ══ TAB 6 — BACKEND ══════════════════════════════════════════════════ --}}
<div class="tab-pane fade p-4" id="tab-back">
    <p class="text-muted small mb-4">
        To fetch or store data, you need a controller and routes.
        The clinical route group lives in <code>routes/web.php</code>.
    </p>

    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="step-num bg-primary text-white">1</span>
        <h6 class="fw-bold mb-0">Create a controller</h6>
    </div>
    <div class="code-block mb-4" id="cb-artisan">
        <button class="copy-btn" onclick="copyCode('cb-artisan',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code><span class="cmt"># Run in your project terminal:</span>
php artisan make:controller Clinical/MyPageController</code></pre>
@endverbatim
    </div>

    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="step-num bg-primary text-white">2</span>
        <h6 class="fw-bold mb-0">Add routes in <code>routes/web.php</code></h6>
    </div>
    <p class="text-muted small mb-2">Place these <strong>before</strong> the <code>/{unitView}</code> catch-all at the bottom of the clinical group.</p>
    <div class="code-block mb-4" id="cb-routes">
        <button class="copy-btn" onclick="copyCode('cb-routes',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code><span class="cmt">// routes/web.php — inside the clinical middleware group</span>
<span class="kw">use</span> App\Http\Controllers\Clinical\MyPageController;

Route::get(<span class="str">'/{unitView}/my-page'</span>,
    [MyPageController::<span class="fn">class</span>, <span class="str">'index'</span>])->name(<span class="str">'clinical.my-page'</span>);

Route::post(<span class="str">'/{unitView}/my-page'</span>,
    [MyPageController::<span class="fn">class</span>, <span class="str">'store'</span>])->name(<span class="str">'clinical.my-page.store'</span>);

Route::get(<span class="str">'/{unitView}/my-page/data'</span>,
    [MyPageController::<span class="fn">class</span>, <span class="str">'data'</span>])->name(<span class="str">'clinical.my-page.data'</span>);
<span class="cmt">//                                  ↑ returns JSON for AJAX calls</span></code></pre>
@endverbatim
    </div>

    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="step-num bg-primary text-white">3</span>
        <h6 class="fw-bold mb-0">Controller boilerplate</h6>
    </div>
    <div class="code-block mb-4" id="cb-ctrl">
        <button class="copy-btn" onclick="copyCode('cb-ctrl',this)"><i class="bi bi-clipboard me-1"></i>Copy</button>
@verbatim
<pre><code><span class="fn">namespace</span> App\Http\Controllers\Clinical;

<span class="kw">use</span> App\Http\Controllers\Controller;
<span class="kw">use</span> App\Models\UnitView;
<span class="kw">use</span> Illuminate\Http\Request;

<span class="kw">class</span> <span class="op">MyPageController</span> <span class="kw">extends</span> Controller
{
    <span class="kw">public function</span> <span class="fn">index</span>(UnitView $unitView)
    {
        $unit         = $unitView->unit->load(<span class="str">'institution'</span>, <span class="str">'unitTemplate'</span>);
        $viewTemplate = $unitView->viewTemplate;
        $pageTitle    = $viewTemplate->name . <span class="str">' — '</span> . $unit->name;

        <span class="cmt">// Fetch whatever this page needs:</span>
        $items = MyModel::where(<span class="str">'unit_id'</span>, $unit->id)->latest()->get();

        <span class="kw">return</span> view(<span class="str">'clinical.my.page'</span>, compact(
            <span class="str">'unitView'</span>, <span class="str">'unit'</span>, <span class="str">'viewTemplate'</span>, <span class="str">'pageTitle'</span>, <span class="str">'items'</span>
        ));
    }

    <span class="kw">public function</span> <span class="fn">store</span>(Request $request, UnitView $unitView)
    {
        $data = $request->validate([
            <span class="str">'notes'</span>  => <span class="str">'required|string'</span>,
            <span class="str">'status'</span> => <span class="str">'required|in:active,discharged'</span>,
        ]);

        MyModel::create($data + [<span class="str">'unit_id'</span> => $unitView->unit_id]);

        <span class="kw">return</span> back()->with(<span class="str">'success'</span>, <span class="str">'Saved successfully.'</span>);
    }

    <span class="kw">public function</span> <span class="fn">data</span>(UnitView $unitView)
    {
        <span class="kw">return</span> response()->json(
            MyModel::where(<span class="str">'unit_id'</span>, $unitView->unit_id)->latest()->get()
        );
    }
}</code></pre>
@endverbatim
    </div>

    <div class="alert border-0 py-2 small" style="background:#fef9c3">
        <i class="bi bi-lightbulb-fill me-2 text-warning"></i>
        <strong>Note:</strong> If you add your own controller and routes, update the
        View Template's <code>blade_path</code> to point to the blade file your controller renders.
    </div>
</div>

</div>
{{-- ══ end developer guide ══════════════════════════════════════════════ --}}

@endsection

@push('scripts')
<script>
function copyCode(blockId, btn) {
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
</script>
@endpush