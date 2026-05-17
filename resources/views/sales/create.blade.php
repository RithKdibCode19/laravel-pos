@extends('master_page.layout')

@section('title', 'New Sale')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Create New Sale</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sales.store') }}" method="POST">
                @csrf

                {{-- Customer Selection --}}
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select class="form-select" id="customer_id" name="customer_id" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Product Selection (Basic example, will need more complex JS for adding multiple items) --}}
                <div class="mb-3">
                    <label for="product_id" class="form-label">Product</label>
                    <select class="form-select" id="product_id" name="items[0][product_id]" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->stock }} in stock)</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="items[0][quantity]" min="1" value="1" required>
                </div>

                {{-- Payment Method --}}
                 <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Create Sale</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- You'll likely need JavaScript here to handle adding multiple products dynamically --}}
@endpush 