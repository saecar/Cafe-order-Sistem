<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\order;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->bearerToken()){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        $orders = Order::when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return response()->json([
            'orders' => $orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = DB::transaction(function () use ($request){
            $total = 0;
            $itemsdata = [];
            foreach ($request->items as $item) {
                $product = products::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    abort(422, "stock {$product->name} tidak cukup");
                }
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;
                $itemsdata[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'price' => $product->price,
                ];

                $order = order::create([
                    'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_name' => $request->customer_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'total_amount' => $total,
                'status' => 'pending',
                ]);

                foreach ($itemsdata as $itemdata) {
                    $order->items()->create($itemdata);
                }

                return $order;
            }
        });
        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(StoreOrderRequest $request,order $order)
    {
        if (!$request->bearerToken()){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        return response()->json([
            'order' => $order->load('items.product', 'payment'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, order $order)
    {
        if (!$request->bearerToken()){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
         $request->validate(['status' => 'required|in:pending,processing,completed,cancelled']);

        $order->update(['status' => $request->status]);

        return response()->json($order);
     }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(order $order)
    {
        //
    }
}
