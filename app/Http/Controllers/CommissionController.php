<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommissionController extends Controller
{
    private const COMMISSION_RATE = 0.10;

    public function index(Request $request)
    {
        $now = now();

        $month = $request->integer('month', $now->month);
        $year = $request->integer('year', $now->year);

        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }

        if ($year < 2000 || $year > $now->year + 1) {
            $year = $now->year;
        }

        $totalOrders = Order::where('status', 'entregado')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('total');

        $totalEvents = Event::where('status', 'entregado')
            ->whereMonth('event_date', $month)
            ->whereYear('event_date', $year)
            ->sum('total');

        $totalSales = $totalOrders + $totalEvents;

        return Inertia::render('Admin/Commissions', [
            'monthLabel' => ucfirst(Carbon::create($year, $month)->locale('es')->translatedFormat('F Y')),
            'month' => $month,
            'year' => $year,
            'totalOrders' => (float) $totalOrders,
            'totalEvents' => (float) $totalEvents,
            'totalSales' => (float) $totalSales,
            'commissionRate' => self::COMMISSION_RATE,
            'commission' => (float) ($totalSales * self::COMMISSION_RATE),
        ]);
    }
}
