<div class="card border shadow-sm term-card">

    {{-- Card header --}}
    <div class="card-header">
        <form class="term-add-form"
              data-category="{{ $cat->slug }}"
              data-url="{{ route('admin.terminology.store') }}"
              data-delete-url-base="{{ url('admin/terminology') }}">
            @csrf
            <div class="d-flex align-items-center gap-2 flex-wrap">

                {{-- Title + slug --}}
                <div class="d-flex align-items-center gap-1 flex-wrap" style="min-width:0">
                    <i class="bi bi-tag-fill text-primary" style="font-size:.7rem"></i>
                    <span style="font-size:.8rem;font-weight:600;color:#374151;white-space:nowrap">{{ $cat->name }}</span>
                    @if(!$cat->is_system)
                        <span class="badge bg-warning text-dark" style="font-size:.6rem">Custom</span>
                    @endif
                    <span class="slug-badge ms-1"
                          onclick="copySlug(this)"
                          title="Click to copy slug — use as data-category in HTML">{{ $cat->slug }}</span>
                    <span class="text-muted" style="font-size:.68rem">({{ $cat->terms_count }})</span>
                </div>

                {{-- Add term input --}}
                <div class="ms-auto" style="max-width:260px;min-width:160px">
                    <div class="input-group input-group-sm">
                        <input type="text" name="term"
                               class="form-control term-input"
                               placeholder="Add new term…"
                               autocomplete="off" required>
                        <button type="submit" class="btn btn-primary btn-sm px-2 term-submit">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <div class="duplicate-warning">
                        <i class="bi bi-exclamation-circle-fill me-1"></i><span class="dup-msg"></span>
                    </div>
                </div>

                {{-- Delete button (custom only) --}}
                @if(!$cat->is_system)
                <form method="POST"
                      action="{{ route('admin.terminology.categories.destroy', $cat) }}"
                      class="flex-shrink-0">
                    @csrf @method('DELETE')
                    <button type="button"
                            class="btn btn-sm btn-outline-danger py-0 px-1"
                            title="{{ $cat->terms_count > 0 ? 'Delete all terms first' : 'Delete this box' }}"
                            @if($cat->terms_count > 0) disabled @endif
                            onclick="confirmDialog({
                                title: 'Delete Terminology Box',
                                body: 'Delete the &quot;{{ addslashes($cat->name) }}&quot; box? This cannot be undone.',
                                confirmText: 'Delete',
                                confirmClass: 'btn-danger',
                                icon: 'bi-trash3-fill text-danger'
                            }, () => this.closest('form').submit())">
                        <i class="bi bi-trash3" style="font-size:.7rem"></i>
                    </button>
                </form>
                @endif

            </div>
        </form>
        @if($cat->description)
        <div class="text-muted mt-1" style="font-size:.72rem;padding-left:1.1rem">{{ $cat->description }}</div>
        @endif
    </div>

    {{-- Term list --}}
    <div class="term-list">
        @forelse($terms->get($cat->slug, collect()) as $term)
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
                    <i class="bi bi-trash3" style="font-size:.75rem"></i>
                </button>
            </form>
        </div>
        @empty
        <div class="empty-terms">No terms yet — add one above.</div>
        @endforelse
        <div class="filter-empty-msg">No matching terms found.</div>
    </div>

    {{-- Pagination --}}
    <div class="term-pagination">
        <span class="page-info"></span>
        <div class="page-btns">
            <button class="btn btn-sm btn-outline-secondary py-0 px-2 btn-prev" title="Previous">
                <i class="bi bi-chevron-left" style="font-size:.7rem"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary py-0 px-2 btn-next" title="Next">
                <i class="bi bi-chevron-right" style="font-size:.7rem"></i>
            </button>
        </div>
    </div>

</div>
