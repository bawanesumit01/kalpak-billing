@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-5 align-self-center">
                    <h4 class="page-title">Manage Other Products</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Other Products</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            {{-- Alerts --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="mdi mdi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            {{-- Top Bar --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Other Product List</h4>
                                <a href="{{ route('other-products.create') }}" class="btn btn-sm btn-primary">
                                    <i class="mdi mdi-plus"></i> Add Other Product
                                </a>
                            </div>


                            {{-- Table --}}
                            <div class="table-responsive">
                                <table id="scroll_hor" class="table table-striped table-bordered display nowrap" width="100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Store</th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Price (₹)</th>
                                            <th>GST%</th>
                                            <th>Stock</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($products as $index => $p)
                                            <tr data-code="{{ strtolower($p->product_code) }}"
                                                data-name="{{ strtolower($p->name) }}"
                                                @if ($p->stock <= 5) style="background:#ffe6e6;" @endif>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $p->store_name }}</td>
                                                <td>{{ $p->product_code }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>₹ {{ number_format($p->price, 2) }}</td>
                                                <td>{{ number_format($p->gst_rate, 2) }}%</td>
                                                <td>
                                                    @if ($p->stock == 0)
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    @elseif($p->stock <= 5)
                                                        <span class="badge bg-warning">Low ({{ $p->stock }})</span>
                                                    @else
                                                        <span class="badge bg-success">{{ $p->stock }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('other-products.edit', $p->id) }}"
                                                        class="btn btn-xs btn-info mr-1">
                                                        <i class="mdi mdi-pencil"></i> Edit
                                                    </a>

                                                    <form action="{{ route('other-products.destroy', $p->id) }}"
                                                        method="POST" style="display:inline;"
                                                        onsubmit="return confirm('Delete this product?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-danger">
                                                            <i class="mdi mdi-delete"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No products found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

