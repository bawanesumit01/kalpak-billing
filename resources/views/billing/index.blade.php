@extends('layouts.app')

@section('content')
<style>
/* ═══════════════════════════════════════════
   KALPAK BILLING — COLORFUL UI
═══════════════════════════════════════════ */
.billing-wrapper {
  background: #f0f4ff;
  min-height: 100vh;
  padding: 20px;
}

/* ── Page Header ── */
.billing-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 5px 24px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.billing-header h4 {
  color: #fff;
  font-size: 20px;
  font-weight: 700;
  margin: 0;
}
.billing-header small {
  color: rgba(255,255,255,0.75);
  font-size: 13px;
}
.billing-header .breadcrumb {
  background: transparent;
  padding: 0;
  margin: 0;
}
.billing-header .breadcrumb-item a,
.billing-header .breadcrumb-item.active,
.billing-header .breadcrumb-item + .breadcrumb-item::before {
  color: rgba(255,255,255,0.7);
  font-size: 12px;
}

/* ── Search Section ── */
.search-card {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  border-radius: 16px;
  padding: 22px 24px;
  margin-bottom: 18px;
  position: relative;
  overflow: visible;
}
.search-card::before {
  content: '🔍';
  position: absolute;
  right: 24px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 40px;
  opacity: 0.15;
}
.search-card label {
  color: #a5b4fc;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 8px;
  display: block;
}
.search-card .search-input {
  background: rgba(255,255,255,0.1);
  border: 2px solid rgba(165,180,252,0.3);
  border-radius: 10px;
  color: #fff;
  font-size: 15px;
  padding: 10px 16px;
  width: 100%;
  transition: all 0.2s;
}
.search-card .search-input::placeholder { color: rgba(255,255,255,0.4); }
.search-card .search-input:focus {
  outline: none;
  background: rgba(255,255,255,0.15);
  border-color: #818cf8;
  box-shadow: 0 0 0 4px rgba(129,140,248,0.15);
}
#productSuggestions {
  position: absolute;
  z-index: 9999;
  background: #fff;
  border: none;
  border-radius: 12px;
  width: 100%;
  box-shadow: 0 16px 40px rgba(0,0,0,0.2);
  max-height: 260px;
  overflow-y: auto;
  top: calc(100% + 6px);
  left: 0;
}
#productSuggestions > div {
  padding: 11px 16px;
  cursor: pointer;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
  color: #1e293b;
  transition: background 0.1s;
}
#productSuggestions > div:hover,
#productSuggestions > div.active { background: #ede9fe !important; }
#productSuggestions > div:last-child { border-bottom: none; }

/* Product info bar */
#product_info {
  background: linear-gradient(90deg, #dbeafe, #ede9fe);
  border: none;
  border-left: 4px solid #6366f1;
  border-radius: 10px;
  color: #1e1b4b;
  font-size: 14px;
  margin-top: 12px;
}

