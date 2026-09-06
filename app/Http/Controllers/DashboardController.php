<?php

namespace App\Http\Controllers;

use App\Models\contact;
use App\Models\order;
use App\Models\order_item;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_order' => order::count(),
                'total_revenue' => order::where('status', 'completed')->sum('total_amount'),
                'order_pending' => order::where('status', 'pending')->count(),
                'contact_baru' => contact::count(),
                'total_product' => products::where('is_active', true)->count(),
            ],
        ]);
    }

    public function bestSeller(Request $request)
    {
        $days = (int) $request->query('days', 7);

        $data = order_item::select(
                'product_id',
                DB::raw('SUM(quantity) as total_terjual'),
                DB::raw('SUM(subtotal) as total_omzet')
            )
            ->whereHas('order', function ($q) use ($days) {
                $q->whereIn('status', ['processing', 'completed'])
                  ->where('created_at', '>=', now()->subDays($days));
            })
            ->with('product:id,name,image,price')
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function profitRecap(Request $request)
    {
        $from = $request->query('from', now()->subDays(6)->format('Y-m-d'));
        $to = $request->query('to', now()->format('Y-m-d'));

        $rows = order_item::select(
                DB::raw('DATE(orders.created_at) as tanggal'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('SUM(order_items.quantity * order_items.cost_price) as modal'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as jumlah_order')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', ['processing', 'completed'])
            ->whereDate('orders.created_at', '>=', $from)
            ->whereDate('orders.created_at', '<=', $to)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->map(function ($row) {
                $row->profit = $row->revenue - $row->modal;
                return $row;
            });

        $totals = [
            'revenue' => $rows->sum('revenue'),
            'modal' => $rows->sum('modal'),
            'profit' => $rows->sum('profit'),
            'jumlah_order' => $rows->sum('jumlah_order'),
        ];

        return response()->json(['status' => 'success', 'data' => $rows, 'totals' => $totals]);
    }
}