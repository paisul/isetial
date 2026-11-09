<?php

namespace App\Http\Controllers;

use App\Models\JerseyAgent;
use App\Models\JerseyPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JerseyAgentDashboardController extends Controller
{
    public function index(Request $request, JerseyAgent $agent)
    {
        abort_unless($agent->is_active && $agent->management_token, 404);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'production' => ['nullable', 'in:queued,processing,ready,delivered,cancelled'],
        ]);

        $orders = $agent->orders()->with(['items.product', 'items.size', 'payments'])
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where(fn ($query) => $query
                ->where('order_number', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")))
            ->when($filters['production'] ?? null, fn ($query, $status) => $query->where('production_status', $status))
            ->latest();

        $modelTotals = DB::table('jersey_order_items')
            ->join('jersey_orders', 'jersey_orders.id', '=', 'jersey_order_items.jersey_order_id')
            ->where('jersey_orders.jersey_agent_id', $agent->id)
            ->whereNull('jersey_orders.deleted_at')
            ->where('jersey_orders.production_status', '!=', 'cancelled')
            ->select('jersey_order_items.model', DB::raw('SUM(jersey_order_items.quantity) as quantity'))
            ->groupBy('jersey_order_items.model')->pluck('quantity', 'model');

        return view('jersey.agent-dashboard', [
            'agent' => $agent,
            'orders' => $orders->paginate(20)->withQueryString(),
            'filters' => $filters,
            'summary' => [
                'orders' => $agent->orders()->count(),
                'funds' => JerseyPayment::where('status', 'verified')->whereHas('order', fn ($query) => $query->where('jersey_agent_id', $agent->id))->sum('amount'),
                'maleLong' => (int) ($modelTotals['Lelaki Panjang'] ?? 0),
                'maleShort' => (int) ($modelTotals['Lelaki Pendek'] ?? 0),
                'muslimah' => (int) ($modelTotals['Muslimah'] ?? 0),
            ],
        ]);
    }

    public function proof(JerseyAgent $agent, JerseyPayment $payment)
    {
        abort_unless($agent->is_active && $agent->management_token && $payment->order?->jersey_agent_id === $agent->id, 404);

        return Storage::disk('local')->download($payment->proof_path);
    }
}
