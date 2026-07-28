<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminEventController extends Controller
{
    public function index()
    {
        $events = Event::with('products')
            ->orderBy('event_date', 'desc')
            ->get();

        $occupiedDates = Event::where('status', '!=', 'cancelado')
            ->pluck('event_date')
            ->map(fn($d) => is_string($d) ? substr($d, 0, 10) : $d->format('Y-m-d'))
            ->values()
            ->all();

        return Inertia::render('Admin/EventList', [
            'events' => $events,
            'occupiedDates' => $occupiedDates,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'status' => 'required|in:pendiente,confirmado,entregado,cancelado',
            'total' => 'nullable|numeric|min:0',
            'deposit_paid' => 'nullable|boolean',
        ]);

        $event->update($validated);

        return redirect()->back();
    }

    public function updateProductQuantities(Request $request, Event $event)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:0',
        ]);

        foreach ($validated['products'] as $product) {
            $event->products()->updateExistingPivot($product['id'], [
                'quantity' => $product['quantity'],
            ]);
        }

        return redirect()->back();
    }
}
