<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Super admin is immune, but normal users and UMKM admins are blocked if their company is inactive.
        if ($user && $user->role !== 'super_admin' && $user->company && !$user->company->is_active) {
            abort(403, 'Your company account has been blocked or deactivated. Please contact support.');
        }

        return $next($request);
    }
}
