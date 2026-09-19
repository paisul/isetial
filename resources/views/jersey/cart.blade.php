@extends('layouts.app')
@section('title','Keranjang Jersey')
@section('content')
<div class="mx-auto max-w-6xl px-4 py-10 {{ $items->isNotEmpty() ? 'pb-32 lg:pb-10' : '' }}">
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
            <aside class="sticky top-24 hidden overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg lg:block"><div class="p-5"><p class="text-xs font-black uppercase tracking-[.14em] text-emerald-700">Ringkasan belanja</p><div class="mt-5 space-y-3 text-sm"><div class="flex justify-between"><span>Total item</span><b>{{$items->sum(fn($e)=>$e['item']['quantity'])}} jersey</b></div><div class="flex justify-between border-t pt-4 text-lg"><span class="font-bold">Total</span><b class="text-emerald-900">฿{{number_format($items->sum(fn($e)=>$e['unit']*$e['item']['quantity']),0,'.',',')}}</b></div></div><button type="button" data-open-checkout class="mt-6 w-full rounded-xl bg-amber-400 px-5 py-3.5 text-center font-black text-emerald-950 hover:bg-amber-300">Checkout sekarang →</button><p class="mt-3 text-center text-xs text-slate-400">Pembayaran dilakukan setelah pesanan dibuat.</p></div></aside>
        </div>
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 p-3 shadow-[0_-8px_24px_rgba(15,23,42,0.12)] backdrop-blur lg:hidden">
            <div class="mx-auto flex max-w-6xl items-center gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-500">Total {{$items->sum(fn($e)=>$e['item']['quantity'])}} jersey</p>
                    <p class="truncate text-lg font-black text-emerald-950">฿{{number_format($items->sum(fn($e)=>$e['unit']*$e['item']['quantity']),0,'.',',')}}</p>
                </div>
                <button type="button" data-open-checkout class="shrink-0 rounded-xl bg-amber-400 px-5 py-3.5 text-center font-black text-emerald-950 hover:bg-amber-300">Checkout sekarang →</button>
            </div>
        </div>
        <div id="checkout-sheet" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="checkout-title">
            <button type="button" data-close-checkout class="absolute inset-0 bg-slate-950/60" aria-label="Tutup formulir checkout"></button>
            <div id="checkout-card" class="absolute inset-x-0 bottom-0 max-h-[92dvh] translate-y-full overflow-y-auto rounded-t-3xl bg-white shadow-2xl transition-transform duration-300 ease-out md:left-1/2 md:right-auto md:w-full md:max-w-2xl md:-translate-x-1/2">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-5 py-4">
                    <div><p class="text-xs font-black uppercase tracking-[.14em] text-emerald-700">Checkout</p><h2 id="checkout-title" class="text-xl font-black">Data pemesan</h2></div>
                    <button type="button" data-close-checkout class="grid h-10 w-10 place-items-center rounded-full bg-slate-100 text-xl" aria-label="Tutup formulir checkout">×</button>
                </div>
                <form method="post" action="{{route('jersey.store')}}" class="p-5">@csrf
                    @foreach($items as $entry)<input type="hidden" name="cart_line_ids[]" value="{{$entry['line']}}">@endforeach
                    <p class="mb-5 text-sm text-slate-500">Lengkapi data berikut untuk membuat pesanan {{$items->sum(fn($e)=>$e['item']['quantity'])}} jersey.</p>
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="text-sm font-bold">Nama lengkap<input name="customer_name" value="{{old('customer_name')}}" required maxlength="150" autocomplete="name" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 font-normal outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"></label>
                        <label class="text-sm font-bold">Nomor HP/WhatsApp<input type="tel" name="phone" value="{{old('phone')}}" required maxlength="30" autocomplete="tel" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 font-normal outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"></label>
                        <label class="text-sm font-bold md:col-span-2">Alamat lengkap<textarea name="address" required maxlength="1000" rows="3" autocomplete="street-address" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 font-normal outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">{{old('address')}}</textarea></label>
                    </div>
                    <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-5"><div><p class="text-xs text-slate-500">Total pesanan</p><p class="text-xl font-black text-emerald-950">฿{{number_format($items->sum(fn($e)=>$e['unit']*$e['item']['quantity']),0,'.',',')}}</p></div><button class="rounded-xl bg-amber-400 px-6 py-4 font-black text-emerald-950 hover:bg-amber-300">Buat Pesanan →</button></div>
                    <p class="mt-4 text-center text-xs text-slate-400">🔒 Data hanya digunakan untuk memproses pesanan.</p>
                </form>
            </div>
        </div>
    @endif
</div>
@if($items->isNotEmpty())
<script>
document.querySelectorAll('[data-quantity-form]').forEach(form=>{const input=form.querySelector('input[name="quantity"]'),save=()=>{input.value=Math.max(1,Math.min(20,Number(input.value)||1));form.querySelector('[data-saving]').classList.remove('hidden');form.requestSubmit()};form.querySelector('[data-quantity-minus]').onclick=()=>{input.value=Math.max(1,Number(input.value)-1);save()};form.querySelector('[data-quantity-plus]').onclick=()=>{input.value=Math.min(20,Number(input.value)+1);save()};input.onchange=save});
const checkoutSheet=document.getElementById('checkout-sheet'),checkoutCard=document.getElementById('checkout-card');
const openCheckout=()=>{checkoutSheet.classList.remove('hidden');document.body.classList.add('overflow-hidden');requestAnimationFrame(()=>checkoutCard.classList.remove('translate-y-full'));setTimeout(()=>checkoutSheet.querySelector('input[name="customer_name"]').focus(),300)};
const closeCheckout=()=>{checkoutCard.classList.add('translate-y-full');document.body.classList.remove('overflow-hidden');setTimeout(()=>checkoutSheet.classList.add('hidden'),300)};
document.querySelectorAll('[data-open-checkout]').forEach(button=>button.addEventListener('click',openCheckout));
document.querySelectorAll('[data-close-checkout]').forEach(button=>button.addEventListener('click',closeCheckout));
document.addEventListener('keydown',event=>{if(event.key==='Escape'&&!checkoutSheet.classList.contains('hidden'))closeCheckout()});
@if($errors->any()) openCheckout(); @endif
</script>
@endif
@endsection
