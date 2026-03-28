<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_no }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #1f2937;
            background: #f3f4f6;
        }

        .invoice-box {
            width: 700px;
            margin: auto;
            border: 2px solid #4f46e5;
            padding: 35px;
            background: #ffffff;
        }

        /* ── Totals ── */
        .totals-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .totals-box {
            width: 300px;
            border: 1px solid #e0e7ff;
            border-radius: 8px;
            overflow: hidden;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 14px;
            border-bottom: 1px solid #e0e7ff;
            font-size: 10pt;
        }

        .totals-row:last-child {
            border-bottom: none;
        }

        .totals-row .t-label {
            color: #555;
        }

        .totals-row .t-value {
            font-weight: 700;
            color: #222;
        }

        .totals-row.discount-row .t-label {
            color: #dc2626;
        }

        .totals-row.discount-row .t-value {
            color: #dc2626;
        }

        .totals-row.grand-row {
            background: #4f46e5;
            padding: 12px 14px;
        }

        .totals-row.grand-row .t-label {
            color: #fff;
            font-size: 12pt;
            font-weight: 700;
        }

        .totals-row.grand-row .t-value {
            color: #a5f3fc;
            font-size: 14pt;
            font-weight: 800;
        }

        /* Pay later outstanding */
        .outstanding-row {
            background: #fffbeb;
            border: 1.5px solid #fbbf24;
            border-radius: 6px;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 11pt;
            font-weight: 700;
            color: #92400e;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }

        .header h3 {
          font-size: 22pt;
          font-weight: 700;
          color: #4f46e5;
          letter-spacing: -0.5px;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 12px 0;
            color: #4f46e5;
            letter-spacing: 1px;
        }

        .section {
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 7px;
            border: 1px solid #d1d5db;
            text-align: left;
        }

        th {
            background: #4f46e5;
            color: #ffffff;
            font-weight: 600;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .no-border td {
            border: none;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
            color: #111827;
        }

        .section strong {
            color: #374151;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            color: #374151;
        }

        .signature {
            text-align: right;
            margin-top: 60px;
        }

        .signature div:first-child {
            border-top: 1px solid #6b7280;
            width: 200px;
            margin-left: auto;
            margin-bottom: 5px;
        }

        .pay-later-badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fbbf24;
            font-size: 9pt;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 4px;
        }

        .pay-later-notice {
            background: #fffbeb;
            border: 1.5px solid #fbbf24;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 10pt;
            color: #92400e;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pay-later-notice strong {
            color: #b45309;
        }
    </style>

</head>

<body class="mb-4">

    {{-- Action Buttons --}}
    <div class="my-4 text-end">
        <a href="{{ route('billing.index') }}" class="btn btn-secondary">← New Invoice</a>
        <a href="{{ route('billing.invoice-list') }}" class="btn btn-info" style="background:#0891b2;">📋 All Invoices</a>
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print / Save PDF</button>
    </div>

    <div class="invoice-box">

        <!-- HEADER -->
        <div class="header">
            <div>
                <h3>Kalpak Enterprises</h3>
                <p>Phone no.: 9552513349</p>
            </div>
            <img src="{{ asset('public/assets/images/kalpak-logo.jpeg') }}" width="30%">
        </div>

        <div class="title">Tax Invoice</div>


        {{-- Pay Later Notice --}}
        @if ($invoice->payment_mode === 'Pay Later')
            <div class="pay-later-notice">
                ⚠️ <div>
                    <strong>Payment Pending</strong> —
                    This invoice is marked as <strong>Pay Later</strong>.
                    Amount of <strong>₹ {{ number_format($invoice->total_amount, 2) }}</strong> is due from customer.
                </div>
            </div>
        @endif

        <!-- BILL TO + INVOICE DETAILS -->
        <table class="no-border">
            <tr>
                <td>
                    <strong>Bill To</strong><br>
                    {{ $invoice->customer_name }}<br>
                    Contact No.: {{ $invoice->customer_phone }}<br>
                    Payment: @if ($invoice->payment_mode === 'Pay Later')
                        <span class="pay-later-badge">⏳ Pay Later</span>
                    @else
                        <span
                            style="background:#dcfce7;color:#166534;padding:2px 10px;border-radius:20px;font-size:9pt;font-weight:600;">
                            {{ $invoice->payment_mode }}
                        </span>
                    @endif

                </td>
                <td class="right">
                    <strong>Invoice Details</strong><br>
                    Invoice No.: {{ $invoice->invoice_no }}<br>
                    Date: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') }}<br>
                    Store: {{ $invoice->store->name ?? ($invoice->store_name ?? '-') }}
                </td>
            </tr>
        </table>

        <!-- ITEMS -->
        <div class="section">
            <table>
                <thead>
                    <tr>
                        <th>Item name</th>
                        <th>Quantity</th>
                        <th>Price / Unit</th>
                        <th>GST (₹)</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>₹ {{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right" style="color:#d97706;">{{ number_format($item->gst_amount, 2) }}
                            </td>
                            <td class="text-right" style="font-weight:700;color:#059669;">
                                {{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>



        {{-- ── TOTALS ── --}}
        <div class="totals-wrap mt-3">
            <div>
                <div class="totals-box">
                    <div class="totals-row">
                        <span class="t-label">Subtotal</span>
                        <span class="t-value">₹ {{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    @if ($invoice->gst_total > 0)
                        <div class="totals-row">
                            <span class="t-label">GST Total</span>
                            <span class="t-value">₹ {{ number_format($invoice->gst_total, 2) }}</span>
                        </div>
                    @endif
                    @if ($invoice->discount_total + $invoice->invoice_discount_amount > 0)
                        <div class="totals-row discount-row">
                            <span class="t-label">Discount</span>
                            <span class="t-value">− ₹ {{ number_format($invoice->discount_total, 2) }}</span>
                        </div>
                    @endif
                    <div class="totals-row grand-row">
                        <span class="t-label">Grand Total</span>
                        <span class="t-value">₹ {{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                </div>

                {{-- Pay Later outstanding amount --}}
                @if ($invoice->payment_mode === 'Pay Later')
                    <div class="outstanding-row">
                        <span>💰 Outstanding Amount</span>
                        <span>₹ {{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- AMOUNT IN WORDS -->
        <div class="section">
            <strong>Invoice Amount in Words:</strong>
            {{ $invoice->total_amount }} only
        </div>

        <!-- TERMS -->
        <div class="section">
            <strong>Terms and Conditions</strong><br>
            Thanks You !<br>
            Our Products : Oils, Fresh Chakki Aata, Dry Fruits, Spices, Millets, Jaggery, Home Made
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <div>For : Kalpak Enterprises</div>

            <div class="signature">
                <div>________________________</div>
                <div>Authorized Signatory</div>
            </div>
        </div>

    </div>

</body>

</html>
