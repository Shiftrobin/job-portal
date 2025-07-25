<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */

    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            if ($request->is('account') || $request->is('account/*')) {
                return route('account.login');
            }

            return route('account.login'); // default
        }
    }


    // protected function redirectTo(Request $request): ?string
    // {
    //     return $request->expectsJson() ? null : route('account.login');
    // }
}
