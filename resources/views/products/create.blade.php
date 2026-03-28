@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-5 align-self-center">
                    <h4 class="page-title">Add Product</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                                <li class="breadcrumb-item active">Add Product</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">



            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Add New Product</h4>

                            {{-- Validation Errors --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('products.store') }}" method="POST">
                                @csrf

                                <div class="form-group my-2">
                                    <label>Store <span class="text-danger">*</span></label>
                                    <select name="store_id" class="form-control @error('store_id') is-invalid @enderror">
                                        <option value="">-- Select Store --</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}"
                                                {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('store_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group my-2">
                                    <label>Product Code <span class="text-danger">*</span></label>
                                    <input type="text" name="product_code"
                                        class="form-control @error('product_code') is-invalid @enderror"
                                        placeholder="Unique product code per store" value="{{ old('product_code') }}">
                                    @error('product_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group my-2">
                                    <label>Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror" placeholder="Product name"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group my-2">
                                            <label>Price (₹) <span class="text-danger">*</span></label>
                                            <input type="number" name="price" step="0.01" min="0"
                                                class="form-control @error('price') is-invalid @enderror" placeholder="0.00"
                                                value="{{ old('price') }}">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group my-2">
                                            <label>GST % <span class="text-danger">*</span></label>
                                            <input type="number" name="gst_rate" step="0.01" min="0"
                                                class="form-control @error('gst_rate') is-invalid @enderror" placeholder="0"
                                                value="{{ old('gst_rate', 0) }}">
                                            @error('gst_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group my-2">
                                            <label>Stock Qty <span class="text-danger">*</span></label>
                                            <input type="number" name="stock" min="0"
                                                class="form-control @error('stock') is-invalid @enderror" placeholder="0"
                                                value="{{ old('stock', 0) }}">
                                            @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2  my-2">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="mdi mdi-content-save"></i> Save Product
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left"></i> Cancel
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
