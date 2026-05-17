<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $categoryId;

    public function __construct($startDate = null, $endDate = null, $categoryId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->categoryId = $categoryId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Fetch orders with customer and order items relationships
        $query = Order::with(['customer', 'items.product'])
            ->where('status', 'completed');

        if ($this->startDate) {
            // Ensure timezone is consistent for comparison
            $query->where(DB::raw('DATE(CONVERT_TZ(created_at, @@session.time_zone, "+00:00"))'), '>=', $this->startDate->format('Y-m-d'));
        }

        if ($this->endDate) {
             // Ensure timezone is consistent for comparison
            $query->where(DB::raw('DATE(CONVERT_TZ(created_at, @@session.time_zone, "+00:00"))'), '<=', $this->endDate->format('Y-m-d'));
        }

        if ($this->categoryId) {
            $query->whereHas('items.product', function($q) {
                $q->where('category_id', $this->categoryId);
            });
        }

        $orders = $query->orderBy('created_at')->get();

        // Prepare data for export, flattening order items
        $exportData = collect();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if (!$this->categoryId || $item->product->category_id == $this->categoryId) {
                    $exportData->push([
                        'Order ID' => $order->id,
                        'Date' => Carbon::parse($order->created_at)->format('M d, Y H:i'),
                        'Customer Name' => optional($order->customer)->name ?? 'N/A',
                        'Product Name' => $item->product->name,
                        'Quantity' => $item->quantity,
                        'Price Per Item' => number_format($item->price, 2, '.', ''),
                        'Item Subtotal' => number_format($item->subtotal, 2, '.', ''),
                        'Order Total' => number_format($order->total, 2, '.', ''),
                        'Payment Method' => $order->payment_method,
                    ]);
                }
            }
        }

        return $exportData;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Order ID',
            'Date',
            'Customer Name',
            'Product Name',
            'Quantity',
            'Price Per Item',
            'Item Subtotal',
            'Order Total',
            'Payment Method',
        ];
    }

    public function salesExport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
        $categoryId = $request->input('category_id');

        return Excel::download(
            new SalesExport($startDate, $endDate, $categoryId),
            'sales_report_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
