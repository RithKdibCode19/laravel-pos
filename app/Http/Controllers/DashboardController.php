<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get date range from request or default to current month
        $dateRange = request('date_range', 'month');
        $startDate = $this->getStartDate($dateRange);
        
        // Get statistics
        $totalSales = Order::where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->sum('total');
            
        $totalOrders = Order::where('created_at', '>=', $startDate)
            ->count();
            
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        
        // Get recent orders
        $recentSales = Order::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // Get popular products with total sales and quantity
        $popularProducts = OrderItem::select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        // Get popular customers
        $popularCustomers = Order::select(
                'customers.name',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent')
            )
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->take(5)
            ->get();

        // Get sales data for chart
        $salesData = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Format sales data for chart
        $salesChartData = [
            'labels' => $salesData->pluck('date'),
            'data' => $salesData->pluck('total')
        ];
            
        // Get low stock products
        $lowStockProducts = Product::where('stock', '<=', 10)
                                   ->orderBy('stock')
                                   ->get();

        return view('dashboard.index', compact(
            'totalSales',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'recentSales',
            'popularProducts',
            'popularCustomers',
            'salesChartData',
            'lowStockProducts'
        ));
    }
    
    private function getStartDate($dateRange)
    {
        $now = Carbon::now();
        
        return match($dateRange) {
            'today' => $now->startOfDay(),
            'week' => $now->copy()->startOfWeek(),
            'month' => $now->copy()->startOfMonth(),
            'year' => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };
    }
    
    private function getStatusColor($status)
    {
        return match($status) {
            'completed' => 'success',
            'pending' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
} 