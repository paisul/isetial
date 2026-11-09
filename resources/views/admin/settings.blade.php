@extends('layouts.app')
@section('title','Pengaturan Pembayaran')
@section('content')
<div class="mx-auto max-w-xl px-4 py-14">
    <a href="{{route('admin.dashboard')}}" class="text-sm font-bold text-emerald-700">← Dashboard</a>
    <h1 class="mt-3 text-4xl font-black">Pengaturan Pembayaran</h1>
    <p class="mt-2 text-slate-500">Nomor dan QR ini ditampilkan kepada petugas saat checkout jersey.</p>
    <form method="post" enctype="multipart/form-data" action="{{route('admin.settings.update')}}" class="mt-8 rounded-2xl bg-white p-7 shadow">@csrf
        <label class="block font-bold">Nomor PromptPay<input name="promptpay_number" value="{{old('promptpay_number',$promptPayNumber)}}" required maxlength="30" inputmode="tel" placeholder="092-671-1380" class="mt-2 w-full rounded-xl border border-slate-300 p-3 font-normal"><span class="mt-1 block text-xs font-normal text-slate-500">Boleh menggunakan angka, spasi, tanda +, kurung, atau tanda hubung.</span></label>
        <label class="mt-6 block font-bold">QR PromptPay<input type="file" name="payment_qr" accept="image/*" class="mt-2 w-full rounded-xl border border-slate-300 p-3 font-normal"></label>
        @if($qr)<div class="mt-5 rounded-2xl bg-slate-50 p-4 text-center"><img src="{{asset('storage/'.$qr)}}" alt="QR PromptPay saat ini" class="mx-auto h-48 w-48 object-contain"><p class="mt-2 text-xs font-bold text-slate-500">QR yang sedang digunakan</p></div>@else<div class="mt-5 rounded-xl bg-amber-50 p-3 text-sm text-amber-900">Belum ada QR khusus. Sistem menggunakan QR bawaan.</div>@endif
        <button class="mt-6 w-full rounded-xl bg-emerald-800 p-3 font-black text-white">Simpan Pengaturan Pembayaran</button>
    </form>
</div>
@endsection
