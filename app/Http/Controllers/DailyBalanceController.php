<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyBalanceController extends Controller
{
    // ── Get opening balance (yesterday's closing) ─────────────────
    private function getOpeningBalance($storeId, $date)
    {
        // First check if today's record exists
        $today = DB::table('daily_balance_flow')
            ->where('store_id', $storeId)
            ->where('balance_date', $date)
            ->value('opening_balance');

        if (!is_null($today)) {
            return floatval($today);
        }

        // Otherwise use yesterday's closing balance
        $yesterday = date('Y-m-d', strtotime($date . ' -1 day'));
        $closing   = DB::table('daily_balance_flow')
            ->where('store_id', $storeId)
            ->where('balance_date', $yesterday)
            ->value('closing_balance');

        return floatval($closing ?? 0);
    }

    // ── Ensure today's balance record exists ──────────────────────
    private function ensureBalanceRecord($storeId, $date, $userId)
    {
        $exists = DB::table('daily_balance_flow')
            ->where('store_id', $storeId)
            ->where('balance_date', $date)
            ->exists();

        if (!$exists) {
            $opening = $this->getOpeningBalance($storeId, $date);

            DB::table('daily_balance_flow')->insert([
                'store_id'        => $storeId,
                'balance_date'    => $date,
                'opening_balance' => $opening,
                'created_by'      => $userId,
            ]);
        }
    }

    // ── Save closing balance ──────────────────────────────────────
    private function saveClosingBalance($storeId, $date)
    {
        $breakdown = $this->getBalanceBreakdown($storeId, $date);

        DB::table('daily_balance_flow')
            ->where('store_id', $storeId)
            ->where('balance_date', $date)
            ->update(['closing_balance' => $breakdown['cash_online']]);
    }

    // ── Get current balance (used for remove-cash check) ─────────
    private function getCurrentBalance($storeId, $date)
    {
        return $this->getBalanceBreakdown($storeId, $date)['cash_online'];
    }

    // ── Full balance breakdown ────────────────────────────────────
    private function getBalanceBreakdown($storeId, $date)
    {
        $opening = $this->getOpeningBalance($storeId, $date);

        // Manual cash additions (non-invoice)
        $manualAdds = DB::table('cash_transactions')
            ->where('store_id', $storeId)
            ->where('transaction_date', $date)
            ->where('transaction_type', 'add')
            ->where(function ($q) {
                $q->whereNull('description')
                  ->orWhere('description', 'not like', 'Invoice #%');
            })
            ->sum('amount');

        // Manual removals
        $manualRemovals = DB::table('cash_transactions')
            ->where('store_id', $storeId)
            ->where('transaction_date', $date)
            ->where('transaction_type', 'remove')
            ->sum('amount');

        // Cash & UPI invoice sales (actual money received)
        $cashOnlineSales = DB::table('invoices')
            ->where('store_id', $storeId)
            ->whereDate('created_at', $date)
            ->whereIn('payment_mode', ['Cash', 'UPI', 'Card'])
            ->sum('total_amount');

        // Pay Later invoices (money NOT yet received)
        $payLater = DB::table('invoices')
            ->where('store_id', $storeId)
            ->whereDate('created_at', $date)
            ->where('payment_mode', 'Pay Later')
            ->sum('total_amount');

        // Today's invoice count breakdown
        $invoiceCounts = DB::table('invoices')
            ->where('store_id', $storeId)
            ->whereDate('created_at', $date)
            ->selectRaw("
                COUNT(*) as total_invoices,
                SUM(CASE WHEN payment_mode IN ('Cash','UPI','Card') THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN payment_mode = 'Pay Later' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN payment_mode IN ('Cash','UPI','Card') THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN payment_mode = 'Pay Later' THEN total_amount ELSE 0 END) as pending_amount
            ")
            ->first();

        $cashOnlineBalance = $opening + $manualAdds + $cashOnlineSales - $manualRemovals;

        return [
            'opening'           => floatval($opening),
            'manual_adds'       => floatval($manualAdds),
            'manual_removals'   => floatval($manualRemovals),
            'cash_online_sales' => floatval($cashOnlineSales),
            'cash_online'       => floatval($cashOnlineBalance),
            'pay_later'         => floatval($payLater),
            'invoice_counts'    => $invoiceCounts,
        ];
    }

    // ── INDEX ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $role   = session('role', 'staff');
        $userId = session('user_id', 0);
        $today  = now()->toDateString();

        if ($role === 'admin') {
            $selectedStore = intval($request->get('store_id', 0));
            $selectedDate  = $request->get('date', $today);
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
                $selectedDate = $today;
            }
            $stores = DB::table('stores')->orderBy('name')->get();
        } else {
            $selectedStore = intval(session('store_id', 0));
            $selectedDate  = $today;
            $stores        = collect();

            if ($selectedStore > 0) {
                $this->ensureBalanceRecord($selectedStore, $selectedDate, $userId);
            }
        }

        $todayBalance    = null;
        $transactions    = collect();
        $breakdown       = [];
        $currentBalance  = 0;
        $payLaterTotal   = 0;
        $storeName       = 'All Stores';

        if ($selectedStore > 0) {
            $todayBalance = DB::table('daily_balance_flow')
                ->where('store_id', $selectedStore)
                ->where('balance_date', $selectedDate)
                ->first();

            $transactions = DB::table('cash_transactions as ct')
                ->leftJoin('users as u', 'u.id', '=', 'ct.created_by')
                ->leftJoin('stores as s', 's.id', '=', 'ct.store_id')
                ->select('ct.*', 'u.username as staff_name', 's.name as store_name')
                ->where('ct.store_id', $selectedStore)
                ->where('ct.transaction_date', $selectedDate)
                ->where(function ($q) {
                    $q->whereNull('ct.description')
                      ->orWhere('ct.description', 'not like', 'Invoice #%');
                })
                ->orderByDesc('ct.created_at')
                ->get();

            $breakdown      = $this->getBalanceBreakdown($selectedStore, $selectedDate);
            $currentBalance = $breakdown['cash_online'];
            $payLaterTotal  = $breakdown['pay_later'];
            $storeName      = DB::table('stores')->where('id', $selectedStore)->value('name') ?? 'Unknown';
        }

        return view('daily_balance.index', compact(
            'role', 'stores', 'selectedStore', 'selectedDate',
            'todayBalance', 'transactions', 'currentBalance',
            'payLaterTotal', 'breakdown', 'storeName', 'today'
        ));
    }

    // ── ADD CASH ──────────────────────────────────────────────────
    public function addCash(Request $request)
    {
        $request->validate([
            'store_id'    => 'required|exists:stores,id',
            'date'        => 'required|date',
            'amount'      => 'required|numeric|min:0.01',
            'category'    => 'required|in:sales,other_income,other',
            'description' => 'nullable|string|max:255',
        ]);

        $storeId = $request->store_id;
        $date    = $request->date;
        $userId  = session('user_id', 0);

        $this->ensureBalanceRecord($storeId, $date, $userId);

        DB::table('cash_transactions')->insert([
            'store_id'             => $storeId,
            'transaction_date'     => $date,
            'transaction_type'     => 'add',
            'amount'               => $request->amount,
            'description'          => $request->description,
            'transaction_category' => $request->category,
            'created_by'           => $userId,
        ]);

        $this->saveClosingBalance($storeId, $date);

        return redirect()
            ->route('daily-balance.index', ['store_id' => $storeId, 'date' => $date])
            ->with('success', 'Cash added successfully!');
    }

    // ── REMOVE CASH ───────────────────────────────────────────────
    public function removeCash(Request $request)
    {
        $request->validate([
            'store_id'    => 'required|exists:stores,id',
            'date'        => 'required|date',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $storeId        = $request->store_id;
        $date           = $request->date;
        $currentBalance = $this->getCurrentBalance($storeId, $date);

        if ($request->amount > $currentBalance) {
            return back()->withInput()->with('error',
                'Insufficient balance. Available: ₹' . number_format($currentBalance, 2));
        }

        DB::table('cash_transactions')->insert([
            'store_id'             => $storeId,
            'transaction_date'     => $date,
            'transaction_type'     => 'remove',
            'amount'               => $request->amount,
            'description'          => $request->description,
            'transaction_category' => 'owner_withdrawal',
            'created_by'           => session('user_id', 0),
        ]);

        $this->saveClosingBalance($storeId, $date);

        return redirect()
            ->route('daily-balance.index', ['store_id' => $storeId, 'date' => $date])
            ->with('success', 'Cash removed successfully!');
    }
}