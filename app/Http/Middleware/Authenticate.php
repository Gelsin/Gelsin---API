<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;
    protected $jwt;


    /**
     * Create a new middleware instance.
     *
     * @param JWTAuth $jwt
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {

            $user = $this->jwt->parseToken()->authenticate();
            $token = $this->jwt->getToken();

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
        } catch (JWTException $e) {

            return new JsonResponse([
                "error" => true,
                'message' => 'token_absent',
            ]);

        }


        return $next($request);
    }
}
