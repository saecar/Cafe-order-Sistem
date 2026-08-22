<?php

namespace App\Http\Controllers;

use App\Models\contact;
use App\Models\order;
use App\Models\products;
use Illuminate\Http\Request;

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
}
