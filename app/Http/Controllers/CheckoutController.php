<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function decrementStock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $result = DB::transaction(function () use ($validated) {
            $decremented = [];

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para '{$product->name}': disponibles {$product->stock_quantity}.",
                    ], 422);
                }

                $product->decrement('stock_quantity', $item['quantity']);
                $decremented[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'remaining' => $product->fresh()->stock_quantity,
                ];
            }

            return $decremented;
        });

        return response()->json(['success' => true, 'decremented' => $result]);
    }
}
