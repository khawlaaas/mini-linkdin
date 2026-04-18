<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsRecruteur
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'recruteur') {
            return response()->json(['message' => 'Accès refusé — recruteur uniquement'], 403);
        }
        return $next($request);
    }
}
