@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-5 align-self-center">
                    <h4 class="page-title">Edit Product</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                                <li class="breadcrumb-item active">Edit Product</li>
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
                            <h4 class="card-title">Edit Product</h4>

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

                            <form action="{{ route('products.update', $product->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group my-2">
                                    <label>Store <span class="text-danger">*</span></label>
                                    <select name="store_id" class="form-control @error('store_id') is-invalid @enderror">
                                        <option value="">-- Select Store --</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}"
                                                {{ old('store_id', $product->store_id) == $store->id ? 'selected' : '' }}>
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
                                        value="{{ old('product_code', $product->product_code) }}">
                                    @error('product_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group my-2">
                                    <label>Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $product->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Price (₹) <span class="text-danger">*</span></label>
                                            <input type="number" name="price" step="0.01" min="0"
                                                class="form-control @error('price') is-invalid @enderror"
                                                value="{{ old('price', $product->price) }}">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 my-2">
                                        <div class="form-group">
                                            <label>GST % <span class="text-danger">*</span></label>
                                            <input type="number" name="gst_rate" step="0.01" min="0"
                                                class="form-control @error('gst_rate') is-invalid @enderror"
                                                value="{{ old('gst_rate', $product->gst_rate) }}">
                                            @error('gst_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 my-2">
                                        <div class="form-group">
                                            <label>Stock Qty <span class="text-danger">*</span></label>
                                            <input type="number" name="stock" min="0"
                                                class="form-control @error('stock') is-invalid @enderror"
                                                value="{{ old('stock', $product->stock) }}">
                                            @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 my-2">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="mdi mdi-content-save"></i> Update Product
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
