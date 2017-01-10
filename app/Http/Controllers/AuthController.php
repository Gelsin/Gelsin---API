<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Exception\HttpResponseException;


class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function postLogin(Request $request)
    {
        try {
            $this->validatePostLoginRequest($request);
        } catch (HttpResponseException $e) {
            return $this->onBadRequest();
        }
        try {
            // Attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt(
                $this->getCredentials($request)
            )) {
                return $this->onUnauthorized();
            }
        } catch (JWTException $e) {
            // Something went wrong whilst attempting to encode the token
            return $this->onJwtGenerationError();
        }
        // All good so return the token
        return $this->onAuthorized($token);
    }
    /**
     * Validate authentication request.
     *
     * @param  Request $request
     * @return void
     * @throws HttpResponseException
     */
    protected function validatePostLoginRequest(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);
    }
    /**
     * What response should be returned on bad request.
     *
     * @return JsonResponse
     */
    protected function onBadRequest()
    {
        return new JsonResponse([
            'message' => 'invalid_credentials'
        ], Response::HTTP_BAD_REQUEST);
    }
    /**
     * What response should be returned on invalid credentials.
     *
     * @return JsonResponse
     */
    protected function onUnauthorized()
    {
        return new JsonResponse([
            'message' => 'invalid_credentials'
        ], Response::HTTP_UNAUTHORIZED);
    }
    /**
     * What response should be returned on error while generate JWT.
     *
     * @return JsonResponse
     */
    protected function onJwtGenerationError()
    {
        return new JsonResponse([
            'message' => 'could_not_create_token'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    /**
     * What response should be returned on authorized.
     *
     * @return JsonResponse
     */
    protected function onAuthorized($token)
    {
        return new JsonResponse([
            'message' => 'token_generated',
            'data' => [
                'token' => $token,
            ]
        ]);
    }
    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only('email', 'password');
    }
    /**
     * Invalidate a token.
     *
     * @return JsonResponse
     */
    public function deleteInvalidate()
    {
        $token = JWTAuth::parseToken();
        $token->invalidate();
        return new JsonResponse(['message' => 'token_invalidated']);
    }
    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function patchRefresh()
    {
        $token = JWTAuth::parseToken();
        $newToken = $token->refresh();
        return new JsonResponse([
            'message' => 'token_refreshed',
            'data' => [
                'token' => $newToken
            ]
        ]);
    }
    /**
     * Get authenticated user.
     *
     * @return JsonResponse
     */
    public function getUser()
    {
        return new JsonResponse([
            'message' => 'authenticated_user',
            'data' => JWTAuth::parseToken()->authenticate()
        ]);
    }

    /**
     * Register new user.
     * @param Request $request
     */
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