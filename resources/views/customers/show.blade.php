@extends('master_page.master')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Customer Details</h2>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back"></i> Back to Customers
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $customer->name }}</p>
                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                    <p><strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>City:</strong> {{ $customer->city ?? 'N/A' }}</p>
                    <p><strong>State:</strong> {{ $customer->state ?? 'N/A' }}</p>
                    <p><strong>Postal Code:</strong> {{ $customer->postal_code ?? 'N/A' }}</p>
                    <p><strong>Country:</strong> {{ $customer->country ?? 'N/A' }}</p>
                </div>
            </div>

            <h5 class="mt-4">Recent Orders</h5>
            @if($customer->orders->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->orders->sortByDesc('created_at') as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status_color }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p>No orders found for this customer.</p>
            @endif

        </div>
    </div>
</div>
@endsection 