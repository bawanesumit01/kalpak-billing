@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-5 align-self-center">
                    <h4 class="page-title">Other Product Sales</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Other Product Sales</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            {{-- ===================== --}}
            {{-- FILTER CARD           --}}
            {{-- ===================== --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Filter Sales</h4>

                            <form method="GET" action="{{ route('other-product-sales.index') }}">
                                <div class="row align-items-end">

                                    {{-- Store --}}
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">Store</label>
                                            <select name="store_id" class="form-control">
                                                <option value="">All Stores</option>
                                                @foreach ($stores as $store)
                                                    <option value="{{ $store->id }}"
                                                        {{ $selectedStore == $store->id ? 'selected' : '' }}>
                                                        {{ $store->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- From Date --}}
                                    <div class="col-md-2">
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">From Date</label>
                                            <input type="date" name="from_date" class="form-control"
                                                value="{{ $fromDate }}" required>
                                        </div>
                                    </div>

                                    {{-- To Date --}}
                                    <div class="col-md-2">
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">To Date</label>
                                            <input type="date" name="to_date" class="form-control"
                                                value="{{ $toDate }}" required>
                                        </div>
                                    </div>

                                    {{-- Search --}}
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">Search</label>
                                            <input type="text" id="sales_search" class="form-control"
                                                placeholder="Invoice #, customer, product, staff...">
                                        </div>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="col-md-2">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="mdi mdi-filter"></i> Filter
                                            </button>
                                            <a href="{{ route('other-product-sales.index') }}" class="btn btn-secondary">
                                                <i class="mdi mdi-refresh"></i> Reset
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== --}}
            {{-- SUMMARY TOTALS        --}}
            {{-- ===================== --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="round round-lg align-self-center round-info">
                                    <i class="mdi mdi-cart"></i>
                                </div>
                                <div class="ml-2 align-self-center">
                                    <h3 class="mb-0 font-weight-medium">{{ $sales->count() }}</h3>
                                    <h5 class="text-muted mb-0">Total Records</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="round round-lg align-self-center round-success">
                                    <i class="mdi mdi-package-variant"></i>
                                </div>
                                <div class="ml-2 align-self-center">
                                    <h3 class="mb-0 font-weight-medium">{{ $totalQty }}</h3>
                                    <h5 class="text-muted mb-0">Total Qty Sold</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="round round-lg align-self-center round-danger">
                                    <i class="mdi mdi-currency-inr"></i>
                                </div>
                                <div class="ml-2 align-self-center">
                                    <h3 class="mb-0 font-weight-medium">₹ {{ number_format($totalAmount, 2) }}</h3>
                                    <h5 class="text-muted mb-0">Total Amount</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== --}}
            {{-- SALES TABLE           --}}
            {{-- ===================== --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="card-title mb-3">
                                Sales from
                                <span
                                    class="text-themecolor">{{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}</span>
                                to
                                <span class="text-themecolor">{{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</span>
                            </h4>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="sales_table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Store</th>
                                            <th>Staff</th>
                                            <th>Customer</th>
                                            <th>Product</th>
                                            <th>Code</th>
                                            <th>Qty</th>
                                            <th>Price (₹)</th>
                                            <th>Total (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sales as $sale)
                                            <tr data-invoice="{{ strtolower($sale->invoice_no) }}"
                                                data-customer="{{ strtolower($sale->customer_name ?? '') }}"
                                                data-product="{{ strtolower($sale->product_name ?? '') }}"
                                                data-staff="{{ strtolower($sale->staff_name ?? '') }}">

                                                <td>#{{ $sale->invoice_no }}</td>
                                                <td>{{ \Carbon\Carbon::parse($sale->invoice_date)->format('d-M-Y H:i') }}
                                                </td>
                                                <td>{{ $sale->store_name ?? '-' }}</td>
                                                <td>{{ $sale->staff_name ?? 'System' }}</td>
                                                <td>
                                                    @if ($sale->customer_name)
                                                        {{ $sale->customer_name }}
                                                    @elseif($sale->customer_phone)
                                                        Phone: {{ $sale->customer_phone }}
                                                    @else
                                                        <span class="text-muted">Walk-in</span>
                                                    @endif
                                                </td>
                                                <td>{{ $sale->product_name }}</td>
                                                <td>{{ $sale->product_code }}</td>
                                                <td>{{ (int) $sale->qty }}</td>
                                                <td>₹ {{ number_format($sale->unit_price, 2) }}</td>
                                                <td class="font-weight-bold text-success">
                                                    ₹ {{ number_format($sale->total_amount, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted py-4">
                                                    <i class="mdi mdi-information-outline"></i>
                                                    No other product sales found for selected filters.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                    {{-- Table Footer Totals --}}
                                    @if ($sales->count() > 0)
                                        <tfoot class="thead-dark">
                                            <tr>
                                                <th colspan="7" class="text-right">Total:</th>
                                                <th>{{ $totalQty }}</th>
                                                <th></th>
                                                <th>₹ {{ number_format($totalAmount, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    @endif

                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Live search across table rows
        document.getElementById('sales_search').addEventListener('input', function() {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('#sales_table tbody tr').forEach(row => {
                const invoice = row.getAttribute('data-invoice') || '';
                const customer = row.getAttribute('data-customer') || '';
                const product = row.getAttribute('data-product') || '';
                const staff = row.getAttribute('data-staff') || '';
                const invoiceCell = row.querySelector('td:first-child');
                const displayed = invoiceCell ? invoiceCell.textContent.trim().toLowerCase() : '';

                row.style.display = (
                    !term ||
                    invoice.includes(term) ||
                    displayed.includes(term) ||
                    customer.includes(term) ||
                    product.includes(term) ||
                    staff.includes(term)
                ) ? '' : 'none';
            });
        });
    </script>
@endpush
