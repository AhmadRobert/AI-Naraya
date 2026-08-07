<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDeveloperAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validKey = env('DEVELOPER_ACCESS_KEY');
        $providedKey = $request->input('key') ?? $request->query('key');

        // Strictly check if valid key is passed via parameter on every request (No session)
        if (empty($validKey) || ! hash_equals((string) $validKey, (string) $providedKey)) {
            abort(404);
        }

        return $next($request);
    }
}
