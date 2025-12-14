<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceVisibility
{
    /**
     * Handle an incoming request.
     *
     * This middleware ensures that models with visibility settings
     * are properly filtered based on the current user's role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // SysAdmin bypasses visibility checks
        if (auth()->user()->isSysAdmin()) {
            return $next($request);
        }

        // For other users, visibility will be enforced at query level
        // using the HasVisibility trait's scopeVisibleTo method

        return $next($request);
    }
}
