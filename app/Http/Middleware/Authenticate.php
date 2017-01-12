<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {

            try {

                $user = $this->jwt->parseToken()->authenticate();

                if (!$user) {

                    return new JsonResponse([
                        "error" => true,
                        'message' => 'user_not_found',
                    ]);
                }

            } catch (TokenInvalidException $e) {

                return new JsonResponse([
                    "error" => true,
                    'message' => 'token_absent',
                ]);

            } catch (TokenExpiredException $e) {

                return new JsonResponse([
                    "error" => true,
                    'message' => 'token_expired',
                ]);
            }
        }

        return $next($request);
    }
}
