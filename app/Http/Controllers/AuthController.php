<?php

namespace App\Http\Controllers;

use App\Gelsin\Repositories\UsersRepositoryInterface;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;
    protected $user;


    /**
     * AuthController constructor.
     * @param JWTAuth $jwt
     * @param UsersRepositoryInterface $user
     */
    public function __construct(JWTAuth $jwt, UsersRepositoryInterface $user)
    {
        $this->jwt = $jwt;
        $this->user = $user;
    }

    public function loginPost(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {
            if (! $token = $this->jwt->attempt($request->only('email', 'password'))) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage()], $e->getStatusCode());
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {

        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
            'username' => 'required',
        ]);
        $this->user->create($request);
    }
}