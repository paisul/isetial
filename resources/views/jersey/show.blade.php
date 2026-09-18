@extends('layouts.app')
@section('title','Pesanan '.$order->order_number)
@section('content')
<div class="mx-auto max-w-4xl px-4 py-14">
 <h1 class="text-4xl font-black">{{$order->order_number}}</h1>
 <p class="mt-2">{{$order->customer_name}} · Status produksi: <b>{{$order->production_status}}</b></p>
 @foreach($order->items as $item)<p class="mt-2 text-slate-600">{{$item->product->name}} · ukuran {{$item->size->name}} · {{$item->model}} · lengan {{$item->sleeve}} · {{$item->quantity}} pcs</p>@endforeach
 @if($qr=\App\Models\Setting::valueOf('payment_qr'))<div class="mt-6 rounded-xl bg-white p-5"><p class="font-bold">QR Pembayaran</p><img src="{{asset('storage/'.$qr)}}" alt="QR pembayaran" class="mt-3 h-48 w-48 object-contain"></div>@endif
 <div class="mt-7 grid gap-4 md:grid-cols-3"><div class="rounded-xl bg-white p-5 shadow"><small>Total</small><p class="text-2xl font-bold">Rp {{number_format($order->total,0,',','.')}}</p></div><div class="rounded-xl bg-white p-5 shadow"><small>Sudah dibayar</small><p class="text-2xl font-bold">Rp {{number_format($order->paid_amount,0,',','.')}}</p></div><div class="rounded-xl bg-emerald-900 p-5 text-white"><small>Sisa</small><p class="text-2xl font-bold">Rp {{number_format($order->balance,0,',','.')}}</p><b>{{$order->payment_status}}</b></div></div>
 <h2 class="mt-10 text-2xl font-black">Riwayat Pembayaran</h2>
 @forelse($order->payments as $p)<div class="mt-3 rounded-lg bg-white p-4">Rp {{number_format($p->amount,0,',','.')}} · <b>{{$p->status}}</b> · {{$p->created_at->format('d/m/Y')}}</div>@empty<p class="mt-3">Belum ada pembayaran.</p>@endforelse
 @if($order->balance>0)<form method="post" enctype="multipart/form-data" action="{{route('jersey.payment',$order->order_number)}}" class="mt-10 rounded-xl bg-white p-6 shadow">@csrf<h2 class="text-xl font-black">Upload Bukti Pembayaran</h2><div class="mt-4 grid gap-4 md:grid-cols-2"><input type="number" name="amount" min="1000" max="{{$order->balance}}" placeholder="Jumlah pembayaran" required class="rounded-lg border p-3"><input type="file" name="proof" accept="image/*" required class="rounded-lg border p-3"></div><button class="mt-4 rounded-lg bg-emerald-800 px-5 py-3 font-bold text-white">Kirim Bukti</button></form>@endif
</div>
@endsection
