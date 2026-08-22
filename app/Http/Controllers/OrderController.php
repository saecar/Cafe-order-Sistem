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
    public function index(Request $request)
    {
        $orders = order::when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return response()->json(['orders' => $orders]);
    }

    public function store(StoreOrderRequest $request)
    {
        $order = DB::transaction(function () use ($request) {
            $total = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = products::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    abort(422, "Stock {$product->name} tidak cukup");
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            // order dibuat SEKALI, setelah semua item diproses
            $order = order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_name' => $request->customer_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            foreach ($itemsData as $data) {
                $order->orderItems()->create($data); // fix: orderItems() bukan items()
            }

            return $order;
        });

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load('orderItems.product'),
        ], 201);
    }

    public function show(order $order)
    {
        return response()->json(['order' => $order->load('orderItems.product', 'payment')]);
    }

    public function updateStatus(Request $request, order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,completed,cancelled']);

        $order->update(['status' => $request->status]);

        return response()->json($order);
    }

    public function destroy(order $order)
    {
        //
    }
}