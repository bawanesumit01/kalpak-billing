@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-5 align-self-center">
                    <h4 class="page-title">Invoices</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Invoices</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">


            {{-- Flash --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            {{-- ── FILTER CARD ── --}}
            <div class="card">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('billing.invoice-list') }}" id="filterForm">
                        <div class="row align-items-end">

                            <div class="col-md-2">
                                <label class="small font-weight-bold mb-1">From Date</label>
                                <input type="date" name="from_date" class="form-control form-control-sm"
                                    value="{{ $fromDate }}">
                            </div>

                            <div class="col-md-2">
                                <label class="small font-weight-bold mb-1">To Date</label>
                                <input type="date" name="to_date" class="form-control form-control-sm"
                                    value="{{ $toDate }}">
                            </div>

                            @if (session('role') === 'admin')
                                <div class="col-md-2">
                                    <label class="small font-weight-bold mb-1">Store</label>
                                    <select name="store_id" class="form-control form-control-sm">
                                        <option value="0">All Stores</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}"
                                                {{ $selectedStore == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-3">
                                <label class="small font-weight-bold mb-1">Search</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Invoice no / customer / phone" value="{{ $search }}">
                            </div>

                            <div class="col-md-3 d-flex mt-2 mt-md-0 gap-2">
                                <button type="submit" class="btn btn-primary btn-sm mr-2">
                                    <i class="mdi mdi-magnify"></i> Search
                                </button>
                                <a href="{{ route('billing.invoice-list') }}" class="btn btn-secondary btn-sm mr-2">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- ── TABLE CARD ── --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="scroll_hor" class="table table-striped table-bordered display nowrap" width="100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    @if (session('role') === 'admin')
                                        <th>Store</th>
                                    @endif
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Subtotal (₹)</th>
                                    <th>Discount (₹)</th>
                                    <th>Total (₹)</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong class="text-primary">#{{ $inv->invoice_no }}</strong></td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($inv->created_at)->format('d M Y') }}
                                            <br><small
                                                class="text-muted">{{ \Carbon\Carbon::parse($inv->created_at)->format('h:i A') }}</small>
                                        </td>
                                        @if (session('role') === 'admin')
                                            <td>
                                                <span
                                                    class="badge bg-light border text-black">{{ $inv->store->name ?? '-' }}</span>
                                            </td>
                                        @endif
                                        <td>{{ $inv->customer_name ?: 'Walk-in' }}</td>
                                        <td>{{ $inv->customer_phone ?: '-' }}</td>
                                        <td class="text-right">{{ number_format($inv->subtotal, 2) }}</td>
                                        <td class="text-right text-danger">
                                            {{ number_format($inv->discount_total + $inv->invoice_discount_amount, 2) }}
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ number_format($inv->total_amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $modeClass = match ($inv->payment_mode) {
                                                    'Cash' => 'bg-success',
                                                    'UPI' => 'bg-info',
                                                    'Card' => 'bg-primary',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $modeClass }}">{{ $inv->payment_mode }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $modeClass = match ($inv->status) {
                                                    'Paid' => 'bg-success',
                                                    'Pending' => 'bg-warning',
                                                };
                                            @endphp
                                            <span class="badge {{ $modeClass }}">{{ $inv->status }}</span>
                                        </td>
                                        <td class="text-center" style="white-space:nowrap;">
                                            @if($inv->status == 'Pending')
                                            <form action="{{ route('billing.markPaid', $inv->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-success mr-1" title="Mark as Paid">
                                                    <i class="mdi mdi-check"></i> Mark as Paid
                                                </button>
                                            </form>
                                            @endif
                                            <a href="{{ route('billing.invoice', $inv->id) }}"
                                                class="btn btn-xs btn-info mr-1" title="View" target="_blank">
                                                <i class="mdi mdi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ session('role') === 'admin' ? 12 : 11 }}"
                                            class="text-center text-muted py-5">
                                            <i class="mdi mdi-file-document-outline"
                                                style="font-size:36px;display:block;margin-bottom:8px;"></i>
                                            No invoices found for the selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($invoices->count() > 0)
                                <tfoot class="thead-light">
                                    <tr>
                                        <td colspan="{{ session('role') === 'admin' ? 9 : 8 }}"
                                            class="text-right font-weight-bold">Grand Total:</td>
                                        <td class="text-right font-weight-bold text-success">
                                            ₹ {{ number_format($invoices->sum('total_amount'), 2) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            {{-- /card --}}

        </div>
    </div>
@endsection

@push('scripts')
    <script>
    $(document).on('click', '.mark-paid-btn', function () {
        let id = $(this).data('id');
        alert('Confirm!');
        $.ajax({
            url: '/billing/mark-paid/' + id,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function (res) {
                alert('Marked as Paid');
                location.reload();
            }
        });
    });
        // Auto-submit on date change
        document.querySelectorAll('input[type="date"]').forEach(function(el) {
            el.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    </script>
@endpush
