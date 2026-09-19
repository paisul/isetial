<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JerseyOrder;
use App\Models\JerseyPayment;
use App\Models\JerseyProduct;
use App\Models\JerseySize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JerseyAdminController extends Controller
{
    public function index()
    {
        return view('admin.orders', [
            'orders' => JerseyOrder::with(['items.product', 'items.size', 'payments'])->latest()->paginate(20),
            'products' => JerseyProduct::with('sizes')->withTrashed()->get(),
        ]);
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'max:150'], 'description' => ['nullable', 'max:2000'], 'price' => ['required', 'numeric', 'min:0'], 'sizes' => ['required', 'string', 'max:300']]);
        DB::transaction(function () use ($data) {
            $product = JerseyProduct::create([...collect($data)->only(['name', 'description', 'price'])->all(), 'is_active' => true]);
            collect(explode(',', $data['sizes']))->map(fn ($size) => strtoupper(trim($size)))->filter()->unique()->each(fn ($size) => JerseySize::create(['jersey_product_id' => $product->id, 'name' => $size]));
        });

        return back()->with('success', 'Produk Jersey ditambahkan.');
    }

    public function updateProduct(Request $request, JerseyProduct $product)
    {
        $product->update($request->validate(['name' => ['required', 'max:150'], 'description' => ['nullable', 'max:2000'], 'price' => ['required', 'numeric', 'min:0'], 'is_active' => ['required', 'boolean']]));

        return back()->with('success', 'Produk Jersey diperbarui.');
    }

    public function storeSize(Request $request, JerseyProduct $product)
    {
        $data = $request->validate(['name' => ['required', 'max:20', Rule::unique('jersey_sizes')->where('jersey_product_id', $product->id)], 'price_adjustment' => ['required', 'numeric', 'min:0']]);
        $product->sizes()->create(['name' => strtoupper($data['name']), 'price_adjustment' => $data['price_adjustment']]);

        return back()->with('success', 'Ukuran ditambahkan.');
    }

    public function updateSize(Request $request, JerseySize $size)
    {
        $size->update($request->validate(['stock' => ['nullable', 'integer', 'min:0', 'max:100000']]));

        return back()->with('success', 'Stok ukuran diperbarui.');
    }

    public function updateOrder(Request $request, JerseyOrder $order)
    {
        $order->update($request->validate(['production_status' => ['required', 'in:queued,processing,ready,delivered,cancelled'], 'notes' => ['nullable', 'max:2000']]));

        return back()->with('success', 'Status produksi diperbarui.');
    }

    public function verifyPayment(Request $request, JerseyPayment $payment)
    {
        $data = $request->validate(['status' => ['required', 'in:verified,rejected'], 'notes' => ['nullable', 'max:1000']]);
        if ($data['status'] === 'verified') {
            $verifiedOther = $payment->order->payments()->where('status', 'verified')->where('id', '!=', $payment->id)->sum('amount');
            abort_if((float) $verifiedOther + (float) $payment->amount > (float) $payment->order->total, 422, 'Pembayaran terverifikasi akan melebihi total pesanan.');
        }
        $payment->update([...$data, 'verified_by' => $request->user()->id, 'verified_at' => now()]);

        return back()->with('success', 'Status pembayaran diperbarui.');
    }
}
