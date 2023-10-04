<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

use App\Models\User;

/**
 * AuthCheck middleware handles authentication with API key
 */
class AuthCheck extends Middleware
{
    public function handle($request, \Closure $next, ...$guards)
    {
        //API key is transmitted as bearer token
        $apiKey = $request->bearerToken();

        if($apiKey) {
            $user = User::where('api_key','=',$apiKey)->first();
            if($user) {
                //Log in as this user and proceed
                auth()->login($user);
                return $next($request);
            } else {
                //No user account for this API key, so unauthorised
                return response()->json(['error' => 'Invalid authorisation.'], 401);
            }
        } else {
            //No API key, so unauthorised
            return response()->json(['error' => 'Invalid authorisation.'], 401);
        }
    }
}
