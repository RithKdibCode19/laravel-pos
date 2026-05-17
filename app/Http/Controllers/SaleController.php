<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Display a listing of the sales.
     */
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();
        $customers = Customer::all();
        $categories = Category::all();
        $recentOrders = Order::with(['customer', 'items.product'])
            ->latest()
            ->paginate(5);
            // ->take(10)
            // ->get();

        return view('sales.index', compact('products', 'customers', 'recentOrders', 'categories'));
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        $customers = Customer::all();
        return view('sales.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,transfer',
        ]);

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'total' => 0,
            'status' => 'completed',
            'payment_method' => $request->payment_method,
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            
            if ($product->stock < $item['quantity']) {
                $order->delete();
                return back()->with('error', "Insufficient stock for {$product->name}");
            }

            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;

            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => $subtotal,
            ]);

            $product->decrement('stock', $item['quantity']);
        }

        $order->update(['total' => $total]);

        return redirect()->route('sales.index')
            ->with('success', 'Sale completed successfully')
            ->with('order_id', $order->id);
    }

    /**
     * Display the specified sale.
     */
    public function show(Order $sale)
    {
        $sale->load(['customer', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified sale.
     */
    public function edit(Order $sale)
    {
        $sale->load(['customer', 'items.product']);
        $products = Product::where('stock', '>', 0)->get();
        $customers = Customer::all();
        return view('sales.edit', compact('sale', 'products', 'customers'));
    }

    /**
     * Update the specified sale in storage.
     */
    public function update(Request $request, Order $sale)
    {
        // Implement update logic if needed
        return redirect()->route('sales.index')->with('success', 'Sale updated successfully');
    }

    /**
     * Remove the specified sale from storage.
     */
    public function destroy(Order $sale)
    {
        // Implement delete logic if needed
        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully');
    }

    /**
     * Generate and download the sale invoice.
     */
    public function invoice(Order $sale)
    {
        $sale->load(['customer', 'items.product']);
        $pdf = Pdf::loadView('sales.invoice', compact('sale'));
        return $pdf->stream('invoice-' . $sale->invoice_number . '.pdf');
    }

    /**
     * Print the sale receipt.
     */
    public function print(Order $sale)
    {
        $sale->load(['customer', 'items.product']);
        return view('sales.print', compact('sale'));
    }
} 