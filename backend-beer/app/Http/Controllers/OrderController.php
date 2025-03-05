<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders;
        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $orderData = $request->validated();
        $total = 0;

        $order = Order::create([
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'total' => $total,
        ]);

        foreach ($orderData['products'] as $productData) {
            $product = Product::findOrFail($productData['id']);
            $order->products()->attach($product, [
                'quantity' => $productData['quantity'],
                'price' => $product->price,
            ]);
            $total += $product->price * $productData['quantity'];
        }

        // Actualizar el total del pedido
        $order->update(['total' => $total]);

        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, Order $order)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->update($request->validated());

        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // El usuario solo puede cancelar su propio pedido
        if ($order->user_id !== Auth::id()) {  
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->delete();
        return response()->json(['message' => 'Order canceled'], 200);
    }
}
