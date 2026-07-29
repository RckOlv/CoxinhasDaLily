<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $now = now()->startOfDay();

        $pendingOrders = Order::where('status', 'pendiente')
            ->whereDate('created_at', $now)
            ->count();

        $ordersToday = Order::whereDate('created_at', $now)->count();

        $pendingEvents = Event::where('status', 'pendiente')
            ->where('event_date', '>=', $now)
            ->count();

        $upcomingEvents = Event::whereIn('status', ['pendiente', 'confirmado'])
            ->where('event_date', '>=', $now)
            ->where('event_date', '<=', $now->copy()->addDays(15))
            ->with('products')
            ->orderBy('event_date')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'client_name' => $e->client_name,
                'event_date' => $e->event_date,
                'status' => $e->status,
                'quantity' => $e->quantity,
                'products_count' => $e->products->count(),
            ]);

        $eventsThisMonth = Event::where('status', '!=', 'cancelado')
            ->whereMonth('event_date', $now->month)
            ->whereYear('event_date', $now->year)
            ->count();

        $lowStockProducts = Product::where('stock_quantity', '<=', 3)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->get(['id', 'name', 'stock_quantity']);

        $outOfStockProducts = Product::where('stock_quantity', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'pendingOrders' => $pendingOrders,
                'ordersToday' => $ordersToday,
                'pendingEvents' => $pendingEvents,
                'upcomingEvents' => $upcomingEvents,
                'eventsThisMonth' => $eventsThisMonth,
                'eventsLimit' => 12,
                'lowStockProducts' => $lowStockProducts,
                'outOfStockProducts' => $outOfStockProducts,
            ],
        ]);
    }
}
