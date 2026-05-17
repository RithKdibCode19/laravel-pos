@extends('master_page.layout')

@section('content')
<div class="container-fluid py-4">
    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">{{ __('messages.todays_sales') }}</h6>
                            <h3 class="mb-0">${{ number_format($totalSales ?? 0, 2) }}</h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">{{ __('messages.total_products') }}</h6>
                            <h3 class="mb-0">{{ $totalProducts ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-box fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">{{ __('messages.total_customers') }}</h6>
                            <h3 class="mb-0">{{ $totalCustomers ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">{{ __('messages.total_orders') }}</h6>
                            <h3 class="mb-0">{{ $totalOrders ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-clipboard-list fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">{{ __('messages.low_stock_items') }}</h6>
                            <h3 class="mb-0">{{ $lowStockProducts->count() ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                    <ul class="list-group list-group-flush mt-3">
                        @forelse($lowStockProducts as $product)
                            <li class="list-group-item text-dark d-flex justify-content-between align-items-center">
                                {{ $product->name }}
                                <span class="badge bg-danger rounded-pill">{{ $product->stock }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-dark">{{ __('messages.no_low_stock') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-3">
        <!-- Recent Sales -->
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('messages.recent_sales') }}</h5>
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-primary">{{ __('messages.view_all') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.invoice_number') }}</th>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th>{{ __('messages.amount') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales ?? [] as $sale)
                                <tr>
                                    <td>{{ $sale->invoice_number }}</td>
                                    <td>{{ $sale->customer_name }}</td>
                                    <td>${{ number_format($sale->total, 2) }}</td>
                                    <td>{{ $sale->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{ __('messages.view') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('messages.no_recent_sales') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">{{ __('messages.quick_actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>{{ __('messages.new_sale') }}
                        </a>
                        <a href="{{ route('products.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-box me-2"></i>{{ __('messages.add_product') }}
                        </a>
                        <a href="{{ route('customers.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>{{ __('messages.add_customer') }}
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-bar me-2"></i>{{ __('messages.view_reports') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Products -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">{{ __('messages.popular_products') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.product') }}</th>
                                    <th>{{ __('messages.total_sales') }}</th>
                                    <th>{{ __('messages.quantity_sold') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularProducts ?? [] as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>${{ number_format($product->total_sales, 2) }}</td>
                                    <td>{{ $product->quantity_sold }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('messages.no_data_available') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Customers -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">{{ __('messages.top_customers') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th>{{ __('messages.total_orders') }}</th>
                                    <th>{{ __('messages.total_spent') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularCustomers ?? [] as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->total_orders }}</td>
                                    <td>${{ number_format($customer->total_spent, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('messages.no_data_available') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">{{ __('messages.sales_overview') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .card {
        border: none;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }
    .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
    }
    .badge {
        padding: 0.5em 0.8em;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($salesChartData ?? []);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: salesData.labels || [],
            datasets: [{
                label: 'Daily Sales',
                data: salesData.data || [],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Daily Sales Overview'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
