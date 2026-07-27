<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_whatsapp' => 'required|string|max:255',
            'delivery_method' => 'required|in:pickup,envio',
            'delivery_address' => 'nullable|string|max:255',
            'payment_method' => 'required|in:efectivo,transferencia,mercadopago',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'push_endpoint' => 'nullable|string',
        ]);

        $total = collect($validated['items'])->sum(fn ($item) => $item['price'] * $item['quantity']);

        $order = Order::create([
            'client_name' => $validated['client_name'],
            'client_whatsapp' => $validated['client_whatsapp'],
            'delivery_method' => $validated['delivery_method'],
            'delivery_address' => $validated['delivery_address'] ?? null,
            'payment_method' => $validated['payment_method'],
            'total' => $total,
            'push_endpoint' => $validated['push_endpoint'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }
}