/* ── Manual Entry ── */
.manual-card {
  background: linear-gradient(135deg, #fff7ed, #ffedd5);
  border: 2px solid #fb923c;
  border-radius: 14px;
  padding: 18px 20px;
  margin-bottom: 18px;
}
.manual-card .section-badge {
  display: inline-block;
  background: linear-gradient(135deg, #f97316, #ea580c);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  padding: 4px 12px;
  border-radius: 20px;
  margin-bottom: 14px;
}
.manual-card label { font-size: 12px; font-weight: 600; color: #9a3412; }
.manual-card .form-control-sm {
  border: 1.5px solid #fdba74;
  border-radius: 8px;
  background: #fff;
  font-size: 13px;
}
.manual-card .form-control-sm:focus {
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249,115,22,0.15);
}
.btn-manual-add {
  background: linear-gradient(135deg, #f97316, #ea580c);
  border: none;
  color: #fff;
  font-weight: 700;
  font-size: 13px;
  padding: 8px 18px;
  border-radius: 8px;
  width: 100%;
  transition: all 0.2s;
}
.btn-manual-add:hover {
  background: linear-gradient(135deg, #ea580c, #c2410c);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(249,115,22,0.35);
  color: #fff;
}

/* ── Invoice Table ── */
.invoice-table-card {
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  border: 2px solid #e0e7ff;
  margin-bottom: 18px;
  box-shadow: 0 4px 20px rgba(99,102,241,0.08);
}
.invoice-table-card .table-header {
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  padding: 12px 18px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.invoice-table-card .table-header span {
  color: #fff;
  font-weight: 700;
  font-size: 14px;
  letter-spacing: 0.3px;
}
.invoice-table-card .table-header .item-count {
  background: rgba(255,255,255,0.2);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 20px;
}
#items_table { margin: 0; }
#items_table thead th {
  background: #f8faff;
  color: #6366f1;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  padding: 10px 14px;
  border-bottom: 2px solid #e0e7ff;
  border-top: none;
}
#items_table tbody td {
  padding: 10px 14px;
  vertical-align: middle;
  font-size: 14px;
  color: #334155;
  border-color: #f1f5f9;
}
#items_table tbody tr:nth-child(even) { background: #f8faff; }
#items_table tbody tr:hover { background: #eef2ff; }
.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #94a3b8;
}
.empty-state i { font-size: 40px; display: block; margin-bottom: 10px; color: #c7d2fe; }
.empty-state p { font-size: 14px; margin: 0; }

/* ── Customer Section ── */
.customer-card {
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  border: 2px solid #4ade80;
  border-radius: 14px;
  padding: 18px 20px;
  margin-bottom: 18px;
}
.customer-card .section-badge {
  display: inline-block;
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  padding: 4px 12px;
  border-radius: 20px;
  margin-bottom: 14px;
}
.customer-card label { font-size: 12px; font-weight: 600; color: #166534; }
.customer-card .form-control,
.customer-card select {
  border: 1.5px solid #86efac;
  border-radius: 8px;
  font-size: 14px;
  background: #fff;
}
.customer-card .form-control:focus,
.customer-card select:focus {
  border-color: #22c55e;
  box-shadow: 0 0 0 3px rgba(34,197,94,0.15);
}

/* ── Totals ── */
.totals-card {
  background: linear-gradient(135deg, #1e1b4b, #312e81, #1e40af);
  border-radius: 14px;
  padding: 4px;
  margin-bottom: 18px;
}
.totals-inner {
  background: rgba(255,255,255,0.04);
  border-radius: 11px;
  overflow: hidden;
}
.totals-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 11px 18px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}
.totals-row:last-child { border-bottom: none; }
.totals-row .label { color: #a5b4fc; font-size: 13px; }
.totals-row .value { color: #e0e7ff; font-size: 14px; font-weight: 700; }
.totals-row.grand {
  background: rgba(99,102,241,0.35);
  padding: 15px 18px;
}
.totals-row.grand .label { color: #fff; font-size: 15px; font-weight: 700; }
.totals-row.grand .value { color: #67e8f9; font-size: 20px; font-weight: 800; }
.totals-row .discount-val { color: #fca5a5 !important; }

/* ── Action Buttons ── */
.action-bar {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 4px;
}
.btn-save-invoice {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  border: none;
  color: #fff;
  font-weight: 700;
  font-size: 15px;
  padding: 12px 30px;
  border-radius: 12px;
  transition: all 0.2s;
  letter-spacing: 0.3px;
}
.btn-save-invoice:hover {
  background: linear-gradient(135deg, #4338ca, #6d28d9);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(79,70,229,0.45);
  color: #fff;
}
.btn-clear-invoice {
  background: #fff;
  border: 2px solid #cbd5e1;
  color: #64748b;
  font-weight: 600;
  font-size: 14px;
  padding: 12px 22px;
  border-radius: 12px;
  transition: all 0.2s;
}
.btn-clear-invoice:hover {
  background: #f1f5f9;
  border-color: #94a3b8;
  color: #334155;
}

/* Remove btn */
.btn-remove-item {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  border: none;
  color: #fff;
  border-radius: 7px;
  padding: 4px 12px;
  font-size: 13px;
  font-weight: 600;
  transition: all 0.15s;
}
.btn-remove-item:hover { background: #b91c1c; transform: scale(1.05); }

/* Qty input */
.qty-input {
  width: 70px !important;
  text-align: center;
  border: 1.5px solid #c7d2fe;
  border-radius: 7px;
  font-weight: 600;
  color: #4f46e5;
}
.qty-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }

/* Stock badges */
.stock-ok   { background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.stock-low  { background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.stock-out  { background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
</style>

<div class="page-wrapper">

  {{-- ── PAGE HEADER ── --}}
  <div class="billing-wrapper">
    <div class="billing-header">
      <div>
        <h4>🧾 Billing <small>— {{ $storeName }}</small></h4>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mt-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">New Invoice</li>
          </ol>
        </nav>
      </div>
      <div style="color:rgba(255,255,255,0.7);font-size:13px;">
        <i class="mdi mdi-calendar mr-1"></i> {{ now()->format('d M Y') }}
      </div>
    </div>
<div class="row">
    {{-- ── SEARCH SECTION ── --}}
    <div class="col-4">
    <div class="search-card">
      <div class="row align-items-end">
        <div class="col-md-10 position-relative">
          <label>Search Product by Name or Code</label>
          <input id="product_name" class="search-input"
                 placeholder="Type product name or code..."
                 autocomplete="off">
          <div id="productSuggestions" style="display:none;"></div>
        </div>
        <div class="col-md-2">
          <div id="product_info" class="alert mb-0" style="display:none;">
            <strong id="p_name"></strong> &nbsp;|&nbsp;
            Price: ₹<span id="p_price"></span> &nbsp;|&nbsp;
            GST: <span id="p_gst"></span>% &nbsp;|&nbsp;
            Stock: <span id="p_stock"></span> &nbsp;
            <span id="stock_badge"></span>
          </div>
        </div>
      </div>
    </div>
</div>
    {{-- ── MANUAL ENTRY ── --}}
     <div class="col-8">
    <div class="manual-card">
      <div class="section-badge">📦 Add Manually (Not in List)</div>
      <div class="row align-items-end">
        <div class="col-md-3">
          <label>Product Name</label>
          <input type="text" id="manual_name" class="form-control form-control-sm" placeholder="Product Name">
        </div>
        <div class="col-md-2">
          <label>Price (₹)</label>
          <input type="number" id="manual_price" class="form-control form-control-sm" placeholder="0.00" step="0.01" min="0">
        </div>
        <div class="col-md-2">
          <label>Qty</label>
          <input type="number" id="manual_qty" class="form-control form-control-sm" placeholder="1" min="1" value="1">
        </div>
        <div class="col-md-2">
          <label>GST %</label>
          <input type="number" id="manual_gst_rate" class="form-control form-control-sm" placeholder="0" step="0.01" min="0" value="0">
        </div>
        <div class="col-md-2 mt-2 mt-md-0">
          <label>&nbsp;</label>
          <button id="add_manual_btn" class="btn-manual-add btn btn-block">
            <i class="mdi mdi-plus"></i> Add Item
          </button>
        </div>
      </div>
    </div>
    </div>
</div>
    {{-- ── INVOICE TABLE ── --}}
    <div class="invoice-table-card">
      <div class="table-header">
        <span>🧾 Invoice Items</span>
        <span class="item-count" id="item-count-badge">0 items</span>
      </div>
      <div class="table-responsive">
        <table class="table table-sm mb-0" id="items_table">
          <thead>
            <tr>
              <th>Code</th>
              <th>Product Name</th>
              <th>Price (₹)</th>
              <th>Qty</th>
              <th>GST (₹)</th>
              <th>Total (₹)</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody id="invoiceItems">
            <tr>
              <td colspan="7">
                <div class="empty-state">
                  <i class="mdi mdi-cart-outline"></i>
                  <p>No items yet — search or add manually above</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    {{-- ── CUSTOMER + TOTALS SIDE BY SIDE ── --}}
    <div class="row">
      <div class="col-md-8">
        <div class="customer-card">
          <div class="section-badge">👤 Customer & Payment Details</div>
          <div class="row">
            <div class="col-md-4">
              <label>Customer Name</label>
              <input id="customer_name" class="form-control form-control-sm" placeholder="Walk-in Customer">
            </div>
            <div class="col-md-3">
              <label>Phone <span class="text-danger">*</span></label>
              <input id="customer_phone" class="form-control form-control-sm" maxlength="10"
                     placeholder="10-digit mobile" inputmode="numeric">
            </div>
            <div class="col-md-3">
              <label>Payment Mode</label>
              <select id="payment_mode" class="form-control form-control-sm">
                <option>Cash</option>
                <option>UPI</option>
                <option>Pay Later</option>
              </select>
            </div>
            <div class="col-md-2">
              <label>Discount (₹)</label>
              <input id="invoice_discount" type="number" min="0" step="0.01" value="0"
                     class="form-control form-control-sm">
            </div>
          </div>
          <div class="action-bar mt-3">
            <button class="btn-clear-invoice" onclick="clearAll()">
              <i class="mdi mdi-close-circle-outline mr-1"></i> Clear All
            </button>
            <button id="save_btn" class="btn-save-invoice">
              <i class="mdi mdi-printer mr-1"></i> Save & Print Invoice
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="totals-card">
          <div class="totals-inner">
            <div class="totals-row">
              <span class="label">Subtotal</span>
              <span class="value" id="subtotal">₹0.00</span>
            </div>
            <div class="totals-row">
              <span class="label">Discount</span>
              <span class="value discount-val" id="discounts">₹0.00</span>
            </div>
            <div class="totals-row">
              <span class="label">GST</span>
              <span class="value" id="gst_total">₹0.00</span>
            </div>
            <div class="totals-row grand">
              <span class="label">💰 Grand Total</span>
              <span class="value" id="grand_total">₹0.00</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /billing-wrapper --}}
</div>{{-- /page-wrapper --}}

{{-- Toast --}}
<div id="toast" style="display:none; position:fixed; bottom:24px; right:24px;
     background:linear-gradient(135deg,#10b981,#059669); color:#fff;
     padding:14px 24px; border-radius:12px; font-weight:700; font-size:14px;
     box-shadow:0 8px 30px rgba(16,185,129,0.4); z-index:99999; letter-spacing:0.3px;">
</div>
@endsection

@push('scripts')
<script>
const storeId     = {{ $storeId }};
const SEARCH_URL  = "{{ route('billing.search') }}";
const FETCH_URL   = "{{ route('billing.fetch') }}";
const SAVE_URL    = "{{ route('billing.save') }}";
const INVOICE_URL = "{{ url('billing/invoice') }}";
const CSRF        = "{{ csrf_token() }}";

let items = [];
let nameSuggestions = [];
let nameSelectedIndex = -1;

const money = v => parseFloat(v).toFixed(2);

$(document).ready(function () {

  // Phone digits only
  $('#customer_phone').on('input', function () {
    $(this).val($(this).val().replace(/\D/g, '').slice(0, 10));
  });

  // Search
  const $nameInput = $('#product_name');
  const $suggestBox = $('#productSuggestions');

  $nameInput.on('input', function () {
    const q = $(this).val().trim();

    if (q.length < 2) {
      $suggestBox.hide();
      return;
    }

    $.ajax({
      url: SEARCH_URL,
      type: 'GET',
      data: {
        q: q,
        store: storeId
      },
      success: function (data) {
        nameSuggestions = Array.isArray(data) ? data : [];
        showSuggestions();
      },
      error: function () {
        $suggestBox.hide();
      }
    });
  });

  function showSuggestions() {
    $suggestBox.html('');
    nameSelectedIndex = -1;

    if (!nameSuggestions.length) {
      $suggestBox.hide();
      return;
    }

    $.each(nameSuggestions, function (i, p) {
      const $div = $('<div></div>').html(`
        <strong style="color:#4f46e5">${p.product_code}</strong> — ${p.name}
        <small style="color:#94a3b8;float:right">₹${parseFloat(p.price).toFixed(2)} | Stock: ${p.stock}</small>
      `);

      $div.on('mouseover', function () {
        nameSelectedIndex = i;
        highlight();
      });

      $div.on('click', function () {
        chooseProduct(p);
      });

      $suggestBox.append($div);
    });

    $suggestBox.show();
    highlight();
  }

  function highlight() {
    $suggestBox.find('div').each(function (i) {
      $(this).css('background', i === nameSelectedIndex ? '#ede9fe' : 'white');
    });
  }

  $nameInput.on('keydown', function (e) {
    if (!nameSuggestions.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      nameSelectedIndex = Math.min(nameSelectedIndex + 1, nameSuggestions.length - 1);
      highlight();
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      nameSelectedIndex = Math.max(nameSelectedIndex - 1, -1);
      highlight();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (nameSelectedIndex >= 0) {
        chooseProduct(nameSuggestions[nameSelectedIndex]);
      }
    }
  });

  $(document).on('click', function (e) {
    if (
      !$(e.target).closest('#product_name').length &&
      !$(e.target).closest('#productSuggestions').length
    ) {
      $suggestBox.hide();
    }
  });

  function chooseProduct(p) {
    if (!p.source_table) p.source_table = 'products';

    showProductInfo(p);

    if (parseFloat(p.stock) <= 0) {
      alert('Product out of stock.');
      $nameInput.val('');
      $suggestBox.hide();
      return;
    }

    addOrIncrement(p);
    $nameInput.val('');
    $suggestBox.hide();
    $nameInput.focus();
  }

  function showProductInfo(p) {
    $('#p_name').text(p.name);
    $('#p_price').text(money(p.price));
    $('#p_gst').text(p.gst_rate || 0);
    $('#p_stock').text(p.stock);

    const stock = Number(p.stock);
    const $badge = $('#stock_badge');

    if (stock <= 0) {
      $badge.html('<span class="stock-out">Out of Stock</span>');
    } else if (stock < 10) {
      $badge.html('<span class="stock-low">⚠ Low Stock</span>');
    } else {
      $badge.html('<span class="stock-ok">✓ In Stock</span>');
    }

    $('#product_info').show();
  }

  function addOrIncrement(p) {
    const key = (p.source_table || 'products') + '_' + p.id;
    const existing = items.find(i => ((i.source_table || 'products') + '_' + i.product_id) === key);

    if (existing) {
      if (existing.qty + 1 > Number(p.stock)) {
        alert('Not enough stock.');
        return;
      }
      existing.qty += 1;
    } else {
      items.push({
        product_id: p.id,
        product_code: p.product_code,
        product_name: p.name,
        unit_price: parseFloat(p.price),
        qty: 1,
        gst_rate: parseFloat(p.gst_rate || 0),
        source_table: p.source_table || 'products'
      });
    }

    renderItems();
  }

  $('#add_manual_btn').on('click', function () {
    const name  = $('#manual_name').val().trim();
    const price = parseFloat($('#manual_price').val()) || 0;
    const qty   = parseInt($('#manual_qty').val()) || 1;
    const gst   = parseFloat($('#manual_gst_rate').val()) || 0;

    if (!name) {
      alert('Please enter product name.');
      return;
    }

    if (price <= 0) {
      alert('Price must be greater than 0.');
      return;
    }

    const prefix = name.substring(0, 3).toUpperCase().replace(/[^A-Z]/g, '') || 'PRD';
    const code   = prefix + Date.now().toString().slice(-4);

    items.push({
      product_id: 0,
      product_code: code,
      product_name: name,
      unit_price: price,
      qty: qty,
      gst_rate: gst,
      source_table: 'manual'
    });

    $('#manual_name').val('');
    $('#manual_price').val('');
    $('#manual_qty').val('1');
    $('#manual_gst_rate').val('0');

    renderItems();
  });

  function renderItems() {
    const $tbody = $('#invoiceItems');
    $tbody.html('');

    let subtotal = 0, gstTotal = 0;

    if (items.length === 0) {
      $tbody.html(`
        <tr>
          <td colspan="7">
            <div class="empty-state">
              <i class="mdi mdi-cart-outline"></i>
              <p>No items yet — search or add manually above</p>
            </div>
          </td>
        </tr>
      `);
      updateBadge(0);
      updateTotals(0, 0);
      return;
    }

    $.each(items, function (idx, it) {
      const base      = it.unit_price * it.qty;
      const gstAmt    = base * (it.gst_rate / 100);
      const lineTotal = base + gstAmt;

      it.gst_amount   = parseFloat(gstAmt.toFixed(2));
      it.total_amount = parseFloat(lineTotal.toFixed(2));

      subtotal += base;
      gstTotal += it.gst_amount;

      const row = `
        <tr>
          <td><span style="background:#ede9fe;color:#4f46e5;padding:2px 8px;border-radius:6px;font-size:12px;font-weight:700;">${it.product_code}</span></td>
          <td style="font-weight:600;color:#1e293b;">${it.product_name}</td>
          <td style="color:#0891b2;font-weight:600;">₹${money(it.unit_price)}</td>
          <td><input type="number" class="form-control qty-input qty-change" min="1" data-index="${idx}" value="${it.qty}"></td>
          <td style="color:#d97706;">₹${money(it.gst_amount)}</td>
          <td style="color:#059669;font-weight:700;">₹${money(it.total_amount)}</td>
          <td><button type="button" class="btn-remove-item remove-item" data-index="${idx}"><i class="mdi mdi-delete"></i></button></td>
        </tr>
      `;

      $tbody.append(row);
    });

    updateBadge(items.length);
    updateTotals(subtotal, gstTotal);
  }

  function updateBadge(count) {
    const $badge = $('#item-count-badge');
    if ($badge.length) {
      $badge.text(count + (count === 1 ? ' item' : ' items'));
    }
  }

  function updateTotals(subtotal, gstTotal) {
    const disc  = Math.min(Math.max(parseFloat($('#invoice_discount').val()) || 0, 0), subtotal);
    const grand = (subtotal - disc) + gstTotal;

    $('#subtotal').text('₹' + money(subtotal));
    $('#discounts').text('₹' + money(disc));
    $('#gst_total').text('₹' + money(gstTotal));
    $('#grand_total').text('₹' + money(grand));
  }

  $(document).on('change', '.qty-change', function () {
    const idx = $(this).data('index');
    items[idx].qty = Math.max(1, parseInt($(this).val()) || 1);
    renderItems();
  });

  $(document).on('click', '.remove-item', function () {
    const idx = $(this).data('index');
    items.splice(idx, 1);
    renderItems();
  });

  $('#invoice_discount').on('input', function () {
    renderItems();
  });

  window.clearAll = function () {
    if (!confirm('Clear all items?')) return;

    items = [];
    renderItems();

    $('#product_info').hide();
    $('#customer_name').val('');
    $('#customer_phone').val('');
    $('#invoice_discount').val('0');
  };

  $('#save_btn').on('click', function (e) {
    e.preventDefault();

    if (!items.length) {
      alert('Please add at least one product.');
      return;
    }

    const phone = $('#customer_phone').val().trim();
    if (!/^[0-9]{10}$/.test(phone)) {
      alert('Please enter a valid 10-digit phone number.');
      return;
    }

    const payload = new FormData();
    payload.append('_token', CSRF);
    payload.append('invoice_items', JSON.stringify(items));
    payload.append('invoice_discount', parseFloat($('#invoice_discount').val() || 0));
    payload.append('customer_name', $('#customer_name').val() || '');
    payload.append('customer_phone', phone);
    payload.append('payment_mode', $('#payment_mode').val() || 'Cash');

    const win = window.open('', '_blank');

    $.ajax({
      url: SAVE_URL,
      type: 'POST',
      data: payload,
      processData: false,
      contentType: false,
      success: function (data) {
        if (data.status === 'success' && data.invoice_id) {
          win.location = INVOICE_URL + '/' + data.invoice_id;
          showToast('✅ Invoice saved successfully!');
          setTimeout(() => clearAll(), 1000);
        } else {
          try { win.close(); } catch (e) {}
          alert('Save failed: ' + (data.msg || 'Unknown error'));
        }
      },
      error: function () {
        try { win.close(); } catch (e) {}
        alert('Network error. Try again.');
      }
    });
  });

  function showToast(msg) {
    const $t = $('#toast');
    $t.text(msg).show().css('opacity', 1);

    setTimeout(function () {
      $t.css('opacity', 0);
      setTimeout(function () {
        $t.hide();
      }, 300);
    }, 2800);
  }

  renderItems();
});
</script>
@endpush