<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 12/01/2017
 * Time: 13:40
 */

namespace App\Http\Middleware;


use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class Courier
{

    protected $auth;
    protected $jwt;


    public function __construct(Guard $auth, JWTAuth $jwt)
    {
        $this->auth = $auth;
        $this->jwt = $jwt;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $permissions
     * @return mixed
     */
    public function handle($request, \Closure $next, $permissions = '')
    {

        $user = $this->jwt->parseToken()->authenticate();

        if (!$user->is_courier == 1) {

            return new JsonResponse([
                "error" => true,
                'message' => "You don't have permission to do this! This user is not a courier!",
            ]);

        }

        return $next($request);
    }

}