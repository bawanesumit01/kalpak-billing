@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-5 align-self-center">
                    <h4 class="page-title">Admin Dashboard</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="col-7 align-self-center">
                    <div class="d-flex no-block justify-content-end align-items-center">
                        <div class="me-2">
                            <div class="lastmonth"></div>
                        </div>
                        <div class="">
                            <small>LAST MONTH</small>
                            <h4 class="text-info mb-0 font-medium">$58,256</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            {{-- ======================== --}}
            {{-- STATS CARDS              --}}
            {{-- ======================== --}}
            <div class="row">

                {{-- Total Products --}}
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row gap-2">
                                <div class="round round-lg align-self-center round-info">
                                    <i class="mdi mdi-package-variant"></i>
                                </div>
                                <div class="ml-2 align-self-center">
                                    <h3 class="mb-0 font-weight-medium"> {{ $totalProducts }}</h3>
                                    <h5 class="text-muted mb-0">Total Products</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Staff --}}
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row gap-2">
                                <div class="round round-lg align-self-center round-success">
                                    <i class="mdi mdi-account-multiple"></i>
                                </div>
                                <div class="ml-2 align-self-center">
                                    <h3 class="mb-0 font-weight-medium"> {{ $totalStaff }}</h3>
                                    <h5 class="text-muted mb-0">Total Staff</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Stores --}}
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row gap-2">
                                <div class="round round-lg align-self-center round-danger">
                                    <i class="mdi mdi-store"></i>
                                </div>
                                <div class="ml-2 align-self-center">
                                    <h3 class="mb-0 font-weight-medium"> {{ $totalStores }}</h3>
                                    <h5 class="text-muted mb-0">Total Stores</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            {{-- END STATS CARDS --}}


            {{-- ======================== --}}
            {{-- STORE WISE SALES         --}}
            {{-- ======================== --}}
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-3">Store Wise Sales</h4>
                </div>

                @forelse($storeSales as $store)
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-themecolor">{{ $store->name }}</h5>
                                <h6 class="text-muted">Total Sales</h6>
                                <h3 class="font-weight-bold">
                                    ₹ {{ number_format($store->total, 2) }}
                                </h3>
                                <small class="text-success">
                                    <i class="mdi mdi-calendar-today"></i>
                                    Today: ₹ {{ number_format(optional($todaySales->get($store->id ?? 0))->total ?? 0, 2) }}
                                </small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">No store sales data found.</div>
                    </div>
                @endforelse

            </div>
            {{-- END STORE WISE SALES --}}


            {{-- ======================== --}}
            {{-- SALES CHART              --}}
            {{-- ======================== --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Store Wise Sales Chart</h4>
                            <canvas id="salesChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END CHART --}}


            {{-- ======================== --}}
            {{-- LOW STOCK ALERTS         --}}
            {{-- ======================== --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">
                                    <i class="mdi mdi-alert text-warning"></i> Low Stock Alerts
                                </h4>
                                <a href="{{ route('products.exportCsv') }}" class="btn btn-sm btn-success">
                                    <i class="mdi mdi-file-excel"></i> Export Products (CSV)
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Store</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lowStock as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->store_name }}</td>
                                                <td>
                                                    @if ($item->stock == 0)
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    @else
                                                        <span class="badge bg-warning">Low ({{ $item->stock }})</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-success">
                                                    <i class="mdi mdi-check-circle"></i> All products are well stocked!
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            {{-- END LOW STOCK --}}

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartLabels = @json($chartLabels);
        const chartTotals = @json($chartTotals);

        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Total Sales (₹)',
                    data: chartTotals,
                    backgroundColor: 'rgba(41, 98, 255, 0.8)',
                    borderColor: '#2962FF',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ₹ ' + Number(context.raw).toLocaleString('en-IN', {
                                    minimumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹ ' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
