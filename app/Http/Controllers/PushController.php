<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'p256dh' => $request->keys['p256dh'],
                'auth' => $request->keys['auth'],
            ]
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);
        PushSubscription::where('endpoint', $request->endpoint)->delete();
        return response()->json(['success' => true]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:300',
            'url' => 'nullable|string',
        ]);

        $subscriptions = PushSubscription::all();
        if ($subscriptions->isEmpty()) {
            return response()->json(['sent' => 0, 'failed' => 0]);
        }

        $vapidPublicKey = config('services.vapid.public_key');
        $vapidPrivateKey = config('services.vapid.private_key');

        if (!$vapidPublicKey || !$vapidPrivateKey) {
            return response()->json(['error' => 'VAPID keys not configured'], 500);
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('services.vapid.subject'),
                'publicKey' => $vapidPublicKey,
                'privateKey' => $vapidPrivateKey,
            ],
        ]);

        $payload = json_encode([
            'title' => $request->title,
            'body' => $request->body,
            'url' => $request->url ?? url('/'),
        ]);

        foreach ($subscriptions as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->p256dh,
                    'authToken' => $sub->auth,
                ]),
                $payload
            );
        }

        $sent = 0;
        $failed = 0;

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
            } else {
                $failed++;
                $response = $report->getResponse();
                if ($response && in_array($response->getStatusCode(), [404, 410])) {
                    $sub = PushSubscription::where('endpoint', (string) $report->getRequest()->getUri())->first();
                    if ($sub) $sub->delete();
                }
            }
        }

        return response()->json(['sent' => $sent, 'failed' => $failed]);
    }

    public function status()
    {
        return response()->json([
            'configured' => !empty(config('services.vapid.public_key')),
            'subscribers' => PushSubscription::count(),
        ]);
    }
}
