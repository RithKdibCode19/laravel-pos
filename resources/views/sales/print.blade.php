<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.sales') }} - {{ $sale->invoice_number }}</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .invoice-table th {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>POS System</h1>
        <h2>{{ __('messages.sales') }}</h2>
    </div>

    <div class="invoice-details">
        <p><strong>{{ __('messages.invoice_number') }}:</strong> {{ $sale->invoice_number }}</p>
        <p><strong>{{ __('messages.date') }}:</strong> {{ $sale->created_at->format('Y-m-d H:i:s') }}</p>
        <p><strong>{{ __('messages.customer') }}:</strong> {{ $sale->customer_name }}</p>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>{{ __('messages.product') }}</th>
                <th>{{ __('messages.quantity') }}</th>
                <th>{{ __('messages.price') }}</th>
                <th>{{ __('messages.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td>${{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <p><strong>{{ __('messages.subtotal') }}:</strong> ${{ number_format($sale->subtotal, 2) }}</p>
        <p><strong>{{ __('messages.tax') }}:</strong> ${{ number_format($sale->tax, 2) }}</p>
        <p><strong>{{ __('messages.total') }}:</strong> ${{ number_format($sale->total, 2) }}</p>
    </div>

    <div class="footer">
        <p>{{ __('messages.thank_you') }}</p>
        <p>{{ __('messages.printed_at') }}: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()">{{ __('messages.print') }}</button>
    </div>
</body>
</html> 