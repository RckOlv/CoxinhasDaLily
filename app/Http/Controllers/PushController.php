<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $vapidPublicKey = config('services.vapid.public_key');
        $vapidPrivateKey = config('services.vapid.private_key');

        if (!$vapidPublicKey || !$vapidPrivateKey) {
            return response()->json(['error' => 'VAPID keys not configured'], 500);
        }

        $sent = 0;
        $failed = 0;

        foreach ($subscriptions as $sub) {
            try {
                $payload = json_encode([
                    'title' => $request->title,
                    'body' => $request->body,
                    'url' => $request->url ?? url('/'),
                ]);

                $headers = [
                    'ttl' => 86400,
                    'urgency' => 'high',
                ];

                $response = Http::withHeaders($headers)->withBody($payload, 'application/json')
                    ->post($sub->endpoint);

                if ($response->successful()) {
                    $sent++;
                } else {
                    $failed++;
                    if ($response->status() === 404 || $response->status() === 410) {
                        $sub->delete();
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                $sub->delete();
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
