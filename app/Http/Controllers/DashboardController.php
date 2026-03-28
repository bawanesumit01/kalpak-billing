<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats counts
        $totalProducts = DB::table('products')->count();
        $totalStaff = DB::table('users')->where('role', 'staff')->count();
        $totalStores = DB::table('stores')->count();

        // Store-wise total sales
        $storeSales = DB::table('stores as s')
            ->leftJoin('invoices as i', 'i.store_id', '=', 's.id')
            ->select('s.name', DB::raw('IFNULL(SUM(i.total_amount), 0) as total'))
            ->groupBy('s.id', 's.name')
            ->get();

        // Store-wise today's sales
        $todaySales = DB::table('stores as s')
            ->leftJoin('invoices as i', function ($join) {
                $join->on('i.store_id', '=', 's.id')
                    ->whereDate('i.created_at', today());
            })
            ->select('s.id', 's.name', DB::raw('IFNULL(SUM(i.total_amount), 0) as total'))
            ->groupBy('s.id', 's.name')
            ->get()
            ->keyBy('id');  // key by store id for easy access in blade

        // Low stock products
        $lowStock = DB::table('products as p')
            ->join('stores as s', 's.id', '=', 'p.store_id')
            ->select('p.name', 's.name as store_name', 'p.stock')
            ->where('p.stock', '<', 10)
            ->orderBy('p.stock', 'asc')
            ->limit(10)
            ->get();

        // Chart data - store wise sales
        $chartLabels = $storeSales->pluck('name');
        $chartTotals = $storeSales->pluck('total');

        return view('dashboard.index', compact(
            'totalProducts',
            'totalStaff',
            'totalStores',
            'storeSales',
            'todaySales',
            'lowStock',
            'chartLabels',
            'chartTotals'
        ));
    }
}
