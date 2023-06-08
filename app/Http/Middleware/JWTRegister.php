<?php

namespace App\Http\Middleware;

use App\Services\Security\JWTAuth;
use Closure;
use Illuminate\Http\Request;

class JWTRegister
{
    public function handle(Request $request, Closure $next)
    {
        if($request->bearerToken()){
            return $next($request);
        }

        if($cookie = $request->headers->get('cookie')){
            if($token = $this->getToken($cookie, "jwt_token=", ";")){
                $request->headers->set('Authorization', "Bearer $token");
                JWTAuth::setUserFromToken($token);
            }
        }

        return $next($request);
    }

    public function getToken($string, $start, $end): string
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
