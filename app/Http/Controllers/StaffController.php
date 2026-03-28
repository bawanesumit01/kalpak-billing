<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    // LIST ALL STAFF + CREATE FORM
    public function index()
    {
        $stores = DB::table('stores')->orderBy('name')->get();

        $staff = DB::table('users as u')
            ->leftJoin('stores as s', 's.id', '=', 'u.store_id')
            ->select('u.*', 's.name as store_name')
            ->where('u.role', 'staff')
            ->orderByDesc('u.created_at')
            ->get();

        return view('staff.index', compact('stores', 'staff'));
    }

    // SAVE NEW STAFF
    public function store(Request $request)
    {
        $request->validate([
            'username'  => 'required|string|max:100|unique:users,username',
            'password'  => 'required|string|min:4',
            'full_name' => 'nullable|string|max:255',
            'store_id'  => 'required|exists:stores,id',
        ], [
            'username.unique' => 'This username already exists.',
            'store_id.required' => 'Please select a store for the staff.',
        ]);

        DB::table('users')->insert([
            'username'   => trim($request->username),
            'password'   => Hash::make($request->password),
            'full_name'  => trim($request->full_name ?? ''),
            'store_id'   => $request->store_id,
            'role'       => 'staff',
            'created_at' => now(),
        ]);

        return redirect()->route('staff.index')
                         ->with('success', 'Staff user created successfully!');
    }

    // SHOW EDIT FORM
    public function edit($id)
    {
        $user = DB::table('users')
            ->where('id', $id)
            ->where('role', 'staff')
            ->first();

        if (!$user) {
            return redirect()->route('staff.index')
                             ->with('error', 'Staff user not found!');
        }

        $stores = DB::table('stores')->orderBy('name')->get();

        return view('staff.edit', compact('user', 'stores'));
    }

    // UPDATE STAFF
    public function update(Request $request, $id)
    {
        $request->validate([
            'username'  => 'required|string|max:100|unique:users,username,' . $id,
            'full_name' => 'nullable|string|max:255',
            'store_id'  => 'required|exists:stores,id',
        ], [
            'username.unique' => 'This username is already taken.',
            'store_id.required' => 'Please select a valid store.',
        ]);

        DB::table('users')->where('id', $id)->update([
            'full_name' => trim($request->full_name ?? ''),
            'username'  => trim($request->username),
            'store_id'  => $request->store_id,
        ]);

        return redirect()->route('staff.index')
                         ->with('success', 'Staff user updated successfully!');
    }

    // DELETE STAFF (only role = staff)
    public function destroy($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return redirect()->route('staff.index')
                             ->with('error', 'User not found!');
        }

        if ($user->role !== 'staff') {
            return redirect()->route('staff.index')
                             ->with('error', 'Only staff accounts can be deleted!');
        }

        DB::table('users')->where('id', $id)->delete();

        return redirect()->route('staff.index')
                         ->with('success', 'Staff user deleted successfully!');
    }
}