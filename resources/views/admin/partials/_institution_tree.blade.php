@foreach($institutions as $institution)
    <a href="{{ $baseRoute }}?{{ $paramName }}={{ $institution->id }}"
       class="list-group-item list-group-item-action d-flex align-items-center gap-2
              {{ ($selectedId ?? null) == $institution->id ? 'active' : '' }}"
       style="padding-left: {{ 0.75 + ($depth * 1.25) }}rem; font-size: .875rem;">
        @if($institution->allChildren->isNotEmpty())
            <i class="bi bi-chevron-right opacity-50" style="font-size:.7rem;"></i>
        @else
            <i class="bi bi-dash opacity-25" style="font-size:.7rem;"></i>
        @endif
        {{ $institution->name }}
    </a>
    @if($institution->allChildren->isNotEmpty())
        @include('admin.partials._institution_tree', [
            'institutions' => $institution->allChildren,
            'depth'        => $depth + 1,
            'selectedId'   => $selectedId ?? null,
            'paramName'    => $paramName,
            'baseRoute'    => $baseRoute,
        ])
    @endif
@endforeach
