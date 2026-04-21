@extends('layouts.admin')
@section('title', 'Terminology Management')

@push('styles')
<style>
    .term-card { display: flex; flex-direction: column; height: 100%; }
    .term-card .card-header { padding: .55rem .75rem; background: #f8f9fa; border-bottom: 1px solid #dee2e6; }
    .term-card .card-header .category-title { font-size: .8rem; font-weight: 600; color: #374151; white-space: nowrap; }
    .term-list .term-row { display: flex; align-items: center; justify-content: space-between; padding: .3rem .75rem; border-bottom: 1px solid #f1f3f5; font-size: .85rem; }
    .term-list .term-row:last-child { border-bottom: none; }
    .term-list .term-row:hover { background: #f8f9fa; }
    .term-list .term-text { flex: 1; word-break: break-word; }
    .empty-terms { color: #9ca3af; font-size: .8rem; padding: .6rem .75rem; font-style: italic; }
    .filter-empty-msg { display: none; color: #6b7280; font-size: .8rem; padding: .6rem .75rem; font-style: italic; }
    .term-pagination { display: none; align-items: center; justify-content: space-between; padding: .35rem .75rem; border-top: 1px solid #e9ecef; background: #f8f9fa; font-size: .78rem; color: #6b7280; }
    .term-pagination .page-btns { display: flex; gap: .25rem; }
    .duplicate-warning { font-size: .72rem; color: #dc3545; margin-top: .15rem; display: none; }
    .term-row.term-highlight { background: #fff8e1; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-journal-medical me-2 text-primary"></i>Terminology Management
    </h4>
    <span class="text-muted small">{{ $terms->flatten()->count() }} terms across {{ count($categories) }} categories</span>
</div>

<div class="row row-cols-1 row-cols-lg-2 g-3">
    @foreach($categories as $key => $label)
    <div class="col">
        <div class="card border shadow-sm term-card">

            {{-- Card header: category name + add form --}}
            <div class="card-header">
                <form class="term-add-form" data-category="{{ $key }}" data-url="{{ route('admin.terminology.store') }}" data-delete-url-base="{{ url('admin/terminology') }}">
                    @csrf
                    <div class="d-flex align-items-center gap-2">
                        <span class="category-title">
                            <i class="bi bi-tag-fill text-primary me-1" style="font-size:.7rem;"></i>{{ $label }}
                        </span>
                        <div class="ms-auto" style="max-width:280px;">
                            <div class="input-group input-group-sm">
                                <input type="text"
                                       name="term"
                                       class="form-control term-input"
                                       placeholder="Add new term…"
                                       autocomplete="off"
                                       required>
                                <button type="submit" class="btn btn-primary btn-sm px-3 term-submit">
                                    <i class="bi bi-plus-lg"></i> Save
                                </button>
                            </div>
                            <div class="duplicate-warning">
                                <i class="bi bi-exclamation-circle-fill me-1"></i><span class="dup-msg"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Term list --}}
            <div class="term-list">
                @forelse($terms->get($key, collect()) as $term)
                <div class="term-row">
                    <span class="term-text">{{ $term->term }}</span>
                    <form action="{{ route('admin.terminology.destroy', $term) }}" method="POST" class="ms-2 flex-shrink-0">
                        @csrf @method('DELETE')
                        <button type="button"
                                class="btn btn-outline-danger btn-sm py-0 px-1"
                                title="Delete"
                                data-term="{{ $term->term }}"
                                onclick="confirmDialog({
                                    title: 'Delete Term',
                                    body: 'Delete &quot;' + this.dataset.term + '&quot;?',
                                    confirmText: 'Delete',
                                    confirmClass: 'btn-danger',
                                    icon: 'bi-trash3-fill text-danger'
                                }, () => this.closest('form').submit())">
                            <i class="bi bi-trash3" style="font-size:.75rem;"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="empty-terms">No terms yet — add one above.</div>
                @endforelse
                <div class="filter-empty-msg">No matching terms found.</div>
            </div>

            {{-- Pagination inserted by JS --}}
            <div class="term-pagination">
                <span class="page-info"></span>
                <div class="page-btns">
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2 btn-prev" title="Previous">
                        <i class="bi bi-chevron-left" style="font-size:.7rem;"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2 btn-next" title="Next">
                        <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
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
        let filtered   = allRows.slice();
        let currentPage = 1;

        function totalPages() {
            return Math.max(1, Math.ceil(filtered.length / PER_PAGE));
        }

        function render() {
            allRows = Array.from(termList.querySelectorAll('.term-row'));
            if (filtered.length === 0 && allRows.length === 0) {
                filterEmpty.style.display = 'none';
                const empty = termList.querySelector('.empty-terms');
                if (empty) empty.style.display = 'block';
                pagination.style.display  = 'none';
                return;
            }

            const emptyEl = termList.querySelector('.empty-terms');
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
            const end   = Math.min(start + PER_PAGE, filtered.length);
            filtered.slice(start, end).forEach(r => r.style.display = 'flex');

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
            if (lower === '') {
                filtered = allRows.slice();
            } else {
                filtered = allRows.filter(r =>
                    r.querySelector('.term-text').textContent.trim().toLowerCase().includes(lower)
                );
            }
        }

        btnPrev.addEventListener('click', function () {
            if (currentPage > 1) { currentPage--; render(); }
        });

        btnNext.addEventListener('click', function () {
            if (currentPage < totalPages()) { currentPage++; render(); }
        });

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

            if (query !== '' && exactMatch) {
                dupMsg.textContent        = '"' + query + '" already exists in this category.';
                dupWarning.style.display  = 'block';
                submitBtn.disabled        = true;
            } else {
                dupWarning.style.display  = 'none';
                submitBtn.disabled        = false;
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
                body: body,
            })
            .then(r => r.json())
            .then(function (data) {
                if (data.id) {
                    // Build new row
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

                    // Insert in sorted position
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
                    if (!inserted) {
                        // Append before filter-empty-msg
                        termList.insertBefore(row, filterEmpty);
                    }

                    input.value = '';
                    dupWarning.style.display = 'none';
                    refreshFiltered();
                    // Go to last page to show the newly added term
                    currentPage = totalPages();
                    render();
                }
                submitBtn.disabled = false;
            })
            .catch(function () {
                submitBtn.disabled = false;
            });
        });

        // Initial render
        if (allRows.length > 0) render();
    }

    document.querySelectorAll('.term-card').forEach(initCard);
});
</script>
@endpush
