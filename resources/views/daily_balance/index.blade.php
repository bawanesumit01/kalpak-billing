@extends('layouts.app')

@section('content')
<style>
.balance-card {
  border-radius: 14px;
  border: none;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0,0,0,0.12);
  transition: transform 0.2s;
}
.balance-card:hover { transform: translateY(-3px); }
.balance-card .card-body { padding: 20px 22px; }
.balance-card .label  { font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; opacity: 0.85; margin-bottom: 6px; }
.balance-card .amount { font-size: 26px; font-weight: 800; margin: 0; line-height: 1.1; }
.balance-card .sub    { font-size: 12px; opacity: 0.75; margin-top: 4px; }
.split-card {
  border-radius: 14px;
  border: none;
  box-shadow: 0 6px 20px rgba(0,0,0,0.12);
  overflow: hidden;
}
.split-half {
  padding: 20px 22px;
  flex: 1;
}
.split-half .label  { font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; opacity: 0.85; margin-bottom: 6px; }
.split-half .amount { font-size: 22px; font-weight: 800; margin: 0; }
.split-half .sub    { font-size: 11px; opacity: 0.75; margin-top: 3px; }
.split-divider { width: 2px; background: rgba(255,255,255,0.2); margin: 12px 0; }

.form-card {
  border-radius: 14px;
  border: none;
  box-shadow: 0 4px 16px rgba(0,0,0,0.07);
  overflow: hidden;
}
.form-card .form-header {
  padding: 14px 20px;
  font-size: 15px;
  font-weight: 700;
}
.form-card .form-body { padding: 20px; }

