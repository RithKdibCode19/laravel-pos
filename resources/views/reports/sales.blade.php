@extends('master_page.master')

@section('title', 'Sales Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('messages.reports') }}</h2>
        <div class="d-flex align-items-center">
    <form method="GET" action="{{ route('reports.sales') }}" class="d-flex align-items-end gap-3">
        <div>
            <label for="startDate" class="form-label small mb-1">Start Date</label>
            <input type="date" class="form-control" id="startDate" name="start_date" value="{{ request('start_date') }}">
        </div>
        <div>
            <label for="endDate" class="form-label small mb-1">End Date</label>
            <input type="date" class="form-control" id="endDate" name="end_date" value="{{ request('end_date') }}">
        </div>
        <div>
            <label for="category" class="form-label small mb-1">Category</label>
            <select class="form-select" id="category" name="category">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-search"></i> Filter
            </button>
        </div>
        <div>
            <a href="{{ route('reports.sales.export', [
                'start_date' => request('start_date'),
                'end_date' => request('end_date'),
                'category' => request('category')
            ]) }}" class="btn btn-success">
                <i class="bx bx-download me-2"></i> Export Data
            </a>
        </div>
    </form>
</div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ __('messages.sales_data') }}</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('messages.order_id') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.customer') }}</th>
                            <th>{{ __('messages.product_name') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.price_per_item') }}</th>
                            <th>{{ __('messages.item_subtotal') }}</th>
                            <th>{{ __('messages.order_total') }}</th>
                            <th>{{ __('messages.payment_method') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($viewData as $sale)
                        <tr>
                            <td>#{{ $sale['order_id'] }}</td>
                            <td>{{ $sale['date'] }}</td>
                            <td>{{ $sale['customer_name'] }}</td>
                            <td>{{ $sale['product_name'] }}</td>
                            <td>{{ $sale['quantity'] }}</td>
                            <td>${{ number_format($sale['price_per_item'], 2) }}</td>
                            <td>${{ number_format($sale['item_subtotal'], 2) }}</td>
                            <td>${{ number_format($sale['order_total'], 2) }}</td>
                            <td>{{ $sale['payment_method'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No sales data available for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRangeSelect = document.getElementById('dateRange');
    const customDateContainer = document.getElementById('customDateContainer');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const categorySelect = document.getElementById('category');
    const exportButton = document.querySelector('.btn-success');
    const exportUrl = '{{ route('reports.sales.export') }}';

    // Show/hide custom date input based on selection
    function toggleCustomDateInput() {
        if (dateRangeSelect.value === 'custom') {
            customDateContainer.style.display = 'block';
        } else {
            customDateContainer.style.display = 'none';
        }
    }

    // Initial state
    toggleCustomDateInput();

    // Date range change handler
    dateRangeSelect.addEventListener('change', function() {
        toggleCustomDateInput();
        updateUrls();
    });

    // Category change handler
    categorySelect.addEventListener('change', function() {
        updateUrls();
    });

    // Custom date change handlers
    startDateInput.addEventListener('change', function() {
        // Ensure end date is not before start date
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
        updateUrls();
    });

    endDateInput.addEventListener('change', function() {
        // Ensure start date is not after end date
        if (startDateInput.value && startDateInput.value > this.value) {
            startDateInput.value = this.value;
        }
        updateUrls();
    });

    function updateUrls() {
        const selectedDateRange = dateRangeSelect.value;
        let url = '{{ route("reports.sales") }}?date_range=' + selectedDateRange;

        if (selectedDateRange === 'custom') {
            if (startDateInput.value) {
                url += '&start_date=' + startDateInput.value;
            }
            if (endDateInput.value) {
                url += '&end_date=' + endDateInput.value;
            }
        }

        // Add category parameter
        if (categorySelect.value) {
            url += '&category=' + categorySelect.value;
        }

        window.location.href = url;

        // Update export button URL
        let exportUrlWithParams = exportUrl + '?date_range=' + selectedDateRange;
        if (selectedDateRange === 'custom') {
            if (startDateInput.value) {
                exportUrlWithParams += '&start_date=' + startDateInput.value;
            }
            if (endDateInput.value) {
                exportUrlWithParams += '&end_date=' + endDateInput.value;
            }
        }
        // Add category to export URL
        if (categorySelect.value) {
            exportUrlWithParams += '&category=' + categorySelect.value;
        }
        exportButton.href = exportUrlWithParams;
    }

    // Ensure the export button URL is correct on page load
    let exportUrlWithParams = exportUrl + '?date_range=' + dateRangeSelect.value;
    if (dateRangeSelect.value === 'custom') {
        if (startDateInput.value) {
            exportUrlWithParams += '&start_date=' + startDateInput.value;
        }
        if (endDateInput.value) {
            exportUrlWithParams += '&end_date=' + endDateInput.value;
        }
    }
    // Add category to initial export URL
    if (categorySelect.value) {
        exportUrlWithParams += '&category=' + categorySelect.value;
    }
    exportButton.href = exportUrlWithParams;
});
</script>
@endpush
