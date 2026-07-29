<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send push reminders to clients 2 days, 1 day, and on the day of their event';

    public function handle()
    {
        $vapidPublicKey = config('services.vapid.public_key');
        $vapidPrivateKey = config('services.vapid.private_key');
        $vapidSubject = config('services.vapid.subject');

        if (!$vapidPublicKey || !$vapidPrivateKey) {
            $this->error('VAPID keys not configured');
            return 1;
        }

        $today = now()->toDateString();
        $inOneDay = now()->addDay()->toDateString();
        $inTwoDays = now()->addDays(2)->toDateString();

        $events = Event::whereIn('status', ['pendiente', 'confirmado'])
            ->whereNotNull('push_endpoint')
            ->whereIn('event_date', [$today, $inOneDay, $inTwoDays])
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events to remind');
            return 0;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => $vapidSubject,
                'publicKey' => $vapidPublicKey,
                'privateKey' => $vapidPrivateKey,
            ],
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($events as $event) {
            $subscription = PushSubscription::where('endpoint', $event->push_endpoint)->first();
            if (!$subscription) continue;

            $diff = now()->startOfDay()->diffInDays($event->event_date);

            $title = match(true) {
                $diff === 0 => '🎉 Hoy es tu evento en Coxinhas da Lily!',
                $diff === 1 => '🎊 Mañana es tu evento en Coxinhas da Lily!',
                $diff === 2 => '📅 Tu evento en Coxinhas da Lily es en 2 días',
                default => 'Recordatorio de evento',
            };

            $body = match(true) {
                $diff === 0 => 'No te olvides de pasar a retirar. Cualquier cosa comunicate con Lily.',
                $diff === 1 => 'Recordá que mañana es tu evento. Prepará todo para el retiro.',
                $diff === 2 => 'Ya falta poco para tu evento. Cualquier cambio, avisale a Lily.',
                default => '',
            };

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
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
            } else {
                $failed++;
                $response = $report->getResponse();
                if ($response && in_array($response->getStatusCode(), [404, 410])) {
                    PushSubscription::where('endpoint', (string) $report->getRequest()->getUri())->delete();
                }
            }
        }

        $this->info("Sent: $sent, Failed: $failed");
        return 0;
    }
}
