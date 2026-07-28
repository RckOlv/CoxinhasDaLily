<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $occupiedDates = Event::where('status', '!=', 'cancelado')
            ->where('event_date', '>=', now()->toDateString())
            ->pluck('event_date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->values();

        return Inertia::render('EventForm', [
            'products' => $products,
            'occupiedDates' => $occupiedDates,
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

        $eventDate = Carbon::parse($validated['event_date']);

        $saturdayOffset = ($eventDate->dayOfWeek + 1) % 7;
        $saturday = (clone $eventDate)->subDays($saturdayOffset)->startOfDay();
        $sunday = (clone $saturday)->addDay()->endOfDay();

        $weekendCount = Event::where('status', '!=', 'cancelado')
            ->whereBetween('event_date', [
                $saturday->toDateString(),
                $sunday->toDateString(),
            ])
            ->count();

        if ($weekendCount >= 2) {
            return back()->withErrors([
                'event_date' => 'Ese fin de semana ya tiene 2 eventos. Elegí otra fecha.',
            ])->withInput();
        }

        $monthCount = Event::where('status', '!=', 'cancelado')
            ->whereYear('event_date', $eventDate->year)
            ->whereMonth('event_date', $eventDate->month)
            ->count();

        if ($monthCount >= 12) {
            return back()->withErrors([
                'event_date' => 'Ese mes ya tiene 12 eventos. Elegí otra fecha.',
            ])->withInput();
        }

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
        $date = \Carbon\Carbon::parse($event->event_date)->format('d/m/Y');

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
