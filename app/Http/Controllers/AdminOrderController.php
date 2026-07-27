<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/OrderList', [
            'orders' => $orders,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pendiente,confirmado,entregado,cancelado',
        ]);

        $oldStatus = $order->status;
        $order->update($validated);

        if ($validated['status'] === 'confirmado' && $oldStatus !== 'confirmado') {
            $this->decrementStock($order);
        }

        return redirect()->back();
    }

    private function decrementStock(Order $order)
    {
        if ($order->stock_decremented) {
            return;
        }

        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $newStock = max(0, $product->stock_quantity - $item->quantity);
                $product->update(['stock_quantity' => $newStock]);
            }
        }

        $order->update(['stock_decremented' => true]);
    }
}
