@foreach($institutions as $institution)
<div class="d-flex align-items-center border-bottom py-2 gap-2"
     style="padding-left: {{ 0.5 + ($depth * 1.5) }}rem;">
    <span class="text-muted" style="width:16px;">
        @if($institution->allChildren->isNotEmpty())
            <i class="bi bi-chevron-down" style="font-size:.7rem;"></i>
        @else
            <i class="bi bi-dot opacity-50"></i>
        @endif
    </span>
    <span class="flex-grow-1 fw-medium d-flex align-items-center gap-2">
        {{ $institution->name }}
        @if($institution->code)
            <span class="badge font-monospace fw-bold px-2 py-1"
                  style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:.65rem;letter-spacing:.06em;">
                {{ strtoupper($institution->code) }}
            </span>
        @else
            <span class="badge px-2 py-1"
                  style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;font-size:.65rem;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>No Code
            </span>
        @endif
    </span>
    <div class="d-flex gap-1 flex-shrink-0">
        <button class="btn btn-outline-success btn-sm py-0 px-2"
                data-bs-toggle="modal" data-bs-target="#addModal"
                data-parent-id="{{ $institution->id }}"
                data-parent-label="{{ $institution->name }}"
                title="Add child institution">
            <i class="bi bi-plus-lg" style="font-size:.75rem;"></i>
        </button>
        <button class="btn btn-outline-secondary btn-sm py-0 px-2"
                data-bs-toggle="modal" data-bs-target="#editModal"
                data-id="{{ $institution->id }}"
                data-name="{{ $institution->name }}"
                data-code="{{ $institution->code }}"
                data-email="{{ $institution->email }}"
                data-phone="{{ $institution->phone }}"
                data-address="{{ $institution->address }}"
                data-logo-url="{{ $institution->logoUrl() }}"
                title="Edit">
            <i class="bi bi-pencil" style="font-size:.75rem;"></i>
        </button>
        <form action="{{ route('admin.hierarchy.destroy', $institution) }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button type="button"
                    class="btn btn-outline-danger btn-sm py-0 px-2"
                    title="Delete"
                    data-confirm-body="Delete &quot;{{ $institution->name }}&quot;? All child institutions will also be deleted."
                    onclick="confirmDialog({title:'Delete Institution', body:this.dataset.confirmBody, confirmText:'Delete', confirmClass:'btn-danger', icon:'bi-trash3-fill text-danger'}, () => this.closest('form').submit())">
                <i class="bi bi-trash3" style="font-size:.75rem;"></i>
            </button>
        </form>
    </div>
</div>
@if($institution->allChildren->isNotEmpty())
    @include('admin.hierarchy._tree', [
        'institutions' => $institution->allChildren,
        'depth'        => $depth + 1,
    ])
@endif
@endforeach
