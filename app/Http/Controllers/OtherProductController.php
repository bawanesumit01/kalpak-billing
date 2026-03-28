<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OtherProductController extends Controller
{
    // LIST ALL OTHER PRODUCTS (non-deleted)
    public function index()
    {
        $products = DB::table('other_product as p')
            ->join('stores as s', 's.id', '=', 'p.store_id')
            ->select('p.*', 's.name as store_name')
            ->where('p.is_deleted', 0)
            ->orderBy('s.name')
            ->orderBy('p.product_code')
            ->get();

        return view('other_products.index', compact('products'));
    }

    // SHOW ADD FORM
    public function create()
    {
        $stores = DB::table('stores')->orderBy('name')->get();
        return view('other_products.create', compact('stores'));
    }

    // SAVE NEW PRODUCT
    public function store(Request $request)
    {
        $request->validate([
            'store_id'     => 'required|exists:stores,id',
            'product_code' => 'required|string|max:100',
            'name'         => 'required|string|max:255',
            'price'        => 'required|numeric|min:0.01',
            'gst_rate'     => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
        ]);

        // Check duplicate product code for same store
        $exists = DB::table('other_product')
            ->where('store_id', $request->store_id)
            ->where('product_code', trim($request->product_code))
            ->where('is_deleted', 0)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['product_code' => 'Product code already exists for this store.']);
        }

        DB::table('other_product')->insert([
            'store_id'     => $request->store_id,
            'product_code' => trim($request->product_code),
            'name'         => trim($request->name),
            'price'        => $request->price,
            'gst_rate'     => $request->gst_rate,
            'stock'        => $request->stock,
            'is_deleted'   => 0,
        ]);

        return redirect()->route('other-products.index')
                         ->with('success', 'Product added successfully!');
    }

    // SHOW EDIT FORM
    public function edit($id)
    {
        $product = DB::table('other_product')
            ->where('id', $id)
            ->where('is_deleted', 0)
            ->first();

        if (!$product) {
            return redirect()->route('other-products.index')
                             ->with('error', 'Product not found!');
        }

        $stores = DB::table('stores')->orderBy('name')->get();
        return view('other_products.edit', compact('product', 'stores'));
    }

    // UPDATE PRODUCT
    public function update(Request $request, $id)
    {
        $request->validate([
            'store_id'     => 'required|exists:stores,id',
            'product_code' => 'required|string|max:100',
            'name'         => 'required|string|max:255',
            'price'        => 'required|numeric|min:0.01',
            'gst_rate'     => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
        ]);

        // Check duplicate product code (excluding current product)
        $exists = DB::table('other_product')
            ->where('store_id', $request->store_id)
            ->where('product_code', trim($request->product_code))
            ->where('id', '!=', $id)
            ->where('is_deleted', 0)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['product_code' => 'Product code already exists for this store.']);
        }

        DB::table('other_product')->where('id', $id)->update([
            'store_id'     => $request->store_id,
            'product_code' => trim($request->product_code),
            'name'         => trim($request->name),
            'price'        => $request->price,
            'gst_rate'     => $request->gst_rate,
            'stock'        => $request->stock,
            'modified_by'  => session('user_id'),
            'modify'       => now(),
        ]);

        return redirect()->route('other-products.index')
                         ->with('success', 'Product updated successfully!');
    }

    // SOFT DELETE
    public function destroy($id)
    {
        DB::table('other_product')->where('id', $id)->update([
            'is_deleted'  => 1,
            'modified_by' => session('user_id'),
            'modify'      => now(),
        ]);

        return redirect()->route('other-products.index')
                         ->with('success', 'Product deleted successfully!');
    }
}