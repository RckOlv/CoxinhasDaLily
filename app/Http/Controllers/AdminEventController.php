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

        return Inertia::render('Admin/EventList', [
            'events' => $events,
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
}
