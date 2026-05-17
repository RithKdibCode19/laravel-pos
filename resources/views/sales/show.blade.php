@extends('master_page.layout')

@section('title', 'Sale Details')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sale Details #{{ $sale->id }}</h5>
            <div>
                <a href="{{ route('sales.print', $sale) }}" class="btn btn-primary me-2">
                    <i class="fas fa-print"></i> Print Receipt
                </a>
                <a href="{{ route('sales.invoice', $sale) }}" class="btn btn-info me-2">
                    <i class="fas fa-file-invoice"></i> Download Invoice
                </a>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Sales
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Customer Information</h6>
                    <p class="mb-1"><strong>Name:</strong> {{ $sale->customer->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $sale->customer->email ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $sale->customer->phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Sale Information</h6>
                    <p class="mb-1"><strong>Date:</strong> {{ $sale->created_at->setTimezone(config('app.timezone'))->format('M d, Y h:i A') }}</p>
                    <p class="mb-1"><strong>Payment Method:</strong> {{ ucfirst($sale->payment_method) }}</p>
                    <p class="mb-1"><strong>Status:</strong> {{ ucfirst($sale->status) }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>${{ number_format($sale->total, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 