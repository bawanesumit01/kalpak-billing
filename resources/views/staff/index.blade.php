@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-5 align-self-center">
                    <h4 class="page-title">Manage Staff Users </h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Manage Staff Users</li>
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

                {{-- ======================== --}}
                {{-- ADD NEW STAFF FORM       --}}
                {{-- ======================== --}}
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Add New Staff User</h4>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('staff.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group my-2">
                                            <label>Full Name</label>
                                            <input type="text" name="full_name" class="form-control"
                                                placeholder="Full name (optional)" value="{{ old('full_name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group my-2">
                                            <label>Username <span class="text-danger">*</span></label>
                                            <input type="text" name="username"
                                                class="form-control @error('username') is-invalid @enderror"
                                                placeholder="Username" value="{{ old('username') }}" required>
                                            @error('username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group my-2">
                                            <label>Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Password" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group my-2">
                                            <label>Assign Store <span class="text-danger">*</span></label>
                                            <select name="store_id"
                                                class="form-control @error('store_id') is-invalid @enderror" required>
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
                                    </div>

                                </div>

                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="mdi mdi-account-plus"></i> Create Staff
                                </button>

                            </form>
                        </div>
                    </div>
                </div>

                {{-- ======================== --}}
                {{-- STAFF LIST TABLE         --}}
                {{-- ======================== --}}
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Existing Staff Users
                                <span class="badge badge-primary ml-2">{{ $staff->count() }}</span>
                            </h4>

                            <div class="table-responsive">
                                <table id="scroll_hor" class="table table-striped table-bordered display nowrap" width="100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Full Name</th>
                                            <th>Assigned Store</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($staff as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $user->username }}</strong>
                                                </td>
                                                <td>{{ $user->full_name ?: '-' }}</td>
                                                <td>
                                                    @if ($user->store_name)
                                                        <span class="badge bg-info">{{ $user->store_name }}</span>
                                                    @else
                                                        <span class="text-muted">Not Assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d-M-Y') : '-' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('staff.edit', $user->id) }}"
                                                        class="btn btn-xs btn-info mr-1">
                                                        <i class="mdi mdi-pencil"></i> Edit
                                                    </a>

                                                    <form action="{{ route('staff.destroy', $user->id) }}" method="POST"
                                                        style="display:inline;"
                                                        onsubmit="return confirm('Delete this staff user?')">
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
                                                <td colspan="6" class="text-center text-muted">
                                                    No staff users found.
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
        </div>
    </div>
@endsection
