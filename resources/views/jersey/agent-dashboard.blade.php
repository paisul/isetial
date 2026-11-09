@extends('layouts.app')
@section('title','Dashboard Petugas Jersey')
@section('content')
@php
    $productionLabels = ['queued'=>'Antrean','processing'=>'Diproses','ready'=>'Siap diambil','delivered'=>'Diserahkan','cancelled'=>'Dibatalkan'];
    $productionColors = ['queued'=>'bg-slate-100 text-slate-700','processing'=>'bg-blue-100 text-blue-800','ready'=>'bg-emerald-100 text-emerald-800','delivered'=>'bg-violet-100 text-violet-800','cancelled'=>'bg-red-100 text-red-700'];
    $paymentLabels = ['pending'=>'Menunggu verifikasi','verified'=>'Pembayaran diterima','rejected'=>'Bukti ditolak'];
@endphp
<div class="mx-auto max-w-7xl px-4 py-8 md:py-12">
    <header class="flex flex-wrap items-start justify-between gap-4"><div><p class="text-xs font-black uppercase tracking-[.18em] text-emerald-700">Dashboard petugas jersey</p><h1 class="mt-1 text-3xl font-black md:text-4xl">{{$agent->name}}</h1><p class="mt-2 text-slate-500">Hanya menampilkan pesanan yang masuk melalui link Anda.</p></div><a href="{{route('jersey.agent.entry',$agent->token)}}" class="rounded-xl bg-emerald-800 px-4 py-3 text-sm font-black text-white">Buka halaman pemesanan</a></header>

    <section class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-5">
        <div class="rounded-2xl border bg-white p-4 shadow-sm"><p class="text-xs font-bold uppercase text-slate-500">Semua pesanan</p><p class="mt-2 text-3xl font-black">{{$summary['orders']}}</p></div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm"><p class="text-xs font-bold uppercase text-emerald-700">Dana diterima</p><p class="mt-2 text-xl font-black text-emerald-950">฿{{number_format($summary['funds'],2,'.',',')}}</p></div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm"><p class="text-xs font-bold uppercase text-blue-700">Lelaki panjang</p><p class="mt-2 text-3xl font-black">{{$summary['maleLong']}}</p></div>
        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 shadow-sm"><p class="text-xs font-bold uppercase text-sky-700">Lelaki pendek</p><p class="mt-2 text-3xl font-black">{{$summary['maleShort']}}</p></div>
        <div class="col-span-2 rounded-2xl border border-violet-200 bg-violet-50 p-4 shadow-sm lg:col-span-1"><p class="text-xs font-bold uppercase text-violet-700">Muslimah</p><p class="mt-2 text-3xl font-black">{{$summary['muslimah']}}</p></div>
    </section>

    <form method="get" class="mt-6 grid gap-3 rounded-2xl border bg-white p-4 shadow-sm md:grid-cols-[1fr_220px_auto]"><input type="search" name="q" value="{{$filters['q'] ?? ''}}" placeholder="Cari nomor, nama, atau HP…" class="h-11 rounded-xl border px-3"><select name="production" class="h-11 rounded-xl border bg-white px-3"><option value="">Semua produksi</option>@foreach($productionLabels as $value=>$label)<option value="{{$value}}" @selected(($filters['production'] ?? '')===$value)>{{$label}}</option>@endforeach</select><button class="h-11 rounded-xl bg-slate-900 px-5 font-black text-white">Terapkan</button></form>

    <div class="mt-5 space-y-4">@forelse($orders as $order)
        @php($payment=$order->payments->sortByDesc('created_at')->first())
        <article class="overflow-hidden rounded-2xl border bg-white shadow-sm"><div class="p-5 md:p-6"><div class="flex flex-wrap items-start justify-between gap-4"><div><div class="flex flex-wrap items-center gap-2"><h2 class="text-lg font-black">{{$order->order_number}}</h2><span class="rounded-full px-2.5 py-1 text-xs font-black {{$productionColors[$order->production_status]}}">{{$productionLabels[$order->production_status]}}</span>@if($payment)<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-black text-amber-800">{{$paymentLabels[$payment->status]}}</span>@endif</div><p class="mt-2 font-bold">{{$order->customer_name}}</p><p class="mt-1 text-sm text-slate-500">{{$order->phone}} · {{$order->created_at->translatedFormat('d M Y, H:i')}}</p></div><div class="text-right"><p class="text-xs font-bold uppercase text-slate-400">Total</p><p class="text-xl font-black text-emerald-950">฿{{number_format($order->payable_total,2,'.',',')}}</p></div></div>
        <details class="mt-5 rounded-xl bg-slate-50 p-4"><summary class="cursor-pointer font-black">Rincian pesanan ({{$order->items->sum('quantity')}} jersey)</summary><div class="mt-3 divide-y">@foreach($order->items as $item)<div class="flex justify-between gap-3 py-2 text-sm"><span>{{$item->product->name}} · {{$item->model}} · {{$item->sleeve==='short'?'Lengan pendek':'Lengan panjang'}} · Ukuran {{$item->size->name}}</span><b>× {{$item->quantity}}</b></div>@endforeach<div class="pt-3 text-sm text-slate-600"><b>Alamat:</b> {{$order->address}}</div></div></details>
        @if($payment)<div class="mt-4 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 p-3 text-sm"><b>฿{{number_format($payment->amount,2,'.',',')}}</b><span>{{$paymentLabels[$payment->status]}}</span><a href="{{route('jersey.agent.proof',[$agent->management_token,$payment])}}" target="_blank" rel="noopener" class="ml-auto font-black text-emerald-800 underline">Lihat bukti ↗</a></div>@endif
        </div></article>
    @empty<div class="rounded-2xl border border-dashed bg-white p-12 text-center"><h2 class="text-xl font-black">Pesanan tidak ditemukan</h2><p class="mt-1 text-slate-500">Belum ada pesanan melalui link Anda atau filter tidak cocok.</p></div>@endforelse</div>
    <div class="mt-6">{{$orders->links()}}</div>
</div>
@endsection
