<?php

namespace App\Http\Controllers;


use App\Gelsin\Helpers\SmsSender;
use App\Gelsin\Models\Customer;
use App\Gelsin\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{
    protected $user;
    protected $customer;
    protected $smsSender;


    /**
     * AuthController constructor.
     * @param User $user
     * @param Customer $customer
     * @param SmsSender $smsSender
     */
    public function __construct(User $user, Customer $customer, SmsSender $smsSender)
    {
        $this->user = $user;
        $this->customer = $customer;
        $this->smsSender = $smsSender;
    }

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
        $user = JWTAuth::parseToken()->authenticate();

        if ($user->is_customer == 1) {

            $error = false;
            $message = "authenticated user is customer";
            $user->customerDetail;
        } else if ($user->is_customer == 0) {

            $error = false;
            $message = "authenticated user is seller";
        } else {
            $error = true;
            $message = "user type is not defined";
        }



        return new JsonResponse([
            'error' => $error,
            'message' => $message,
            'user' => $user
        ]);
    }

    /**
     * Register new user.
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {

        // -- define required parameters
        $rules = [
            'email' => 'required|unique:users|email|max:255',
            'password' => 'required|min:6',
            'fullname' => 'required',
            'contact' => 'required',
        ];

        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->first()
            ]);
        }

        $activation_code = rand(100000, 999999);
        $this->user->email = $request->get("email");
        $this->user->password = app('hash')->make($request->get("password"));
        $this->user->verification_code = $activation_code;
        $this->user->save();

        $this->customer->user_id = $this->user->id;
        $this->customer->fullname = $request->get("fullname");
        $this->customer->contact = $request->get("contact");
        $this->customer->save();

        // -- send sms with activation code
        $this->smsSender->activation($this->customer, $activation_code);

        $this->user->customerDetail;

        return new JsonResponse([
            "error" => false,
            'message' => 'user is created and activation code has sent, successfully!',
            'user' => $this->user,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function activate(Request $request)
    {
        // -- define required parameters
        $rules = [
            'activation_code' => 'required|min:6|max:6',
        ];

        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->first()
            ]);
        }
        $activationCode = $request->get('activation_code');
        $user = JWTAuth::parseToken()->authenticate();

        if ($user && $user->verification_code === $activationCode) {
            $user->confirmed_at = Carbon::now();
            $user->save();

            return new JsonResponse([
                "error" => false,
                'message' => 'Thanks! You have succesfull activated your account!',
                'user' => $user,
            ]);
        }

        return new JsonResponse([
            "error" => true,
            'message' => 'Verification code error. Please, Check your code!',
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function resendCode()
    {

        $activation_code = rand(100000, 999999);
        $user = JWTAuth::parseToken()->authenticate();
        $user->verification_code = $activation_code;
        $user->save();
        $customer = $user->customerDetail;
        $this->smsSender->activation($customer, $activation_code);

        return new JsonResponse([
            "error" => false,
            'message' => 'Thanks! Activation code has successfully resent!',
            'user' => $user,
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {

    }
}