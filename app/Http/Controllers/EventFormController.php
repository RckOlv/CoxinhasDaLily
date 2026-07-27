<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventFormController extends Controller
{
    public function show()
    {
        $products = Product::where('is_event', true)
            ->where('is_active', true)
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        return Inertia::render('EventForm', [
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_whatsapp' => 'required|string|max:255',
            'event_date' => 'required|date|after_or_equal:' . now()->addDays(15)->format('Y-m-d'),
            'quantity' => 'required|integer|min:100',
            'pickup_time' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
        ]);

        $event = Event::create([
            'client_name' => $validated['client_name'],
            'client_whatsapp' => $validated['client_whatsapp'],
            'event_date' => $validated['event_date'],
            'quantity' => $validated['quantity'],
            'pickup_time' => $validated['pickup_time'],
            'event_type' => $validated['event_type'],
            'color' => $validated['color'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['products'] as $product) {
            $event->products()->attach($product['id'], ['quantity' => 0]);
        }

        $event->load('products');

        $whatsappUrl = $this->buildWhatsAppUrl($event);

        return Inertia::location($whatsappUrl);
    }

    private function buildWhatsAppUrl(Event $event): string
    {
        $phone = config('services.whatsapp');
        $date = $event->event_date->format('d/m/Y');

        $productsList = $event->products->map(function ($p) {
            return "- {$p->name}";
        })->implode("\n");

        $message = "¡Hola Lily! 🎉 Quiero solicitar un evento:\n\n"
            . "👤 *Cliente:* {$event->client_name}\n"
            . "📱 *WhatsApp:* {$event->client_whatsapp}\n"
            . "📅 *Fecha:* {$date}\n"
            . "🕐 *Horario de retiro:* {$event->pickup_time}\n"
            . "👥 *Cantidad de personas:* {$event->quantity}\n"
            . "🎊 *Tipo de evento:* {$event->event_type}\n"
            . "🎨 *Color:* {$event->color}\n"
            . ($event->notes ? "📝 *Observaciones:* {$event->notes}\n" : "")
            . "\n🛍️ *Productos:*\n{$productsList}\n\n"
            . "Quedo a la espera del presupuesto! 😊";

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }
}
