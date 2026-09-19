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
            @php $mobileFieldLabelShown = false; @endphp
            @foreach($children as $child)
                @php
                    $isField = $assignments->where('position_id', $child->id)->whereNotNull('division_id')->isNotEmpty();
                @endphp
                @if(!$position->parent_id && $isField && !$mobileFieldLabelShown)
                    <li class="mobile-fields-label"><span>Bidang-Bidang</span></li>
                    @php $mobileFieldLabelShown = true; @endphp
                @endif
                @include('public.partials.structure-node', ['position' => $child, 'positions' => $positions, 'assignments' => $assignments])
            @endforeach
        </ul>
    @endif
</li>
