@php
    $holders = $assignments->where('position_id', $position->id);
    $children = $positions->where('parent_id', $position->id);
@endphp
<li class="org-node">
    <div class="space-y-3">
        @foreach($holders as $holder)
            @include('public.partials.structure-card', ['holder' => $holder])
        @endforeach
    </div>
    @if($children->isNotEmpty())
        <ul class="org-level">
            @foreach($children as $child)
                @include('public.partials.structure-node', ['position' => $child, 'positions' => $positions, 'assignments' => $assignments])
            @endforeach
        </ul>
    @endif
</li>
