<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\OtherProduct;
use App\Models\Store;

class BillingController extends Controller
{
    // ── SHOW BILLING PAGE ─────────────────────────────────────────
    public function index()
    {
        $storeId = session('store_id');
        $storeName = session('store_name', 'Store');

        if (!$storeId) {
            return redirect()->route('dashboard')
                ->with('error', 'Store not assigned to your account.');
        }

        return view('billing.index', compact('storeId', 'storeName'));
    }

    // ── AJAX: SEARCH PRODUCTS BY NAME OR CODE ─────────────────────
    public function searchProduct(Request $request)
    {
       
        $query = trim($request->get('q', ''));
        $storeId = intval($request->get('store', session('store_id')));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Products table
        $products = Product::where('store_id', $storeId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('product_code', 'like', "%{$query}%");
            })
            ->select('id', 'product_code', 'name', 'price', 'gst_rate', 'stock')
            ->limit(5)
            ->get()
            ->map(function ($p) {
                $p->source_table = 'products';
                return $p;
            });

        // Other Products table
        $otherProducts = OtherProduct::where('store_id', $storeId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('product_code', 'like', "%{$query}%");
            })
            ->select('id', 'product_code', 'name', 'price', 'gst_rate', 'stock')
            ->limit(5)
            ->get()
            ->map(function ($p) {
                $p->source_table = 'other_products';
                return $p;
            });

        // Merge both
        $result = $products->merge($otherProducts)->take(10)->values();

        return response()->json($result);
    }

    // ── AJAX: SAVE INVOICE ────────────────────────────────────────
    public function saveInvoice(Request $request)
    {
        $request->validate([
            'invoice_items' => 'required|string',
            'customer_phone' => 'required|digits:10',
            'payment_mode' => 'required|string',
            'invoice_discount' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
        ]);

        $storeId = session('store_id');
        $userId = session('user_id');
        $items = json_decode($request->invoice_items, true);

        if (empty($items)) {
            return response()->json(['status' => 'error', 'msg' => 'No items in invoice.']);
        }

        // Calculate totals
        $subtotal = 0;
        $gstTotal = 0;
        foreach ($items as $item) {
            $base = floatval($item['unit_price']) * intval($item['qty']);
            $gstAmt = $base * (floatval($item['gst_rate']) / 100);
            $subtotal += $base;
            $gstTotal += $gstAmt;
        }

        $discount = min(floatval($request->invoice_discount ?? 0), $subtotal);
        $grandTotal = ($subtotal - $discount) + $gstTotal;

        // Generate invoice number (per store, sequential)
        $lastNo = Invoice::where('store_id', $storeId)->max('invoice_no');
        $invoiceNo = $lastNo ? (intval($lastNo) + 1) : 1;
        
        if($request->payment_mode == 'Pay Later'){
            $status = 'Pending';
        }else{
            $status = 'Paid';
        }

        DB::beginTransaction();
        try {
            // Create invoice using Model
            $invoice = Invoice::create([
                'invoice_no' => $invoiceNo,
                'store_id' => $storeId,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'payment_mode' => $request->payment_mode,
                'subtotal' => round($subtotal, 2),
                'discount_total' => round($discount, 2),
                'invoice_discount_amount' => round($discount, 2),
                'gst_total' => round($gstTotal, 2),
                'total_amount' => round($grandTotal, 2),
                'status' => $status,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            // Insert invoice items + deduct stock
            foreach ($items as $item) {
                $base = floatval($item['unit_price']) * intval($item['qty']);
                $gstAmt = $base * (floatval($item['gst_rate']) / 100);
                $lineTotal = $base + $gstAmt;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => intval($item['product_id']),
                    'product_code' => $item['product_code'],
                    'product_name' => $item['product_name'],
                    'unit_price' => floatval($item['unit_price']),
                    'qty' => intval($item['qty']),
                    'gst_amount' => round($gstAmt, 2),
                    'total_amount' => round($lineTotal, 2),
                ]);

                // Deduct stock (only DB products, skip manual entries)
                if (intval($item['product_id']) > 0 && ($item['source_table'] ?? '') !== 'manual') {
                    Product::where('id', $item['product_id'])
                        ->decrement('stock', intval($item['qty']));
                }
            }
            
            $date = now()->toDateString();

            // Only add to cash_transactions if NOT Pay Later
            if ($request->payment_mode !== 'Pay Later') {
            
                DB::table('cash_transactions')->insert([
                    'store_id'             => $storeId,
                    'transaction_date'     => $date,
                    'transaction_type'     => 'add',
                    'amount'               => $grandTotal,
                    'description'          => 'Invoice #' . $invoiceNo,
                    'transaction_category' => 'sales',
                    'created_by'           => $userId,
                    'created_at'           => now(),
                ]);
            }

            DB::commit();
            
            

            return response()->json([
                'status' => 'success',
                'invoice_id' => $invoice->id,
                'invoice_no' => $invoiceNo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    // ── SHOW INVOICE / PDF ────────────────────────────────────────
    public function showInvoice($id)
    {
        $invoice = Invoice::with('store')->find($id);

        if (!$invoice) {
            abort(404, 'Invoice not found.');
        }

        $items = InvoiceItem::where('invoice_id', $id)->get();

        return view('billing.invoice', compact('invoice', 'items'));
    }

    // ── INVOICE LIST ──────────────────────────────────────────────
    public function invoiceList(Request $request)
    {
        $today = now()->toDateString();
        $role = session('role', 'staff');

        $fromDate = $request->get('from_date', $today);
        $toDate = $request->get('to_date', $today);
        $search = trim($request->get('search', ''));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate))
            $fromDate = $today;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate))
            $toDate = $today;

        // Staff locked to own store — admin can filter freely
        $selectedStore = ($role === 'staff')
            ? intval(session('store_id', 0))
            : intval($request->get('store_id', 0));

        $stores = Store::orderBy('name')->get();

        $query = Invoice::with(['store', 'createdBy'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate]);

        if ($selectedStore > 0) {
            $query->where('store_id', $selectedStore);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $invoices = $query->orderByDesc('id')->get();
        $totalCount = $invoices->count();
        $totalAmount = $invoices->sum('total_amount');

        return view('billing.invoice_list', compact(
            'invoices',
            'stores',
            'fromDate',
            'toDate',
            'selectedStore',
            'search',
            'totalAmount',
            'totalCount',
            'role'
        ));
    }
    
    public function markPaid($id)
{
    $invoice = Invoice::findOrFail($id);
    $invoice->status = 'Paid';
    $invoice->save();

    return back()->with('success', 'Invoice marked as paid');
}
}