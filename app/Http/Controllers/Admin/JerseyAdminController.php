<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JerseyOrder;
use App\Models\JerseyAgent;
use App\Models\JerseyPayment;
use App\Models\JerseyProduct;
use App\Models\JerseySize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JerseyAdminController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'production' => ['nullable', 'in:active,queued,processing,ready,delivered,cancelled'],
            'payment' => ['nullable', 'in:pending,paid'],
        ]);

        $orders = JerseyOrder::query()
            ->with(['agent', 'items.product', 'items.size', 'payments'])
            ->when($filters['q'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when(($filters['production'] ?? null) === 'active', fn ($query) => $query->whereIn('production_status', ['queued', 'processing']))
            ->when(($filters['production'] ?? null) && $filters['production'] !== 'active', fn ($query) => $query->where('production_status', $filters['production']))
            ->when(($filters['payment'] ?? null) === 'pending', fn ($query) => $query->whereHas('payments', fn ($payments) => $payments->where('status', 'pending')))
            ->when(($filters['payment'] ?? null) === 'paid', fn ($query) => $query
                ->whereRaw('(select coalesce(sum(amount), 0) from jersey_payments where jersey_payments.jersey_order_id = jersey_orders.id and status = ?) >= (jersey_orders.total + coalesce(jersey_orders.payment_code, 0) / 100)', ['verified']))
            ->latest();

        $modelTotals = DB::table('jersey_order_items')
            ->join('jersey_orders', 'jersey_orders.id', '=', 'jersey_order_items.jersey_order_id')
            ->whereNull('jersey_orders.deleted_at')
            ->where('jersey_orders.production_status', '!=', 'cancelled')
            ->select('jersey_order_items.model', DB::raw('SUM(jersey_order_items.quantity) as quantity'))
            ->groupBy('jersey_order_items.model')
            ->pluck('quantity', 'model');

        return view('admin.orders', [
            'orders' => $orders->paginate(20)->withQueryString(),
            'products' => JerseyProduct::with('sizes')->withTrashed()->get(),
            'agents' => JerseyAgent::withCount('orders')->orderBy('name')->get(),
            'summary' => [
                'all' => JerseyOrder::count(),
                'pendingPayments' => JerseyPayment::where('status', 'pending')->count(),
                'inProduction' => JerseyOrder::whereIn('production_status', ['queued', 'processing'])->count(),
                'ready' => JerseyOrder::where('production_status', 'ready')->count(),
                'funds' => JerseyPayment::where('status', 'verified')->whereHas('order')->sum('amount'),
                'maleLong' => (int) ($modelTotals['Lelaki Panjang'] ?? 0),
                'maleShort' => (int) ($modelTotals['Lelaki Pendek'] ?? 0),
                'muslimah' => (int) ($modelTotals['Muslimah'] ?? 0),
            ],
            'filters' => $filters,
        ]);
    }

    public function storeAgent(Request $request)
    {
        abort_if(JerseyAgent::count() >= 10, 422, 'Maksimal 10 petugas pemesanan.');
        $data = $request->validate(['name' => ['required', 'string', 'max:150'], 'phone' => ['nullable', 'string', 'max:30']]);
        JerseyAgent::create([...$data, 'token' => bin2hex(random_bytes(24)), 'management_token' => bin2hex(random_bytes(24))]);

        return back()->with('success', 'Petugas dan link pemesanan berhasil dibuat.');
    }

    public function updateAgent(Request $request, JerseyAgent $agent)
    {
        $agent->update($request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['required', 'boolean'],
        ]));

        return back()->with('success', 'Data petugas diperbarui.');
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

    public function destroyOrder(JerseyOrder $order)
    {
        $order->delete();

        return back()->with('success', "Pesanan {$order->order_number} dihapus.");
    }

    public function verifyPayment(Request $request, JerseyPayment $payment)
    {
        $data = $request->validate(['status' => ['required', 'in:verified,rejected'], 'notes' => ['nullable', 'max:1000']]);
        if ($data['status'] === 'verified') {
            $verifiedOther = $payment->order->payments()->where('status', 'verified')->where('id', '!=', $payment->id)->sum('amount');
            abort_if((float) $verifiedOther + (float) $payment->amount > $payment->order->payable_total, 422, 'Pembayaran terverifikasi akan melebihi total pesanan.');
        }
        $payment->update([...$data, 'verified_by' => $request->user()->id, 'verified_at' => now()]);

        return back()->with('success', 'Status pembayaran diperbarui.');
    }
}
