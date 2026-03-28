<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLogin
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Not logged in
        if (!session('user_id')) {
            return redirect()->route('login')
                             ->with('error', 'Please login to continue.');
        }

        // Role check (if roles are specified)
        if (!empty($roles) && !in_array(session('role'), $roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}