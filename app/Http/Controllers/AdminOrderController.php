<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

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
            $this->sendPushToClient($order, '¡Tu pedido fue confirmado! 🎉', 'Lily confirmó tu pedido. Coordiná la entrega por WhatsApp.');
        }

        if ($validated['status'] === 'entregado' && $oldStatus !== 'entregado') {
            $this->sendPushToClient($order, '¡Pedido entregado! ✅', 'Tu pedido de Coxinhas da Lily fue entregado. ¡Que lo disfrutes!');
        }

        return redirect()->back();
    }

    private function sendPushToClient(Order $order, string $title, string $body)
    {
        if (!$order->push_endpoint) {
            error_log('sendPushToClient: no push_endpoint on order ' . $order->id);
            return;
        }

        $subscription = PushSubscription::where('endpoint', $order->push_endpoint)->first();
        if (!$subscription) {
            error_log('sendPushToClient: subscription not found for endpoint ' . $order->push_endpoint);
            return;
        }

        $vapidPublicKey = config('services.vapid.public_key');
        $vapidPrivateKey = config('services.vapid.private_key');
        if (!$vapidPublicKey || !$vapidPrivateKey) {
            error_log('sendPushToClient: VAPID keys not configured');
            return;
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('services.vapid.subject'),
                    'publicKey' => $vapidPublicKey,
                    'privateKey' => $vapidPrivateKey,
                ],
            ]);

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'url' => url('/'),
            ]);

            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->p256dh,
                    'authToken' => $subscription->auth,
                ]),
                $payload
            );

            foreach ($webPush->flush() as $report) {
                if (!$report->isSuccess()) {
                    $reason = $report->getReason();
                    $response = $report->getResponse();
                    $statusCode = $response ? $response->getStatusCode() : 'unknown';
                    error_log('sendPushToClient: push failed - status=' . $statusCode . ' reason=' . $reason);
                    if ($response && in_array($statusCode, [404, 410])) {
                        $subscription->delete();
                        error_log('sendPushToClient: deleted expired subscription');
                    }
                }
            }
        } catch (\Exception $e) {
            error_log('sendPushToClient: exception - ' . $e->getMessage());
        }
    }

    private function decrementStock(Order $order)
    {
        if ($order->stock_decremented) return;

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
