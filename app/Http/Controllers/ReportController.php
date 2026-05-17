<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        // Redirect to sales report by default, or show a reports dashboard
        return redirect()->route('reports.sales');
        // Or if you prefer a landing page for reports:
        // return view('reports.index');
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
        $categoryId = $request->input('category');

        $query = Order::with(['customer', 'items.product'])->where('status', 'completed');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        if ($categoryId) {
            $query->whereHas('items.product', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $salesData = $query->orderBy('created_at')->get();
        $viewData = collect();
        $categories = Category::all();

        foreach ($salesData as $order) {
            foreach ($order->items as $item) {
                if (!$categoryId || $item->product->category_id == $categoryId) {
                $viewData->push([
                    'order_id' => $order->id,
                    'date' => $order->created_at->format('M d, Y H:i'),
                    'customer_name' => optional($order->customer)->name ?? 'N/A',
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price_per_item' => $item->price,
                    'item_subtotal' => $item->subtotal,
                    'order_total' => $order->total,
                    'payment_method' => $order->payment_method,
                ]);
                }
            }
        }

        return view('reports.sales', compact('viewData', 'startDate', 'endDate', 'categories'));
    }

    public function salesExport(Request $request)
    {
        $startDate = $request->input('start_date')
    ? Carbon::parse($request->input('start_date'), config('app.timezone'))->startOfDay()->timezone('UTC')
    : null;

$endDate = $request->input('end_date')
    ? Carbon::parse($request->input('end_date'), config('app.timezone'))->endOfDay()->timezone('UTC')
    : null;


        $categoryId = $request->input('category');

        return Excel::download(
            new SalesExport($startDate, $endDate, $categoryId),
            'sales_report_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function inventory(Request $request)
    {
        $products = Product::orderBy('stock')->get();

        return view('reports.inventory', compact('products'));
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
}
