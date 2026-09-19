@extends('layouts.app')
@section('title', 'Struktur Pengurus')
@section('content')
<section class="overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-lime-700 px-4 py-14 text-white">
    <div class="mx-auto max-w-7xl">
        <p class="text-sm font-bold uppercase tracking-[.25em] text-amber-300">iSetial Wisdom</p>
        <h1 class="mt-2 text-4xl font-black md:text-6xl">Struktur Pengurus</h1>
        <p class="mt-3 text-lg text-emerald-100">{{ $period ? 'Periode '.$period->name : 'Susunan kepengurusan' }}</p>
    </div>
</section>

<section class="mx-auto max-w-[1500px] px-4 py-12">
    @if($period && $assignments->isNotEmpty())
        @php
            $positionIds = $positions->pluck('id');
            $roots = $positions->filter(fn ($position) => ! $position->parent_id || ! $positionIds->contains($position->parent_id));
        @endphp
        <div class="org-chart" aria-label="Bagan struktur pengurus">
            <ul class="org-level org-roots">
                @foreach($roots as $position)
                    @include('public.partials.structure-node', compact('position', 'positions', 'assignments'))
                @endforeach
            </ul>
        </div>
    @else
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
            <h2 class="text-2xl font-black">Data pengurus sedang diperbarui</h2>
            <p class="mt-2 text-slate-500">Susunan periode aktif akan tampil di halaman ini.</p>
        </div>
    @endif
</section>
@endsection
