<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Please login as admin.');
        }

        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Access denied. Admins only.');
        }

        // if ($request->user() == null){
        //     return redirect()->route('home');
        // }

        // if ($request->user()->role != "admin") {
        //     session()->flash("error","You are not authorized to access this page.");
        //     return redirect()->route("account.profile");
        // }

        return $next($request);
    }
}
