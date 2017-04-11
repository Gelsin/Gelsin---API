<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 31/01/2017
 * Time: 14:02
 */

namespace App\Http\Controllers\Admin;

use App\Gelsin\Models\Courier;
use App\Gelsin\Models\Customer;
use App\Http\Controllers\Controller;
use App\Gelsin\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class UserController extends Controller
{

    protected $jwt;
    protected $user;
    protected $customer;
    protected $courier;

    public function __construct(JWTAuth $jwt, User $user, Customer $customer, Courier $courier)
    {
        $this->jwt = $jwt;
        $this->user = $user;
        $this->customer = $customer;
        $this->courier = $courier;

    }


    /**
     * Display couriers and customers.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {

        $type = $request->user_type;
        // type = 1; -> Customer
        // type = 2; -> Courier

        if ($type == '1') {
            $users = User::customers()->get();
        }
        if ($type == '2') {
            $users = User::couriers()->get();
        }
        return new JsonResponse([
            "error" => false,
            'message' => 'success',
            'users' => $users,
        ]);

    }

    /**
     * Create new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {

        // -- define required parameters
        $rules = [
            'user_type' => 'required',
            'email' => 'unique:users|required',
            'password' => 'required',
            'contact' => 'required',
        ];

        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->all()
            ]);
        }

        $type = $request->user_type;

        $activation_code = rand(100000, 999999);
        $this->user->email = $request->get("email");
        $this->user->password = app('hash')->make($request->get("password"));
        $this->user->verification_code = $activation_code;
        $this->user->confirmed_at = Carbon::now();
        if ($type == 2)
            $this->user->is_courier = 1;
        $this->user->save();

        if ($type == 1) {
            $this->customer->user_id = $this->user->id;
            $this->customer->fullname = $request->get("fullname");
            $this->customer->contact = $request->get("contact");
            $this->customer->save();
        }

        if ($type == 2) {

            $this->courier->user_id = $this->user->id;
            $this->courier->fullname = $request->get("fullname");
            $this->courier->contact = $request->get("contact");
            $this->courier->save();


        }

        return new JsonResponse([
            "error" => false,
            'message' => 'user is created',
            'users' => $this->user,
        ]);

    }

    /**
     * Update user.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        // -- define required parameters
        $rules = [
            'email' => 'email|max:255',
        ];


        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->first()
            ]);
        }
        // Get auth. user
        $user = $this->jwt->authenticate();
        // All good so update order and order detail

        if ($user->is_customer == 1) {

            if ($request->get('email')) {
                $user->email = $request->get('email');
            }

            $user->save();

            $customer_detail = $user->customerDetail;
            if ($request->get('fullname')) {
                $customer_detail->fullname = $request->get('fullname');
            }

            if ($request->get('contact')) {
                $customer_detail->contact = $request->get('contact');
            }
            $customer_detail->save();
            $error = false;
            $message = "Profile is updated!";
            $user->customerDetail;
        } else if ($user->is_customer == 0) {

            $error = true;
            $message = "user is seller! You don't have permission to update profile!";
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
     * Delete   product.
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {


        // All good so update category
        $order = Order::find($request->get('order_id'));
        $order->detail->delete();
        $order->products->delete();
        $order->delete();


        return new JsonResponse([
            "error" => false,
            'message' => "Selected order is soft deleted",
            "category" => $order
        ]);

    }


}