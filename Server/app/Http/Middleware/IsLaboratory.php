<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsLaboratory
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('laboratory')->check()) {
            return $next($request);
        }

        // Optionally redirect or return an error
        return redirect('/login/laboratory'); // Or wherever you want to redirect
        // Or:  abort(403, 'Unauthorized.');
    }
}
