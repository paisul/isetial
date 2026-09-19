@extends('layouts.app')
@section('title','Admin Struktur')
@section('content')
<div class="mx-auto max-w-7xl px-4 py-12">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><p class="text-sm font-bold uppercase tracking-widest text-emerald-700">Pengelolaan organisasi</p><h1 class="text-4xl font-black">Struktur & DKM</h1></div>
        <div class="flex gap-4"><a href="{{route('structure')}}" target="_blank" class="font-bold text-emerald-700">Lihat halaman publik ↗</a><a href="{{route('admin.dashboard')}}">← Dashboard</a></div>
    </div>

    <div class="mt-8 grid gap-5 lg:grid-cols-3">
        <form method="post" action="{{route('admin.periods.store')}}" class="grid gap-3 rounded-2xl bg-white p-5 shadow">@csrf
            <h2 class="text-xl font-bold">Tambah Periode</h2>
            <select name="masjid_id" class="rounded-lg border p-2">@if($canGlobal)<option value="">iSetial Wisdom</option>@endif @foreach($masjids as $m)<option value="{{$m->id}}">DKM {{$m->name}}</option>@endforeach</select>
            <input name="name" required placeholder="Contoh: 2026–2029" class="rounded-lg border p-2">
            <div class="grid grid-cols-2 gap-2"><input type="date" name="starts_at" required class="rounded-lg border p-2"><input type="date" name="ends_at" class="rounded-lg border p-2"></div>
            <select name="status" class="rounded-lg border p-2"><option value="draft">Draft</option><option value="active">Aktif</option><option value="archived">Arsip</option></select>
            <button class="rounded-lg bg-emerald-800 p-2 font-bold text-white">Tambah periode</button>
        </form>

        <form method="post" action="{{route('admin.positions.store')}}" class="grid gap-3 rounded-2xl bg-white p-5 shadow">@csrf
            <h2 class="text-xl font-bold">Tambah Jabatan</h2>
            <div class="grid grid-cols-2 gap-2"><select name="context_type" class="rounded-lg border p-2"><option value="isetial">iSetial</option><option value="dkm">DKM</option></select><select name="masjid_id" class="rounded-lg border p-2"><option value="">Pusat</option>@foreach($masjids as $m)<option value="{{$m->id}}">{{$m->name}}</option>@endforeach</select></div>
            <input name="name" required placeholder="Contoh: Setiausaha" class="rounded-lg border p-2">
            <select name="parent_id" class="rounded-lg border p-2"><option value="">Tanpa induk (tingkat teratas)</option>@foreach($positions as $p)<option value="{{$p->id}}">Di bawah: {{$p->name}} · {{$p->masjid?->name ?? 'iSetial'}}</option>@endforeach</select>
            <input type="number" name="display_order" value="0" min="0" class="rounded-lg border p-2" aria-label="Urutan">
            <button class="rounded-lg bg-emerald-800 p-2 font-bold text-white">Tambah jabatan</button>
        </form>

        <form method="post" action="{{route('admin.divisions.store')}}" class="grid gap-3 rounded-2xl bg-white p-5 shadow">@csrf
            <h2 class="text-xl font-bold">Tambah Bidang</h2>
            <select name="masjid_id" class="rounded-lg border p-2">@if($canGlobal)<option value="">iSetial Wisdom</option>@endif @foreach($masjids as $m)<option value="{{$m->id}}">{{$m->name}}</option>@endforeach</select>
            <input name="name" required placeholder="Contoh: Kegiatan" class="rounded-lg border p-2">
            <textarea name="description" placeholder="Deskripsi opsional" class="rounded-lg border p-2"></textarea>
            <button class="rounded-lg bg-emerald-800 p-2 font-bold text-white">Tambah bidang</button>
        </form>
    </div>

    <form method="post" enctype="multipart/form-data" action="{{route('admin.assignments.store')}}" class="mt-6 grid gap-3 rounded-2xl bg-slate-800 p-6 text-white md:grid-cols-3">@csrf
        <div class="md:col-span-3"><h2 class="text-xl font-bold">Tempatkan Person pada Jabatan</h2><p class="text-sm text-slate-300">Orang yang sama dapat memegang lebih dari satu jabatan.</p></div>
        <select name="person_id" required class="rounded-lg border p-2 text-slate-900"><option value="">Pilih person</option>@foreach($people as $p)<option value="{{$p->id}}">{{$p->name}}</option>@endforeach</select>
        <select name="organization_period_id" required class="rounded-lg border p-2 text-slate-900"><option value="">Pilih periode</option>@foreach($periods as $p)<option value="{{$p->id}}">{{$p->name}} · {{$p->masjid?->name ?? 'iSetial'}} · {{$p->status}}</option>@endforeach</select>
        <select name="position_id" required class="rounded-lg border p-2 text-slate-900"><option value="">Pilih jabatan</option>@foreach($positions as $p)<option value="{{$p->id}}">{{$p->name}} · {{$p->masjid?->name ?? 'iSetial'}}</option>@endforeach</select>
        <select name="division_id" class="rounded-lg border p-2 text-slate-900"><option value="">Tanpa bidang</option>@foreach($divisions as $d)<option value="{{$d->id}}">{{$d->name}} · {{$d->masjid?->name ?? 'iSetial'}}</option>@endforeach</select>
        <input type="file" name="photo" accept="image/*" class="rounded-lg border border-slate-500 p-2 text-sm" aria-label="Foto pengurus">
        <input type="number" name="display_order" value="0" min="0" class="rounded-lg border p-2 text-slate-900" aria-label="Urutan tampil">
        <input type="hidden" name="is_active" value="1">
        <button class="rounded-lg bg-amber-400 p-2 font-bold text-emerald-950 md:col-span-3">Tambahkan penempatan</button>
    </form>

    <section class="mt-10">
        <h2 class="text-2xl font-black">Kelola Periode, Jabatan & Bidang</h2>
        <div class="mt-4 grid gap-4 lg:grid-cols-3">
            <div class="space-y-3"><h3 class="font-bold text-slate-500">PERIODE</h3>@forelse($periods as $period)
                <details class="rounded-xl bg-white p-4 shadow-sm"><summary class="cursor-pointer font-bold">{{$period->name}} <span class="text-xs uppercase text-emerald-700">{{$period->status}}</span></summary>
                    <form method="post" action="{{route('admin.periods.update',$period)}}" class="mt-3 grid gap-2">@csrf @method('PUT')<input type="hidden" name="masjid_id" value="{{$period->masjid_id}}"><input name="name" value="{{$period->name}}" required class="rounded border p-2"><div class="grid grid-cols-2 gap-2"><input type="date" name="starts_at" value="{{$period->starts_at->format('Y-m-d')}}" required class="rounded border p-2"><input type="date" name="ends_at" value="{{$period->ends_at?->format('Y-m-d')}}" class="rounded border p-2"></div><select name="status" class="rounded border p-2">@foreach(['draft'=>'Draft','active'=>'Aktif','archived'=>'Arsip'] as $value=>$label)<option value="{{$value}}" @selected($period->status===$value)>{{$label}}</option>@endforeach</select><button class="rounded bg-emerald-700 p-2 text-white">Simpan</button></form>
                    <form method="post" action="{{route('admin.periods.destroy',$period)}}" class="mt-2" onsubmit="return confirm('Hapus periode kosong ini?')">@csrf @method('DELETE')<button class="text-sm text-red-700">Hapus periode</button></form>
                </details>@empty<p class="text-sm text-slate-500">Belum ada periode.</p>@endforelse</div>

            <div class="space-y-3"><h3 class="font-bold text-slate-500">JABATAN</h3>@forelse($positions as $position)
                <details class="rounded-xl bg-white p-4 shadow-sm"><summary class="cursor-pointer font-bold">{{$position->name}} <span class="text-xs text-slate-500">{{$position->parent ? '← '.$position->parent->name : 'tingkat teratas'}}</span></summary>
                    <form method="post" action="{{route('admin.positions.update',$position)}}" class="mt-3 grid gap-2">@csrf @method('PUT')<input type="hidden" name="context_type" value="{{$position->context_type}}"><input type="hidden" name="masjid_id" value="{{$position->masjid_id}}"><input name="name" value="{{$position->name}}" required class="rounded border p-2"><select name="parent_id" class="rounded border p-2"><option value="">Tanpa induk</option>@foreach($positions->where('id','!=',$position->id) as $parent)<option value="{{$parent->id}}" @selected($position->parent_id===$parent->id)>Di bawah: {{$parent->name}}</option>@endforeach</select><input type="number" name="display_order" value="{{$position->display_order}}" min="0" class="rounded border p-2"><button class="rounded bg-emerald-700 p-2 text-white">Simpan</button></form>
                    <form method="post" action="{{route('admin.positions.destroy',$position)}}" class="mt-2" onsubmit="return confirm('Hapus jabatan yang tidak digunakan ini?')">@csrf @method('DELETE')<button class="text-sm text-red-700">Hapus jabatan</button></form>
                </details>@empty<p class="text-sm text-slate-500">Belum ada jabatan.</p>@endforelse</div>

            <div class="space-y-3"><h3 class="font-bold text-slate-500">BIDANG</h3>@forelse($divisions as $division)
                <details class="rounded-xl bg-white p-4 shadow-sm"><summary class="cursor-pointer font-bold">{{$division->name}}</summary>
                    <form method="post" action="{{route('admin.divisions.update',$division)}}" class="mt-3 grid gap-2">@csrf @method('PUT')<input type="hidden" name="masjid_id" value="{{$division->masjid_id}}"><input name="name" value="{{$division->name}}" required class="rounded border p-2"><textarea name="description" class="rounded border p-2">{{$division->description}}</textarea><button class="rounded bg-emerald-700 p-2 text-white">Simpan</button></form>
                    <form method="post" action="{{route('admin.divisions.destroy',$division)}}" class="mt-2" onsubmit="return confirm('Hapus bidang yang tidak digunakan ini?')">@csrf @method('DELETE')<button class="text-sm text-red-700">Hapus bidang</button></form>
                </details>@empty<p class="text-sm text-slate-500">Belum ada bidang.</p>@endforelse</div>
        </div>
    </section>

    <section class="mt-10"><h2 class="text-2xl font-black">Susunan Pengurus</h2><div class="mt-4 grid gap-3 md:grid-cols-2">@forelse($assignments as $item)
        <details class="rounded-xl bg-white p-5 shadow"><summary class="flex cursor-pointer items-center gap-3">@if($item->person->photo_url)<img src="{{$item->person->photo_url}}" alt="" class="h-12 w-12 rounded-full object-cover">@endif<div><b>{{$item->person->name}}</b><p>{{$item->position->name}} @if($item->division) · {{$item->division->name}}@endif</p><small>{{$item->period->name}} · {{$item->masjid?->name ?? 'iSetial Wisdom'}}</small></div></summary>
            <form method="post" enctype="multipart/form-data" action="{{route('admin.assignments.update',$item)}}" class="mt-4 grid gap-2 md:grid-cols-2">@csrf @method('PUT')<select name="person_id" class="rounded border p-2">@foreach($people as $person)<option value="{{$person->id}}" @selected($person->id===$item->person_id)>{{$person->name}}</option>@endforeach</select><select name="organization_period_id" class="rounded border p-2">@foreach($periods as $period)<option value="{{$period->id}}" @selected($period->id===$item->organization_period_id)>{{$period->name}}</option>@endforeach</select><select name="position_id" class="rounded border p-2">@foreach($positions as $position)<option value="{{$position->id}}" @selected($position->id===$item->position_id)>{{$position->name}}</option>@endforeach</select><select name="division_id" class="rounded border p-2"><option value="">Tanpa bidang</option>@foreach($divisions as $division)<option value="{{$division->id}}" @selected($division->id===$item->division_id)>{{$division->name}}</option>@endforeach</select><input type="file" name="photo" accept="image/*" class="rounded border p-2 text-sm"><input type="number" name="display_order" value="{{$item->display_order}}" min="0" class="rounded border p-2"><label class="p-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Tampilkan</label><button class="rounded bg-emerald-700 p-2 text-white">Simpan penempatan</button></form>
            <form method="post" action="{{route('admin.assignments.destroy',$item)}}" class="mt-3" onsubmit="return confirm('Hapus penempatan ini?')">@csrf @method('DELETE')<button class="text-sm text-red-700">Hapus penempatan</button></form>
        </details>@empty<p class="text-slate-500">Belum ada penempatan.</p>@endforelse</div></section>
</div>
@endsection
