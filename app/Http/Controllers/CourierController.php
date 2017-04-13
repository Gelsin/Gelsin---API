<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 11/01/2017
 * Time: 01:40
 */

namespace App\Http\Controllers;


use App\Gelsin\Models\Order;
use App\Gelsin\Models\OrderDetail;
use App\Gelsin\Models\OrderProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class CourierController extends Controller
{

    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }


    /**
     * Orders of couriers
     *
     * @return JsonResponse
     */
    public function orders()
    {

        $user = $this->jwt->parseToken()->authenticate();
        $courier = $user->courierDetail;
        $orders = $courier->orders;

        foreach ($orders as $order) {
            $order->detail;
            $order->products;
            foreach ($order->products as $product) {
                $product->relatedProduct;
            }
        }

        return new JsonResponse([
            "error" => false,
            'message' => 'Courier orders are displayed as below!',
            'orders' => $orders,
        ]);

    }


    /**
     * change status of order as 'Completed'
     * @param Request $request
     * @return JsonResponse
     */
    public function completeOrder(Request $request)
    {

        $order_id = $request->order_id;
        $order = Order::find($order_id);

        if (!$order) {
            return new JsonResponse([
                "error" => true,
                'message' => 'no such order exists!',
            ]);
        }

        $order->status = 4;
        $order->save();

        return new JsonResponse([
            "error" => false,
            'message' => 'Order status is marked as Completed!',
            'order' => $order,
        ]);

    }

}