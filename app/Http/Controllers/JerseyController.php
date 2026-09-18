<?php

namespace App\Http\Controllers;

use App\Models\JerseyOrder;
use App\Models\JerseyOrderItem;
use App\Models\JerseyPayment;
use App\Models\JerseyProduct;
use App\Models\JerseySize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JerseyController extends Controller
{
    public function create()
    {
        return view('jersey.order', ['products' => JerseyProduct::with('sizes')->where('is_active', true)->get()]);
    }

    public function store(Request $r)
    {
        $d = $r->validate(['customer_name' => 'required|max:150', 'birth_date' => 'nullable|date|before:today', 'address' => 'required|max:1000', 'phone' => 'required|max:30', 'gender' => 'required|in:male,female', 'jersey_product_id' => 'required|exists:jersey_products,id', 'jersey_size_id' => 'required|exists:jersey_sizes,id', 'model' => 'required|max:50', 'sleeve' => 'required|in:short,long', 'quantity' => 'required|integer|min:1|max:2']);
        $product = JerseyProduct::findOrFail($d['jersey_product_id']);
        $size = JerseySize::whereBelongsTo($product, 'product')->findOrFail($d['jersey_size_id']);
        $order = DB::transaction(function () use ($d, $product, $size) {
            $unit = (float) $product->price + (float) $size->price_adjustment;
            $order = JerseyOrder::create([...$d, 'order_number' => 'TEMP-'.str()->uuid(), 'total' => $unit * $d['quantity']]);
            $order->update(['order_number' => 'JRS-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);
            JerseyOrderItem::create(['jersey_order_id' => $order->id, 'jersey_product_id' => $product->id, 'jersey_size_id' => $size->id, 'model' => $d['model'], 'sleeve' => $d['sleeve'], 'quantity' => $d['quantity'], 'unit_price' => $unit, 'subtotal' => $unit * $d['quantity']]);

            return $order;
        });

        return redirect()->route('jersey.show', ['order' => $order->order_number, 'phone' => $order->phone])->with('success', 'Pesanan berhasil dibuat. Simpan nomor pesanan Anda.');
    }

    public function lookup()
    {
        return view('jersey.lookup');
    }

    public function find(Request $r)
    {
        $d = $r->validate(['order_number' => 'required|string', 'phone' => 'required|string']);
        $order = JerseyOrder::where('order_number', strtoupper($d['order_number']))->where('phone', $d['phone'])->firstOrFail();

        return redirect()->route('jersey.show', ['order' => $order->order_number, 'phone' => $order->phone]);
    }

    public function show(Request $r, string $order)
    {
        $record = JerseyOrder::with(['items.product', 'items.size', 'payments' => fn ($q) => $q->select('id', 'jersey_order_id', 'amount', 'status', 'created_at')])->where('order_number', $order)->where('phone', $r->query('phone'))->firstOrFail();

        return view('jersey.show', ['order' => $record]);
    }

    public function payment(Request $r, string $order)
    {
        $record = JerseyOrder::where('order_number', $order)->where('phone', $r->input('phone'))->firstOrFail();
        $d = $r->validate(['amount' => 'required|numeric|min:1000|max:'.$record->balance, 'proof' => 'required|image|max:4096']);
        $path = $r->file('proof')->store('jersey-proofs', 'local');
        JerseyPayment::create(['jersey_order_id' => $record->id, 'amount' => $d['amount'], 'proof_path' => $path]);

        return back()->with('success', 'Bukti pembayaran diterima dan menunggu verifikasi.');
    }

    public function proof(JerseyPayment $payment)
    {
        abort_unless(auth()->user()?->isSuperAdmin() || auth()->user()?->hasRole('bendahara'), 403);

        return Storage::disk('local')->download($payment->proof_path);
    }
}
