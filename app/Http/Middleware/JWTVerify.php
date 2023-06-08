<?php

namespace App\Http\Middleware;

use App\Services\Security\JWTAuth;
use Closure;
use Illuminate\Http\Request;

class JWTVerify
{
    public function handle(Request $request, Closure $next, $permission = 'is_accountant')
    {
        if(!JWTAuth::user() && str_contains($request->url(), 'api')){
            return response()->json([
                'status' => 'Forbidden',
                'message' => 'You don\'t have access here'
            ], 403);
        }

        if(JWTAuth::validate($permission)){
            return $next($request);
        }

        return to_route('login');
    }
}
