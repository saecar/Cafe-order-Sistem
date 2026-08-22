<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreProductRequest;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $product = products::where('is_active', true)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->with('category')
            ->paginate(12);

            return response()->json([
                'status' => 'success',
                'data' => $product,
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
    public function store(StoreProductRequest $request)
    {
        if (!$request->bearerToken()){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = products::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(products $products)
    {
        return response()->json([
            'status' => 'success',
            'data' => $products->load('category'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, products $products)
    {
        if (!$request->bearerToken()){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($products->image){
                Storage::disk('public')->delete($products->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $products->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $products,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, products $products) // fix: Request biasa, bukan StoreProductRequest
    {
        if ($products->orderItems()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete product with associated order items.',
                ], 400);
        }

        if ($products->image) {
            Storage::disk('public')->delete($products->image);
        }

        $products->delete();

        return response()->json(['status' => 'success', 'message' => 'Product deleted successfully']);
        
    }
}