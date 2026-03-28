@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-5 align-self-center">
                    <h4 class="page-title">Edit Staff User</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('staff.index') }}">Staff</a></li>
                                <li class="breadcrumb-item active">Edit</li>
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
                            <h4 class="card-title">Edit Staff User</h4>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('staff.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group my-2">
                                    <label>Full Name</label>
                                    <input type="text" name="full_name" class="form-control"
                                        placeholder="Full name (optional)" value="{{ old('full_name', $user->full_name) }}">
                                </div>

                                <div class="form-group my-2">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username"
                                        class="form-control @error('username') is-invalid @enderror"
                                        value="{{ old('username', $user->username) }}" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group my-2">
                                    <label>Assign Store <span class="text-danger">*</span></label>
                                    <select name="store_id" class="form-control @error('store_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Select Store --</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}"
                                                {{ old('store_id', $user->store_id) == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('store_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex my-2 gap-2">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="mdi mdi-content-save"></i> Update Staff
                                    </button>
                                    <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left"></i> Back
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
