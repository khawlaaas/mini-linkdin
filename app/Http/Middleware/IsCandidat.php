<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCandidat
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'candidat') {
            return response()->json(['message' => 'Accès refusé — candidat uniquement'], 403);
        }
        return $next($request);
    }
}
