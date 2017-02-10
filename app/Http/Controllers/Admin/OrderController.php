<?php

/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 06/02/2017
 * Time: 04:53
 */
namespace App\Http\Controllers\Admin;

use App\Gelsin\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function index()
    {
        $orders = Order::all();

        foreach ($orders as $order) {

            $order->detail;
            $order->products;
            $order->customer;

            foreach ($order->products as $product) {
                $product->relatedProduct;
            }
        }


        return new JsonResponse([
            "error" => false,
            'message' => 'success',
            'orders' => $orders,
        ]);
    }

}