<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OtherProductSalesController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $fromDate      = $request->get('from_date', $today);
        $toDate        = $request->get('to_date', $today);
        $selectedStore = intval($request->get('store_id', 0));

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) $fromDate = $today;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate))   $toDate   = $today;

        // Get all stores for dropdown
        $stores = DB::table('stores')->orderBy('name')->get();

        // Build sales query
        $query = DB::table('invoice_items as ii')
            ->join('invoices as i', 'i.id', '=', 'ii.invoice_id')
            ->join('other_product as op', function ($join) {
                $join->on('op.id', '=', 'ii.product_id')
                     ->where('op.is_deleted', 0)
                     ->whereColumn('op.store_id', 'i.store_id');
            })
            ->leftJoin('stores as s', 's.id', '=', 'i.store_id')
            ->leftJoin('users as u', 'u.id', '=', 'i.created_by')
            ->select(
                'ii.id',
                'ii.invoice_id',
                'i.invoice_no',
                'i.store_id',
                's.name as store_name',
                'i.customer_name',
                'i.customer_phone',
                'i.payment_mode',
                'i.created_at as invoice_date',
                'ii.product_id',
                'ii.product_code',
                'ii.product_name',
                'ii.qty',
                'ii.unit_price',
                'ii.total_amount',
                'u.username as staff_name',
                'op.name as other_product_name'
            )
            ->whereBetween(DB::raw('DATE(i.created_at)'), [$fromDate, $toDate]);

        if ($selectedStore > 0) {
            $query->where('i.store_id', $selectedStore);
        }

        $sales = $query->orderByDesc('i.created_at')
                       ->orderByDesc('i.invoice_no')
                       ->limit(500)
                       ->get();

        // Summary totals
        $totalQty    = $sales->sum('qty');
        $totalAmount = $sales->sum('total_amount');

        return view('other_product_sales.index', compact(
            'sales',
            'stores',
            'fromDate',
            'toDate',
            'selectedStore',
            'totalQty',
            'totalAmount'
        ));
    }
}