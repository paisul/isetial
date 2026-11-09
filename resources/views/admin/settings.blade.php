@extends('layouts.app')
@section('title','Pengaturan Pembayaran')
@section('content')
<div class="mx-auto max-w-xl px-4 py-14">
    <a href="{{route('admin.dashboard')}}" class="text-sm font-bold text-emerald-700">← Dashboard</a>
    <h1 class="mt-3 text-4xl font-black">Pengaturan Pembayaran</h1>
    <p class="mt-2 text-slate-500">Cukup masukkan nomor PromptPay. QR dan nominal pembayaran dibuat otomatis saat checkout.</p>
    <form method="post" action="{{route('admin.settings.update')}}" class="mt-8 rounded-2xl bg-white p-7 shadow">@csrf
        <label class="block font-bold">Nomor PromptPay<input name="promptpay_number" value="{{old('promptpay_number',$promptPayNumber)}}" required maxlength="30" inputmode="tel" placeholder="092-671-1380" class="mt-2 w-full rounded-xl border border-slate-300 p-3 font-normal"><span class="mt-1 block text-xs font-normal text-slate-500">Gunakan nomor HP Thailand atau ID PromptPay 13 digit.</span></label>
        <div class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm leading-6 text-emerald-900"><b>QR otomatis</b><br>QR akan berisi nomor ini, total pembayaran, dan kode unik pesanan.</div>
        <button class="mt-6 w-full rounded-xl bg-emerald-800 p-3 font-black text-white">Simpan Nomor PromptPay</button>
    </form>
</div>
@endsection
