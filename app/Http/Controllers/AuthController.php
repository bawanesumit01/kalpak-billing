<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // SHOW LOGIN FORM
    public function showLogin()
    {
        // If already logged in, redirect
        if (session('user_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // HANDLE LOGIN
    public function login(Request $request)
    {
        
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username
        $user = DB::table('users')
            ->where('username', trim($request->username))
            ->first();

        // Check user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withInput(['username' => $request->username])
                ->with('error', 'Invalid username or password.');
        }

        // Set session values
        session([
            'user_id'    => $user->id,
            'username'   => $user->username,
            'full_name'  => $user->full_name ?? $user->username,
            'role'       => $user->role,
            'store_id'   => $user->store_id,
            'store_name' => $user->store_id
                ? DB::table('stores')->where('id', $user->store_id)->value('name')
                : null,
        ]);

        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->route('dashboard')->with('success', 'Welcome back, ' . ($user->full_name ?? $user->username) . '!');
        }

        return redirect()->route('billing.index')->with('success', 'Welcome, ' . ($user->full_name ?? $user->username) . '!');
    }

    // LOGOUT
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}