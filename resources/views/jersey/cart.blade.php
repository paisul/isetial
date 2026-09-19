@extends('layouts.app')
@section('title','Keranjang Jersey')
@section('content')
<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><p class="text-xs font-black uppercase tracking-[.16em] text-emerald-700">Keranjang</p><h1 class="mt-1 text-3xl font-black md:text-4xl">Jersey pilihan Anda</h1><p class="mt-2 text-slate-500">Ubah jumlah atau hapus item sebelum melanjutkan.</p></div><a href="{{route('jersey.create')}}" class="rounded-xl border border-emerald-700 px-4 py-3 text-sm font-black text-emerald-800 hover:bg-emerald-50">+ Tambah jersey lain</a></div>
    @if($items->isEmpty())
        <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm"><div class="text-5xl">🛒</div><h2 class="mt-4 text-xl font-black">Keranjang masih kosong</h2><p class="mt-2 text-slate-500">Pilih model jersey terlebih dahulu untuk mulai memesan.</p><a href="{{route('jersey.create')}}" class="mt-6 inline-block rounded-xl bg-emerald-800 px-6 py-3 font-black text-white">Pilih jersey</a></div>
    @else
        <div class="mt-8 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-4">
                @foreach($items as $entry)
                @php($image=match($entry['item']['model']){'Lelaki Panjang'=>'model-pria-panjang-v3.jpg','Muslimah'=>'model-muslimah-v3.jpg',default=>'model-pria-pendek-v2.jpg'})
                <article class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:p-5"><div class="flex gap-4"><img src="{{asset('images/jersey/'.$image)}}" alt="{{$entry['item']['model']}}" class="h-24 w-24 shrink-0 rounded-2xl object-cover md:h-32 md:w-32"><div class="min-w-0 flex-1"><div class="flex items-start justify-between gap-3"><div><h2 class="font-black md:text-lg">{{$entry['item']['model']}}</h2><p class="mt-1 text-sm text-slate-500">{{$entry['product']->name}} · Ukuran {{$entry['size']->name}}</p><p class="mt-2 font-black text-emerald-900">฿{{number_format($entry['unit'],0,'.',',')}}</p></div><form method="post" action="{{route('jersey.cart.remove',$entry['line'])}}" onsubmit="return confirm('Hapus jersey ini dari keranjang?')">@csrf @method('DELETE')<button class="rounded-lg px-3 py-2 text-sm font-bold text-red-600 hover:bg-red-50">Hapus</button></form></div><form method="post" action="{{route('jersey.cart.update',$entry['line'])}}" data-quantity-form class="mt-4 flex items-center gap-3">@csrf @method('PATCH')<span class="text-sm font-bold">Jumlah</span><div class="flex overflow-hidden rounded-xl border border-slate-300 bg-white"><button type="button" data-quantity-minus class="h-10 w-10 text-xl hover:bg-slate-50" aria-label="Kurangi jumlah">−</button><input type="number" name="quantity" min="1" max="20" value="{{$entry['item']['quantity']}}" class="h-10 w-12 border-x border-slate-200 text-center font-black [appearance:textfield]" aria-label="Jumlah jersey"><button type="button" data-quantity-plus class="h-10 w-10 text-xl hover:bg-slate-50" aria-label="Tambah jumlah">+</button></div><span data-saving class="hidden text-xs font-bold text-emerald-700">Menyimpan…</span></form></div></div></article>
                @endforeach
            </div>
            <aside class="sticky top-24 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg"><div class="p-5"><p class="text-xs font-black uppercase tracking-[.14em] text-emerald-700">Ringkasan belanja</p><div class="mt-5 space-y-3 text-sm"><div class="flex justify-between"><span>Total item</span><b>{{$items->sum(fn($e)=>$e['item']['quantity'])}} jersey</b></div><div class="flex justify-between border-t pt-4 text-lg"><span class="font-bold">Total</span><b class="text-emerald-900">฿{{number_format($items->sum(fn($e)=>$e['unit']*$e['item']['quantity']),0,'.',',')}}</b></div></div><a href="{{route('jersey.checkout')}}" class="mt-6 block rounded-xl bg-amber-400 px-5 py-3.5 text-center font-black text-emerald-950 hover:bg-amber-300">Checkout sekarang →</a><p class="mt-3 text-center text-xs text-slate-400">Pembayaran dilakukan setelah pesanan dibuat.</p></div></aside>
        </div>
    @endif
</div>
@if($items->isNotEmpty())
<script>
document.querySelectorAll('[data-quantity-form]').forEach(form=>{const input=form.querySelector('input[name="quantity"]'),save=()=>{input.value=Math.max(1,Math.min(20,Number(input.value)||1));form.querySelector('[data-saving]').classList.remove('hidden');form.requestSubmit()};form.querySelector('[data-quantity-minus]').onclick=()=>{input.value=Math.max(1,Number(input.value)-1);save()};form.querySelector('[data-quantity-plus]').onclick=()=>{input.value=Math.min(20,Number(input.value)+1);save()};input.onchange=save});
</script>
@endif
@endsection
