<?php

namespace App\Http\Controllers;

use App\Models\JerseyOrder;
use App\Models\JerseyOrderItem;
use App\Models\JerseyPayment;
use App\Models\JerseyProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class JerseyController extends Controller
{
    public function create()
    {
        return view('jersey.order', ['products' => JerseyProduct::with('sizes')->where('is_active', true)->get()]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'customer_name' => ['required', 'string', 'max:150'], 'address' => ['required', 'string', 'max:1000'], 'phone' => ['required', 'string', 'max:30'],
            'items' => ['required', 'array', 'min:1', 'max:20'], 'items.*.jersey_product_id' => ['required', 'integer', 'exists:jersey_products,id'],
            'items.*.jersey_size_id' => ['required', 'integer', 'exists:jersey_sizes,id'], 'items.*.model' => ['required', 'in:Lelaki Pendek,Lelaki Panjang,Muslimah'],
            'items.*.sleeve' => ['required', 'in:short,long'], 'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);
        $models = ['Lelaki Pendek' => 'short', 'Lelaki Panjang' => 'long', 'Muslimah' => 'long'];
        $products = JerseyProduct::with('sizes')->where('is_active', true)->whereIn('id', collect($d['items'])->pluck('jersey_product_id'))->get()->keyBy('id');
        $items = collect($d['items'])->map(function (array $item, int $index) use ($models, $products) {
            $product = $products->get($item['jersey_product_id']);
            if (! $product) throw ValidationException::withMessages(["items.$index.jersey_product_id" => 'Produk jersey tidak tersedia.']);
            $size = $product->sizes->firstWhere('id', $item['jersey_size_id']);
            if (! $size) throw ValidationException::withMessages(["items.$index.jersey_size_id" => 'Ukuran tidak sesuai dengan produk yang dipilih.']);
            if ($models[$item['model']] !== $item['sleeve']) throw ValidationException::withMessages(["items.$index.model" => 'Model dan jenis lengan tidak sesuai.']);
            $unit = (float) $product->price + (float) $size->price_adjustment;
            return compact('item', 'product', 'size', 'unit');
        });
        $order = DB::transaction(function () use ($d, $items) {
            $order = JerseyOrder::create([
                ...collect($d)->only(['customer_name', 'address', 'phone'])->all(),
                'order_number' => 'TEMP-'.str()->uuid(),
                'total' => $items->sum(fn ($entry) => $entry['unit'] * $entry['item']['quantity']),
            ]);
            $order->update(['order_number' => 'JRS-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);
            foreach ($items as $entry) {
                $item = $entry['item'];
                JerseyOrderItem::create(['jersey_order_id' => $order->id, 'jersey_product_id' => $entry['product']->id, 'jersey_size_id' => $entry['size']->id, 'model' => $item['model'], 'sleeve' => $item['sleeve'], 'quantity' => $item['quantity'], 'unit_price' => $entry['unit'], 'subtotal' => $entry['unit'] * $item['quantity']]);
            }

            return $order;
        });

        $r->session()->put('jersey_access.'.$order->order_number, true);

        return redirect()->route('jersey.show', $order->order_number)->with('success', 'Pesanan berhasil dibuat. Simpan nomor pesanan Anda.');
    }

    public function lookup()
    {
        return view('jersey.lookup');
    }

    public function find(Request $r)
    {
        $d = $r->validate(['order_number' => 'required|string', 'phone' => 'required|string']);
        $order = JerseyOrder::where('order_number', strtoupper($d['order_number']))->where('phone', $d['phone'])->firstOrFail();

        $r->session()->put('jersey_access.'.$order->order_number, true);

        return redirect()->route('jersey.show', $order->order_number);
    }

    public function show(Request $r, string $order)
    {
        abort_unless($r->session()->get('jersey_access.'.$order), 403);
        $record = JerseyOrder::with(['items.product', 'items.size', 'payments' => fn ($q) => $q->select('id', 'jersey_order_id', 'amount', 'status', 'created_at')])->where('order_number', $order)->firstOrFail();

        return view('jersey.show', ['order' => $record]);
    }

    public function payment(Request $r, string $order)
    {
        abort_unless($r->session()->get('jersey_access.'.$order), 403);
        $record = JerseyOrder::where('order_number', $order)->firstOrFail();
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
