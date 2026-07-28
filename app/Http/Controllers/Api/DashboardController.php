<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get aggregated business metrics for the restaurant dashboard.
     */
    public function stats(): JsonResponse
    {
        $today = now()->today();

        // 1. KPI Cards Data
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $totalOrdersToday = Order::whereDate('created_at', $today)->count();

        $activeOrdersCount = Order::whereIn('status', ['pending', 'preparing', 'ready'])->count();

        $totalTables = Table::count();
        $occupiedTables = Table::where('status', 'occupied')->count();
        $occupancyRate = $totalTables > 0 ? round(($occupiedTables / $totalTables) * 100) : 0;

        // 2. Weekly Sales Chart Data (Last 7 Days)
        $salesChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->format('D');
            $revenue = Order::whereDate('created_at', $date->toDateString())
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            $orders = Order::whereDate('created_at', $date->toDateString())->count();

            $salesChart[] = [
                'day' => $dayName,
                'date' => $date->format('M d'),
                'revenue' => (float) $revenue,
                'orders' => $orders,
            ];
        }

        // 3. Popular / Top Dishes
        $topDishes = DB::table('order_items')
            ->select('menu_items.id', 'menu_items.name', 'menu_items.price', 'menu_items.image', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.price', 'menu_items.image')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->price = (float) $item->price;
                return $item;
            });

        // 4. Recent Orders
        $recentOrders = Order::with(['table', 'items.menuItem'])
            ->latest()
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'kpis' => [
                    'today_revenue' => (float) $todayRevenue,
                    'today_orders' => $totalOrdersToday,
                    'active_orders' => $activeOrdersCount,
                    'table_occupancy' => [
                        'total' => $totalTables,
                        'occupied' => $occupiedTables,
                        'rate_percentage' => $occupancyRate,
                    ],
                ],
                'sales_chart' => $salesChart,
                'top_dishes' => $topDishes,
                'recent_orders' => $recentOrders,
            ],
        ]);
    }
}
