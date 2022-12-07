<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

class CurrentUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $request->setUserResolver(function () {
            if ($token = request()->bearerToken()) {
                $model = Sanctum::$personalAccessTokenModel;

                $accessToken = $model::findToken($token);

                if (!$accessToken) {
                    return null;
                }

                return optional($accessToken->tokenable)->withAccessToken(
                    tap($accessToken->forceFill(['last_used_at' => now()]))->save()
                );
            }

            return null;

        });

        return $next($request);
    }
}
