@php
    $holders = $assignments->where('position_id', $position->id);
    $children = $positions->where('parent_id', $position->id);
@endphp
<li class="org-node">
    <div class="space-y-3">
        @foreach($holders as $holder)
            <article class="org-card">
                @if($holder->person->photo_url)
                    <img src="{{ $holder->person->photo_url }}" alt="Foto {{ $holder->person->name }}" class="org-photo">
                @else
                    <div class="org-photo org-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($holder->person->name, 0, 1)) }}</div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="org-role">{{ $holder->position->name }}</p>
                    <h2 class="truncate text-lg font-black text-slate-900">{{ $holder->person->name }}</h2>
                    @if($holder->division)<p class="text-sm font-semibold text-emerald-700">Bidang {{ $holder->division->name }}</p>@endif
                </div>
            </article>
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
