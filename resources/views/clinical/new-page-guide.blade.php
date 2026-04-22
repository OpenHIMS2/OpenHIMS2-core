@extends('layouts.clinical')
@section('title', $pageTitle ?? 'New Page Guide')

@push('styles')
<style>
    pre { background: #0f172a; color: #e2e8f0; border-radius: .75rem; padding: 1.25rem 1.5rem;
          font-size: .8rem; line-height: 1.6; overflow-x: auto; }
    .kw  { color: #93c5fd; }  /* keyword / tag */
    .str { color: #86efac; }  /* string value */
    .cmt { color: #64748b; font-style: italic; }  /* comment */
    .var { color: #fde68a; }  /* variable / directive */
    .fn  { color: #f9a8d4; }  /* method / function */
    .step-badge { width: 2rem; height: 2rem; border-radius: 50%; display: inline-flex;
                  align-items: center; justify-content: center; font-weight: 700;
                  font-size: .85rem; flex-shrink: 0; }
</style>
@endpush

@section('content')
{{-- ── Dev Placeholder Banner ──────────────────────────────────────── --}}
<div class="alert border-0 mb-4" style="background:linear-gradient(135deg,#fef3c7,#fde68a);border-left:4px solid #f59e0b!important;">
    <div class="d-flex align-items-start gap-3">
        <i class="bi bi-tools fs-3 text-warning mt-1"></i>
        <div>
            <h5 class="fw-bold mb-1" style="color:#92400e;">Page Not Built Yet</h5>
            <p class="mb-0 small" style="color:#78350f;">
                The view template <strong>{{ $viewTemplate->name }}</strong> is registered in the database,
                but its blade file does not exist yet. Follow the guide below to create it.
                Once the file is created, this placeholder disappears automatically.
            </p>
        </div>
    </div>
</div>

{{-- ── Template Info Card ──────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>View Template Details</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-sm-6 col-md-3">
                <p class="text-muted small mb-1">Template Name</p>
                <p class="fw-semibold mb-0">{{ $viewTemplate->name }}</p>
            </div>
            <div class="col-sm-6 col-md-3">
                <p class="text-muted small mb-1">Code</p>
                <code class="text-primary">{{ $viewTemplate->code }}</code>
            </div>
            <div class="col-sm-6 col-md-3">
                <p class="text-muted small mb-1">Unit Template</p>
                <p class="fw-semibold mb-0">{{ $viewTemplate->unitTemplate->name ?? '—' }}</p>
            </div>
            <div class="col-sm-6 col-md-3">
                <p class="text-muted small mb-1">Assigned Unit</p>
                <p class="fw-semibold mb-0">{{ $unit->name }}</p>
            </div>
        </div>
        <hr class="my-3">
        <p class="text-muted small mb-1">Blade Path (dot notation)</p>
        <code class="fs-6 text-danger">{{ $viewTemplate->blade_path }}</code>
        <p class="text-muted small mt-2 mb-0">
            <i class="bi bi-arrow-right me-1"></i>
            Create the file at:
            <code class="text-success">resources/views/{{ str_replace('.', '/', $viewTemplate->blade_path) }}.blade.php</code>
        </p>
    </div>
</div>

{{-- ── Step-by-Step Guide ──────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-book me-2 text-primary"></i>How to Create a New Clinical Page from Scratch</h6>
    </div>
    <div class="card-body">

        {{-- Step 1 --}}
        <div class="d-flex gap-3 mb-4">
            <div class="step-badge bg-primary text-white mt-1">1</div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">Create the blade file</h6>
                <p class="text-muted small mb-2">
                    Create the directory structure if needed, then create the file. For this template the path is:
                </p>
                <pre><code>resources/views/{{ str_replace('.', '/', $viewTemplate->blade_path) }}.blade.php</code></pre>
                <p class="text-muted small mb-0">On Windows with XAMPP, the full path would be:
                    <code>H:\xampp\htdocs\PHIMS\resources\views\{{ str_replace('.', '\\', $viewTemplate->blade_path) }}.blade.php</code>
                </p>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="d-flex gap-3 mb-4">
            <div class="step-badge bg-primary text-white mt-1">2</div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">Use the clinical layout and declare a title</h6>
                <p class="text-muted small mb-2">Every clinical page must extend the shared layout:</p>
                <pre><code><span class="var">@extends</span>(<span class="str">'layouts.clinical'</span>)
<span class="var">@section</span>(<span class="str">'title'</span>, <span class="var">$pageTitle</span> ?? <span class="str">'{{ $viewTemplate->name }}'</span>)</code></pre>
            </div>
        </div>

        {{-- Step 3 --}}
        <div class="d-flex gap-3 mb-4">
            <div class="step-badge bg-primary text-white mt-1">3</div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">Available variables (injected automatically)</h6>
                <p class="text-muted small mb-2">These variables are always passed by the controller — you can use them immediately:</p>
                <pre><code><span class="var">$unitView</span>       <span class="cmt">// UnitView model — the specific view instance assigned to this user</span>
<span class="var">$unit</span>           <span class="cmt">// Unit model — the physical unit (e.g. "GMC Akurana")</span>
<span class="var">$viewTemplate</span>   <span class="cmt">// ViewTemplate model — this template's name, code, blade_path</span>
<span class="var">$pageTitle</span>      <span class="cmt">// String — e.g. "{{ $viewTemplate->name }} — {{ $unit->name }}"</span>

<span class="cmt">// Useful relationships already eager-loaded:</span>
<span class="var">$unit</span>-><span class="fn">institution</span>    <span class="cmt">// Institution model</span>
<span class="var">$unit</span>-><span class="fn">unitTemplate</span>   <span class="cmt">// UnitTemplate model</span></code></pre>
            </div>
        </div>

        {{-- Step 4 --}}
        <div class="d-flex gap-3 mb-4">
            <div class="step-badge bg-primary text-white mt-1">4</div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">Write the page content</h6>
                <p class="text-muted small mb-2">Add custom styles in a <code>@push('styles')</code> block, then write your content in the <code>@section('content')</code>:</p>
                <pre><code><span class="var">@push</span>(<span class="str">'styles'</span>)
<span class="kw">&lt;style&gt;</span>
    <span class="cmt">/* page-specific CSS goes here */</span>
<span class="kw">&lt;/style&gt;</span>
<span class="var">@endpush</span>

<span class="var">@section</span>(<span class="str">'content'</span>)
<span class="kw">&lt;div</span> <span class="fn">class</span>=<span class="str">"mb-4"</span><span class="kw">&gt;</span>
    <span class="kw">&lt;h4</span> <span class="fn">class</span>=<span class="str">"fw-bold"</span><span class="kw">&gt;</span>{{ "<span class='var'>{{ $pageTitle }}</span>" }}<span class="kw">&lt;/h4&gt;</span>
    <span class="kw">&lt;p</span> <span class="fn">class</span>=<span class="str">"text-muted small"</span><span class="kw">&gt;</span>{{ "<span class='var'>{{ $unit->name }}</span>" }} · {{ "<span class='var'>{{ $unit->institution->name }}</span>" }}<span class="kw">&lt;/p&gt;</span>
<span class="kw">&lt;/div&gt;</span>

<span class="cmt">&lt;!-- your page content here --&gt;</span>
<span class="var">@endsection</span></code></pre>
            </div>
        </div>

        {{-- Step 5 --}}
        <div class="d-flex gap-3 mb-4">
            <div class="step-badge bg-success text-white mt-1">5</div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">Link to Terminology Boxes (autocomplete)</h6>
                <p class="text-muted small mb-2">
                    OpenHIMS2 has a terminology system backed by the <code>terminology_terms</code> table.
                    Any text input can be turned into a live-search autocomplete box with two attributes:
                </p>
                <pre><code><span class="kw">&lt;input</span> <span class="fn">type</span>=<span class="str">"text"</span>
       <span class="fn">class</span>=<span class="str">"form-control terminology-search"</span>
       <span class="fn">data-category</span>=<span class="str">"diagnosis"</span>    <span class="cmt">&lt;!-- filter by category (optional) --&gt;</span>
       <span class="fn">name</span>=<span class="str">"diagnosis"</span>
       <span class="fn">placeholder</span>=<span class="str">"Start typing..."</span><span class="kw">&gt;</span></code></pre>
                <p class="text-muted small mt-2 mb-2">
                    The shared clinical layout already includes the autocomplete JavaScript at the bottom of every page.
                    The script watches for inputs with the class <code>terminology-search</code> and calls
                    <code>GET /terminology/search?q=...&amp;category=...</code> as the user types.
                    Results appear in a Bootstrap dropdown beneath the input.
                </p>
                <p class="text-muted small mb-2">Available categories (from <code>Admin → Terminology</code>):</p>
                <ul class="small text-muted mb-0">
                    <li><code>diagnosis</code> — ICD-style diagnoses</li>
                    <li><code>symptom</code> — presenting symptoms</li>
                    <li><code>procedure</code> — clinical procedures</li>
                    <li><code>allergy</code> — allergy substances</li>
                    <li>Any custom category added via Admin → Terminology</li>
                </ul>
            </div>
        </div>

        {{-- Step 6 --}}
        <div class="d-flex gap-3 mb-4">
            <div class="step-badge bg-info text-white mt-1">6</div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">Drug autocomplete (optional)</h6>
                <p class="text-muted small mb-2">
                    Similarly, drug name inputs can use the drug search endpoint:
                </p>
                <pre><code><span class="kw">&lt;input</span> <span class="fn">type</span>=<span class="str">"text"</span>
       <span class="fn">class</span>=<span class="str">"form-control drug-search"</span>
       <span class="fn">name</span>=<span class="str">"drug_name"</span>
       <span class="fn">placeholder</span>=<span class="str">"Drug name..."</span><span class="kw">&gt;</span></code></pre>
                <p class="text-muted small mt-2 mb-0">
                    Calls <code>GET /drugs/search?q=...</code>. The clinical layout includes this script too.
                </p>
            </div>
        </div>

        {{-- Step 7 --}}
        <div class="d-flex gap-3">
            <div class="step-badge bg-secondary text-white mt-1">7</div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">Adding new backend routes for this page</h6>
                <p class="text-muted small mb-2">
                    If the page needs its own controller actions (AJAX calls, form posts, etc.) add them in
                    <code>routes/web.php</code> inside the <code>clinical</code> group, <strong>before</strong>
                    the <code>/{unitView}</code> catch-all at the bottom:
                </p>
                <pre><code><span class="cmt">// routes/web.php — inside the clinical group, before /{unitView}</span>
Route::<span class="fn">get</span>(<span class="str">'/{unitView}/my-custom-action'</span>, [MyController::<span class="fn">class</span>, <span class="str">'myMethod'</span>])
     -><span class="fn">name</span>(<span class="str">'clinical.my-custom-action'</span>);</code></pre>
                <p class="text-muted small mt-2 mb-0">
                    Use <code>php artisan make:controller Clinical/MyController</code> to scaffold a new controller
                    in the correct namespace.
                </p>
            </div>
        </div>

    </div>
</div>

{{-- ── Full Starter Template ──────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-code me-2 text-primary"></i>Complete Starter Template (copy &amp; paste)</h6>
        <span class="badge bg-light text-dark border font-monospace small">
            resources/views/{{ str_replace('.', '/', $viewTemplate->blade_path) }}.blade.php
        </span>
    </div>
    <div class="card-body p-0">
        <pre class="m-0" style="border-radius: 0 0 .75rem .75rem;"><code><span class="var">@extends</span>(<span class="str">'layouts.clinical'</span>)
<span class="var">@section</span>(<span class="str">'title'</span>, <span class="var">$pageTitle</span> ?? <span class="str">'{{ $viewTemplate->name }}'</span>)

<span class="var">@push</span>(<span class="str">'styles'</span>)
<span class="kw">&lt;style&gt;</span>
<span class="cmt">    /* Add page-specific CSS here */</span>
<span class="kw">&lt;/style&gt;</span>
<span class="var">@endpush</span>

<span class="var">@section</span>(<span class="str">'content'</span>)

<span class="cmt">{{-- Page header --}}</span>
&lt;div class="mb-4"&gt;
    &lt;h4 class="fw-bold mb-1"&gt;{{ "<span class='var'>{{ $pageTitle }}</span>" }}&lt;/h4&gt;
    &lt;p class="text-muted small mb-0"&gt;
        {{ "<span class='var'>{{ $unit->name }}</span>" }} &amp;middot; {{ "<span class='var'>{{ $unit->institution->name ?? '' }}</span>" }}
    &lt;/p&gt;
&lt;/div&gt;

<span class="cmt">{{-- Example card --}}</span>
&lt;div class="card border-0 shadow-sm"&gt;
    &lt;div class="card-body"&gt;
        &lt;p class="text-muted"&gt;Your content goes here.&lt;/p&gt;

        <span class="cmt">{{-- Terminology autocomplete example --}}</span>
        &lt;label class="form-label"&gt;Diagnosis&lt;/label&gt;
        &lt;input type="text"
               class="form-control terminology-search"
               data-category="diagnosis"
               name="diagnosis"
               placeholder="Start typing a diagnosis..."&gt;
    &lt;/div&gt;
&lt;/div&gt;

<span class="var">@endsection</span>

<span class="var">@push</span>(<span class="str">'scripts'</span>)
<span class="kw">&lt;script&gt;</span>
<span class="cmt">    // Add page-specific JavaScript here</span>
<span class="kw">&lt;/script&gt;</span>
<span class="var">@endpush</span></code></pre>
    </div>
</div>

{{-- ── Terminology Reference ──────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-tags me-2 text-primary"></i>Terminology Box — API Reference</h6>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            The terminology search endpoint is available to all authenticated users.
            Use it to power autocomplete inputs anywhere in the application.
        </p>
        <table class="table table-sm table-bordered mb-3">
            <thead class="table-light">
                <tr>
                    <th>Parameter</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody class="small">
                <tr>
                    <td><code>q</code></td>
                    <td>Yes</td>
                    <td>Search string (min 1 char)</td>
                </tr>
                <tr>
                    <td><code>category</code></td>
                    <td>No</td>
                    <td>Filter by category (e.g. <code>diagnosis</code>, <code>symptom</code>). Omit for all categories.</td>
                </tr>
            </tbody>
        </table>
        <p class="text-muted small mb-2">Example response:</p>
        <pre><code>GET /terminology/search?q=hyper&category=diagnosis

[
  { "id": 12, "term": "Hypertension",          "category": "diagnosis" },
  { "id": 47, "term": "Hyperthyroidism",        "category": "diagnosis" },
  { "id": 83, "term": "Hyperglycaemia",         "category": "diagnosis" }
]</code></pre>
        <p class="text-muted small mb-0">
            Manage terminology entries in <strong>Admin → Terminology</strong>.
            New categories appear automatically once any term uses them.
        </p>
    </div>
</div>

{{-- ── Relevant DB Info ───────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-database me-2 text-primary"></i>Context: How This Page Was Loaded</h6>
    </div>
    <div class="card-body">
        <div class="row g-3 small text-muted">
            <div class="col-md-6">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><td class="fw-semibold text-dark" style="width:40%">UnitView ID</td><td>{{ $unitView->id }}</td></tr>
                        <tr><td class="fw-semibold text-dark">UnitView Name</td><td>{{ $unitView->name }}</td></tr>
                        <tr><td class="fw-semibold text-dark">Unit</td><td>{{ $unit->name }} (ID {{ $unit->id }})</td></tr>
                        <tr><td class="fw-semibold text-dark">Institution</td><td>{{ $unit->institution->name ?? '—' }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><td class="fw-semibold text-dark" style="width:40%">ViewTemplate ID</td><td>{{ $viewTemplate->id }}</td></tr>
                        <tr><td class="fw-semibold text-dark">ViewTemplate Code</td><td><code>{{ $viewTemplate->code }}</code></td></tr>
                        <tr><td class="fw-semibold text-dark">Blade Path</td><td><code>{{ $viewTemplate->blade_path }}</code></td></tr>
                        <tr><td class="fw-semibold text-dark">File Expected</td><td><code class="text-success">resources/views/{{ str_replace('.', '/', $viewTemplate->blade_path) }}.blade.php</code></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
