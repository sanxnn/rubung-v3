<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CustomOrder;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending_payment')->count(),
            'processing_orders' => Order::whereIn('status', ['paid', 'processing', 'packing'])->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'pending_custom_orders' => CustomOrder::where('status', 'pending_review')->count(),
            'total_customers' => User::where('role', 'customer')->count(),

            'low_stock_items' => ProductVariant::where('stock', '<', 5)->count(),
        ];

        $validStatuses = ['paid', 'processing', 'packing', 'shipped', 'completed'];
        $totalRevenue = Order::whereIn('status', $validStatuses)->sum('final_amount');

        $chartMonths = [];
        $chartRevenues = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chartMonths[] = $date->locale('id')->isoFormat('MMM');

            $chartRevenues[] = Order::whereIn('status', $validStatuses)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('final_amount');
        }

        $recent_orders = Order::with('user')->latest()->take(5)->get();
        $recent_activities = CustomOrder::with('user')->latest()->take(4)->get();

        return view('admin.dashboard', compact(
            'stats', 'totalRevenue', 'chartMonths', 'chartRevenues', 'recent_orders', 'recent_activities'
        ));
    }
}
