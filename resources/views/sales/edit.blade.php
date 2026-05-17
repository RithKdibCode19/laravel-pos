@extends('master_page.master')

@section('title', 'Edit Sale')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Sale #{{ $sale->order_id }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales.update', $sale->order_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_name">Customer Name</label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" 
                                           value="{{ old('customer_name', $sale->customer_name) }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select class="form-control @error('payment_method') is-invalid @enderror" 
                                            id="payment_method" name="payment_method" required>
                                        <option value="cash" {{ $sale->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="card" {{ $sale->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                                        <option value="transfer" {{ $sale->payment_method == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="items-container">
                                    @foreach($sale->items as $item)
                                    <tr>
                                        <td>
                                            <select class="form-control product-select" name="items[{{ $loop->index }}][product_id]" required>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                            data-price="{{ $product->price }}"
                                                            {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity-input" 
                                                   name="items[{{ $loop->index }}][quantity]" 
                                                   value="{{ $item->quantity }}" min="1" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control price-input" 
                                                   name="items[{{ $loop->index }}][price]" 
                                                   value="{{ $item->price }}" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control subtotal" 
                                                   value="{{ $item->quantity * $item->price }}" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-item">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success btn-sm" id="add-item">
                                <i class="bx bx-plus"></i> Add Item
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="form-group">
                                    <label for="total">Total Amount</label>
                                    <input type="number" class="form-control" id="total" name="total" 
                                           value="{{ $sale->order_total }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Update Sale
                            </button>
                            <a href="{{ route('reports.sales') }}" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    let itemCount = {{ count($sale->items) }};

    // Function to calculate subtotal
    function calculateSubtotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const subtotal = quantity * price;
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        calculateTotal();
    }

    // Function to calculate total
    function calculateTotal() {
        const subtotals = Array.from(document.querySelectorAll('.subtotal'))
            .map(input => parseFloat(input.value) || 0);
        const total = subtotals.reduce((sum, value) => sum + value, 0);
        document.getElementById('total').value = total.toFixed(2);
    }

    // Add new item row
    addButton.addEventListener('click', function() {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select class="form-control product-select" name="items[${itemCount}][product_id]" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" class="form-control quantity-input" 
                       name="items[${itemCount}][quantity]" value="1" min="1" required>
            </td>
            <td>
                <input type="number" class="form-control price-input" 
                       name="items[${itemCount}][price]" value="0.00" step="0.01" required>
            </td>
            <td>
                <input type="number" class="form-control subtotal" value="0.00" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        `;
        container.appendChild(row);
        itemCount++;

        // Set initial price from selected product
        const select = row.querySelector('.product-select');
        const priceInput = row.querySelector('.price-input');
        priceInput.value = select.options[select.selectedIndex].dataset.price;
        calculateSubtotal(row);
    });

    // Remove item row
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr').remove();
            calculateTotal();
        }
    });

    // Update price when product is selected
    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('tr');
            const price = e.target.options[e.target.selectedIndex].dataset.price;
            row.querySelector('.price-input').value = price;
            calculateSubtotal(row);
        }
    });

    // Calculate subtotal when quantity or price changes
    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('price-input')) {
            calculateSubtotal(e.target.closest('tr'));
        }
    });
});
</script>
@endpush 