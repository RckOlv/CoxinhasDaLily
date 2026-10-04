<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use Carbon\Carbon;
use Inertia\Inertia;

class CommissionController extends Controller
{
    private const COMMISSION_RATE = 0.10;

    public function index()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $totalOrders = Order::where('status', 'entregado')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total');

        $totalEvents = Event::where('status', 'entregado')
            ->where('event_date', '>=', $monthStart->toDateString())
            ->where('event_date', '<=', $monthEnd->toDateString())
            ->sum('total');

        $totalSales = $totalOrders + $totalEvents;

        return Inertia::render('Admin/Commissions', [
            'monthLabel' => $now->translatedFormat('F Y'),
            'totalOrders' => (float) $totalOrders,
            'totalEvents' => (float) $totalEvents,
            'totalSales' => (float) $totalSales,
            'commissionRate' => self::COMMISSION_RATE,
            'commission' => (float) ($totalSales * self::COMMISSION_RATE),
        ]);
    }
}