.txn-table thead th {
  background: #1e293b;
  color: #94a3b8;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  padding: 11px 14px;
  border: none;
}
.txn-table tbody td { padding: 10px 14px; vertical-align: middle; font-size: 13px; border-color: #f1f5f9; }
.txn-table tbody tr.add-row  { background: #f0fdf4; }
.txn-table tbody tr.rem-row  { background: #fff1f2; }
.txn-table tbody tr:hover { opacity: 0.9; }

.badge-add    { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
.badge-remove { background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
.breakdown-pill {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,0.15);
  border-radius: 20px; padding: 4px 12px;
  font-size: 12px; font-weight: 600;
  margin-top: 8px;
}
</style>

<div class="page-wrapper">
  <div class="page-breadcrumb">
    <div class="row">
      <div class="col-5 align-self-center">
        <h4 class="page-title">Daily Balance Flow
          @if($role === 'staff')
            <small class="text-muted">({{ session('store_name') }} — {{ now()->format('d M Y') }})</small>
          @endif
        </h4>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Daily Balance</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>

  <div class="container-fluid">

    {{-- Alerts --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        <i class="mdi mdi-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    @endif

    {{-- ── ADMIN FILTER ── --}}
    @if($role === 'admin')
    <div class="card form-card mb-4">
      <div class="card-body py-3">
        <form method="GET" action="{{ route('daily-balance.index') }}">
          <div class="row align-items-end">
            <div class="col-md-4">
              <label class="small font-weight-bold">Store</label>
              <select name="store_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Select Store --</option>
                @foreach($stores as $store)
                  <option value="{{ $store->id }}" {{ $selectedStore == $store->id ? 'selected' : '' }}>
                    {{ $store->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="small font-weight-bold">Date</label>
              <input type="date" name="date" class="form-control"
                     value="{{ $selectedDate }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary">
                <i class="mdi mdi-filter"></i> Filter
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    @endif

    @if($selectedStore > 0)

    {{-- ── BALANCE SUMMARY CARDS ── --}}
    <div class="row mb-4">

      {{-- Opening Balance --}}
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="balance-card card" style="background:linear-gradient(135deg,#667eea,#764ba2);">
          <div class="card-body text-white">
            <div class="label">📂 Opening Balance</div>
            <div class="amount">₹ {{ number_format($todayBalance->opening_balance ?? 0, 2) }}</div>
            <div class="sub">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }} (from yesterday's closing)</div>
          </div>
        </div>
      </div>

      {{-- Current Balance Split Card --}}
      <div class="col-md-6 col-sm-12 mb-3">
        <div class="split-card d-flex" style="background:linear-gradient(135deg,#1e3a5f,#1a6b3c);">

          {{-- Cash + Online --}}
          <div class="split-half text-white">
            <div class="label">💵 Cash + Online Received</div>
            <div class="amount">₹ {{ number_format($currentBalance, 2) }}</div>
            @if(!empty($breakdown))
            <div class="mt-2">
              <span class="breakdown-pill">
                Opening: ₹{{ number_format($breakdown['opening'], 2) }}
              </span>
              <span class="breakdown-pill">
                Sales: ₹{{ number_format($breakdown['cash_online_sales'], 2) }}
              </span>
              @if($breakdown['manual_adds'] > 0)
              <span class="breakdown-pill">
                +Added: ₹{{ number_format($breakdown['manual_adds'], 2) }}
              </span>
              @endif
              @if($breakdown['manual_removals'] > 0)
              <span class="breakdown-pill" style="background:rgba(239,68,68,0.2);">
                -Removed: ₹{{ number_format($breakdown['manual_removals'], 2) }}
              </span>
              @endif
            </div>
            @if(!empty($breakdown['invoice_counts']))
            <div class="sub mt-2">
              {{ $breakdown['invoice_counts']->paid_count ?? 0 }} paid invoices today
            </div>
            @endif
            @endif
          </div>

          <div class="split-divider"></div>

          {{-- Pay Later --}}
          <div class="split-half text-white" style="background:rgba(0,0,0,0.15);">
            <div class="label">⏳ Pay Later (Pending)</div>
            <div class="amount" style="color:#fbbf24;">₹ {{ number_format($payLaterTotal, 2) }}</div>
            @if(!empty($breakdown['invoice_counts']))
            <div class="sub mt-1">
              {{ $breakdown['invoice_counts']->pending_count ?? 0 }} unpaid invoices
            </div>
            @endif
            <div class="sub mt-1" style="color:#fcd34d;">Money not yet collected</div>
          </div>

        </div>
      </div>

      {{-- Closing Balance --}}
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="balance-card card" style="background:linear-gradient(135deg,#f7971e,#ffd200);">
          <div class="card-body text-white">
            <div class="label">🔒 Closing Balance</div>
            <div class="amount">₹ {{ number_format($todayBalance->closing_balance ?? $currentBalance, 2) }}</div>
            <div class="sub">{{ $storeName }}</div>
          </div>
        </div>
      </div>

    </div>
    {{-- /balance cards --}}

    {{-- ── ADMIN ADD/REMOVE FORMS ── --}}
    @if($role === 'admin')
    <div class="row mb-4">

      {{-- ADD CASH --}}
      <div class="col-md-6">
        <div class="form-card card">
          <div class="form-header text-white" style="background:linear-gradient(135deg,#10b981,#059669);">
            <i class="mdi mdi-plus-circle mr-2"></i> Add Cash
          </div>
          <div class="form-body">
            <form action="{{ route('daily-balance.add-cash') }}" method="POST">
              @csrf
              <input type="hidden" name="store_id" value="{{ $selectedStore }}">
              <input type="hidden" name="date" value="{{ $selectedDate }}">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="small font-weight-bold">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="small font-weight-bold">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-control" required>
                      <option value="sales">Sales</option>
                      <option value="other_income">Other Income</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="small font-weight-bold">Description</label>
                <input type="text" name="description" class="form-control" placeholder="Optional description">
              </div>
              <button type="submit" class="btn btn-success btn-block font-weight-bold">
                <i class="mdi mdi-plus"></i> Add Cash
              </button>
            </form>
          </div>
        </div>
      </div>

      {{-- REMOVE CASH --}}
      <div class="col-md-6">
        <div class="form-card card">
          <div class="form-header text-white" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
            <i class="mdi mdi-minus-circle mr-2"></i> Remove Cash
          </div>
          <div class="form-body">
            <form action="{{ route('daily-balance.remove-cash') }}" method="POST">
              @csrf
              <input type="hidden" name="store_id" value="{{ $selectedStore }}">
              <input type="hidden" name="date" value="{{ $selectedDate }}">
              <div class="form-group">
                <label class="small font-weight-bold">Amount (₹) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
              </div>
              <div class="form-group">
                <label class="small font-weight-bold">Reason</label>
                <input type="text" name="description" class="form-control" placeholder="Reason for withdrawal">
              </div>
              <div class="alert py-2 mb-3" style="background:#fff7ed;border:1.5px solid #fdba74;border-radius:8px;">
                <small><i class="mdi mdi-information text-warning"></i>
                  Available Cash+Online: <strong>₹ {{ number_format($currentBalance, 2) }}</strong>
                </small>
              </div>
              <button type="submit" class="btn btn-danger btn-block font-weight-bold">
                <i class="mdi mdi-minus"></i> Remove Cash
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>
    @endif

    {{-- ── TRANSACTIONS TABLE ── --}}
    <div class="card form-card">
      <div class="card-body p-0">
        <div class="px-4 pt-4 pb-3 d-flex align-items-center justify-content-between">
          <h5 class="mb-0 font-weight-bold">
            📋 Transactions — {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
          </h5>
          @if($role === 'staff')
            <span style="background:#dbeafe;color:#1e40af;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
              View Only
            </span>
          @endif
        </div>

        <div class="table-responsive">
          <table class="table txn-table mb-0">
            <thead>
              <tr>
                <th>Time</th>
                @if($role === 'admin')<th>Store</th>@endif
                <th>Type</th>
                <th>Category</th>
                <th>Amount (₹)</th>
                <th>Description</th>
                @if($role === 'admin')<th>By</th>@endif
              </tr>
            </thead>
            <tbody>
              @forelse($transactions as $t)
              <tr class="{{ $t->transaction_type === 'add' ? 'add-row' : 'rem-row' }}">
                <td>{{ \Carbon\Carbon::parse($t->created_at)->format('h:i A') }}</td>
                @if($role === 'admin')<td>{{ $t->store_name }}</td>@endif
                <td>
                  @if($t->transaction_type === 'add')
                    <span class="badge-add">➕ Add</span>
                  @else
                    <span class="badge-remove">➖ Remove</span>
                  @endif
                </td>
                <td style="color:#475569;">{{ ucfirst(str_replace('_', ' ', $t->transaction_category ?? 'other')) }}</td>
                <td style="font-weight:700;color:{{ $t->transaction_type === 'add' ? '#166534' : '#991b1b' }};">
                  ₹ {{ number_format($t->amount, 2) }}
                </td>
                <td style="color:#64748b;">{{ $t->description ?: '—' }}</td>
                @if($role === 'admin')<td>{{ $t->staff_name ?? 'System' }}</td>@endif
              </tr>
              @empty
              <tr>
                <td colspan="{{ $role === 'admin' ? 7 : 5 }}" class="text-center text-muted py-5">
                  <i class="mdi mdi-information-outline" style="font-size:32px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>
                  No manual transactions for this date.
                  @if($breakdown['cash_online_sales'] > 0)
                    <br><small>Invoice sales of ₹{{ number_format($breakdown['cash_online_sales'], 2) }} are included in the balance above.</small>
                  @endif
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    @else
      @if($role === 'admin')
      <div class="alert alert-info">
        <i class="mdi mdi-information"></i> Please select a store and date to view transactions.
      </div>
      @endif
    @endif

  </div>
</div>
@endsection