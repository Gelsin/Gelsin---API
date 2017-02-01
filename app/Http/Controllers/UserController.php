<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 31/01/2017
 * Time: 14:02
 */

namespace App\Http\Controllers;


use App\Gelsin\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class UserController extends Controller
{

    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }


    /**
     * List products according to selected category.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $user = $this->jwt->parseToken()->authenticate();

        $orders = $user->orders;
        if ($orders->count() < 1) {

            return new JsonResponse([
                "error" => true,
                'message' => "youu don't have any orders",
                'orders' => $orders,
            ]);
        }

        // All good so list user orders
        foreach ($orders as $order) {
            $order->detail;
            $order->products;
        }

        return new JsonResponse([
            "error" => false,
            'message' => 'success',
            'orders' => $orders,
        ]);

    }


    /**
     * Update category.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {

        // -- define required parameters
        $rules = [
            'email' => 'unique:users|email|max:255',
            'username' => 'unique:users',
        ];

        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->first()
            ]);
        }


        // All good so update order and order detail
        $user = User::find($request->get('user_id'));

        if ($user->is_customer == 1) {

            $user->email = $request->get('email');
            $user->username = $request->get('username');
            $user->save();

            $customer_detail = $user->customerDetail;
            $customer_detail->first_name = $request->get("first_name");
            $customer_detail->last_name = $request->get("last_name");
            $customer_detail->contact = $request->get("contact");
            $customer_detail->save();

            $error = false;
            $message = "Your profile is updated!";
            $user->customerDetail;
        } else if ($user->is_customer == 0) {

            $error = false;
            $message = "user is seller";
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