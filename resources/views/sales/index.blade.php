@extends('master_page.master')

@section('title', 'Sales')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Products List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title">Products</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select" id="categoryFilter" style="width: 200px;">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="productList">
                        @foreach($products as $product)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 product-card"
                                data-product-id="{{ $product->id }}"
                                data-category-id="{{ $product->category_id }}">
                                <div class="d-flex align-items-center p-3">
                                    <div class="flex-grow-1 me-3">
                                        <h6 class="card-title">{{ $product->name }}</h6>
                                        <p class="card-text text-muted">Stock: {{ $product->stock }}</p>
                                        <p class="card-text">${{ number_format($product->price, 2) }}</p>
                                        <button class="btn btn-primary btn-sm add-to-cart"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}"
                                                data-product-price="{{ $product->price }}">
                                            Add to Cart
                                        </button>
                                    </div>
                                    <div>
                                        @if($product->image)
                                            <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" width="80" class="img-fluid rounded">
                                        @else
                                            <div style="width: 80px; height: 80px; background-color: #e9ecef; display: flex; justify-content: center; align-items: center;" class="rounded text-muted">No Image</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Cart</h5>
                    <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="customerSearchInput" class="form-label">Customer</label>
                            <input type="text" class="form-control" id="customerSearchInput" placeholder="Search or select customer" autocomplete="off">
                            <input type="hidden" id="customerId" name="customer_id" required>
                            <div id="customerSearchResults" class="list-group position-absolute" style="z-index: 1000; width: 100%;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Items</label>
                            <div id="cartItems">
                                <!-- Cart items will be added here dynamically -->
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <h4>Total: $<span id="cartTotal">0.00</span></h4>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Complete Sale</button>
                            <button type="button" class="btn btn-secondary" id="printCurrentSale" disabled>
                                <i class="bx bx-printer"></i> Print
                            </button>
                            <button type="button" class="btn btn-danger" id="clearCart">
                                Clear Cart
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title">Recent Sales</h5>
                        <button type="button" class="btn btn-sm btn-secondary" id="printRecentSales">
                            <i class="bx bx-printer"></i> Print All
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="recentSalesTable">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th>{{ __('messages.amount') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    @if ($order->customer)
                                        <td>{{ $order->customer->name }}</td>
                                    @else
                                        <td class="text-muted">No Customer</td>
                                    @endif
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>{{ $order->created_at->format('M d, H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-secondary print-sale" data-order-id="{{ $order->id }}">
                                            <i class="bx bx-printer"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $recentOrders->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pass customers data from backend to frontend JavaScript
    const allCustomers = @json($customers);

    const allProducts = @json($products);

    document.addEventListener('DOMContentLoaded', function() {
        const cart = [];
        const productSearch = document.getElementById('productSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const saleForm = document.getElementById('saleForm');
        const customerSearchInput = document.getElementById('customerSearchInput');
        const customerIdInput = document.getElementById('customerId');
        const customerSearchResults = document.getElementById('customerSearchResults');
        const clearCartButton = document.getElementById('clearCart');

        // Clear cart functionality
        clearCartButton.addEventListener('click', function() {
            cart.length = 0; // Clear the cart array
            updateCart();
            customerSearchInput.value = ''; // Clear customer search
            customerIdInput.value = ''; // Clear customer ID
        });

        // Add to cart functionality
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const productPrice = parseFloat(this.dataset.productPrice);

                const existingItem = cart.find(item => item.productId === productId);
                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    cart.push({
                        productId,
                        productName,
                        productPrice,
                        quantity: 1
                    });
                }

                updateCart();
            });
        });

        // Update cart display
        function updateCart() {
            cartItems.innerHTML = '';
            let total = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.productPrice * item.quantity;
                total += itemTotal;

                const itemElement = document.createElement('div');
                itemElement.className = 'cart-item mb-2';
                itemElement.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <input type="hidden" name="items[${index}][product_id]" value="${item.productId}">
                            <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                            <strong>${item.productName}</strong>
                            <br>
                            <div class="d-flex align-items-center mt-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary decrease-qty" data-index="${index}">
                                    <i class="bx bx-minus"></i>
                                </button>
                                <input type="number" class="form-control form-control-sm mx-2 quantity-input" 
                                    style="width: 60px; text-align: center;" 
                                    value="${item.quantity}" 
                                    min="1" 
                                    data-index="${index}">
                                <button type="button" class="btn btn-sm btn-outline-secondary increase-qty" data-index="${index}">
                                    <i class="bx bx-plus"></i>
                                </button>
                                <small class="ms-2">$${item.productPrice.toFixed(2)} each</small>
                            </div>
                        </div>
                        <div>
                            <span class="me-2">$${itemTotal.toFixed(2)}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-item" data-index="${index}">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                cartItems.appendChild(itemElement);
            });

            cartTotal.textContent = total.toFixed(2);

            // Add remove functionality
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.dataset.index;
                    cart.splice(index, 1);
                    updateCart();
                });
            });

            // Add quantity input functionality
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const index = this.dataset.index;
                    const newQuantity = parseInt(this.value);
                    if (newQuantity > 0) {
                        cart[index].quantity = newQuantity;
                        updateCart();
                    } else {
                        this.value = cart[index].quantity; // Reset to previous value if invalid
                    }
                });

                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.blur();
                    }
                });
            });

            // Add increase/decrease quantity functionality
            document.querySelectorAll('.increase-qty').forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.dataset.index;
                    cart[index].quantity++;
                    updateCart();
                });
            });

            document.querySelectorAll('.decrease-qty').forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.dataset.index;
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                        updateCart();
                    }
                });
            });

            // Update print button state
            updatePrintButton();
        }

        // Customer search functionality
        customerSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            customerSearchResults.innerHTML = ''; // Clear previous results

            if (searchTerm.length > 0) {
                // Filter the allCustomers array based on the search term
                const filteredCustomers = allCustomers.filter(customer =>
                    customer.name.toLowerCase().includes(searchTerm) ||
                    (customer.email && customer.email.toLowerCase().includes(searchTerm)) ||
                    (customer.phone && customer.phone.includes(searchTerm))
                );

                filteredCustomers.forEach(customer => {
                    const resultItem = document.createElement('a');
                    resultItem.href = '#';
                    resultItem.className = 'list-group-item list-group-item-action';
                    resultItem.textContent = customer.name;
                    resultItem.addEventListener('click', function(e) {
                        e.preventDefault();
                        customerSearchInput.value = customer.name;
                        customerIdInput.value = customer.id;
                        customerSearchResults.innerHTML = ''; // Hide results after selection
                    });
                    customerSearchResults.appendChild(resultItem);
                });
            }
        });

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!customerSearchInput.contains(e.target) && !customerSearchResults.contains(e.target)) {
                customerSearchResults.innerHTML = '';
            }
        });

        // Product search and category filter functionality
        function filterProducts() {
            const searchTerm = productSearch.value.toLowerCase();
            const selectedCategory = categoryFilter.value;

            document.querySelectorAll('.product-card').forEach(card => {
                const productColumn = card.closest('.col-md-4');
                if (!productColumn) return;
                const productName = card.querySelector('.card-title').textContent.toLowerCase();
                const productCategory = card.dataset.categoryId;

                const matchesSearch = productName.includes(searchTerm);
                const matchesCategory = !selectedCategory || productCategory === selectedCategory;

                productColumn.style.display = matchesSearch && matchesCategory ? '' : 'none';
            });
        }

        productSearch.addEventListener('input', filterProducts);
        categoryFilter.addEventListener('change', function() {
            productSearch.value = ''; // Clear the search input
            filterProducts();
        });

        // Form submission
        saleForm.addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Please add items to cart');
            }
        });

        // Handle successful sale
        @if(session('success'))
            const orderId = {{ session('order_id') }};
            window.open(`/sales/${orderId}/invoice`, '_blank');
        @endif

        // Print functionality
        const printCurrentSale = document.getElementById('printCurrentSale');
        const printRecentSales = document.getElementById('printRecentSales');

        // Enable print button when items are in cart
        function updatePrintButton() {
            printCurrentSale.disabled = cart.length === 0;
        }

        // Print current sale
        printCurrentSale.addEventListener('click', function() {
            const printWindow = window.open('', '_blank');
            const cartContent = cartItems.innerHTML;
            const customerName = document.getElementById('customer').options[document.getElementById('customer').selectedIndex].text;

            printWindow.document.write(`
                <html>
                    <head>
                        <title>Current Sale</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            .header { text-align: center; margin-bottom: 20px; }
                            .items { margin-bottom: 20px; }
                            .total { font-size: 1.2em; font-weight: bold; }
                            table { width: 100%; border-collapse: collapse; }
                            th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>Current Sale</h2>
                            <p>Customer: ${customerName}</p>
                            <p>Date: ${new Date().toLocaleString()}</p>
                        </div>
                        <div class="items">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${cart.map(item => `
                                        <tr>
                                            <td>${item.productName}</td>
                                            <td>${item.quantity}</td>
                                            <td>$${item.productPrice.toFixed(2)}</td>
                                            <td>$${(item.quantity * item.productPrice).toFixed(2)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                        <div class="total">
                            Total: $${cartTotal.textContent}
                        </div>
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        });

        // Print recent sales
        printRecentSales.addEventListener('click', function() {
            const printWindow = window.open('', '_blank');
            const table = document.getElementById('recentSalesTable');

            printWindow.document.write(`
                <html>
                    <head>
                        <title>Recent Sales Report</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            .header { text-align: center; margin-bottom: 20px; }
                            table { width: 100%; border-collapse: collapse; }
                            th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>Recent Sales Report</h2>
                            <p>Generated on: ${new Date().toLocaleString()}</p>
                        </div>
                        ${table.outerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        });

        // Print individual sale
        document.querySelectorAll('.print-sale').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                window.open(`/sales/${orderId}/invoice`, '_blank');
            });
        });
    });
</script>
@endpush
