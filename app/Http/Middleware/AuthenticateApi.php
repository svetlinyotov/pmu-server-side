<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AuthenticateApi
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (
            $request->hasHeader("AuthOrigin") &&
            $request->hasHeader("AccessToken") &&
            $request->hasHeader("AuthSocialId") &&
            User::isTokenValid($request->header("AuthOrigin"), $request->header("AuthSocialId"), $request->header("AccessToken"))
        ) {
            return $next($request);
        }

        return response()->json(["error" => "User not authorized"], 401);
    }
}
