<?php

/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 06/02/2017
 * Time: 04:53
 */
namespace App\Http\Controllers\Admin;

use App\Gelsin\Helpers\SmsSender;
use App\Gelsin\Models\Courier;
use App\Gelsin\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class OrderController extends Controller
{

    /**
     * @param null $status
     * @return JsonResponse
     */
    public function index($status = null)
    {

        $orders = Order::where('status', '=', $status)->get();

        if (!isset($status)) {
            $orders = Order::all();
        }

        foreach ($orders as $order) {

            $order->detail;
            $order->customer;

            foreach ($order->products as $product) {
                $product->relatedProduct;
            }
        }

        return new JsonResponse([
            "error" => false,
            'message' => 'Orders are listed below.',
            'orders' => $orders,
        ]);
    }

    /**
     * Set courier to order
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setCourier(Request $request)
    {


        $courier_id = $request->courier_id;
        $courier = Courier::find($courier_id);
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $customer = $order->customer->customerDetail;


        if (!$courier) {

            return new JsonResponse([
                "error" => true,
                'message' => 'no courier found!',
            ]);
        }
        if (!$order) {

            return new JsonResponse([
                "error" => true,
                'message' => 'no order found!',
            ]);
        }

        $order->courier_id = $courier_id;
        $order->status = 3;
        $order->save();
        $order->courier;

        $smsSender = new SmsSender();
        $smsSender->smsToCourier($courier, "Sizin yeni siparişiniz var! ");
        $smsSender->smsToCustomer($customer, "Sifarişiniz kuryere teslim edildi! ");


        return new JsonResponse([
            "error" => false,
            'message' => 'Courier is set to order',
            'order' => $order,
        ]);
    }

    /**
     * @param $order_id
     * @return JsonResponse
     * @internal param null $status
     */
    public function show($order_id)
    {
        $order = Order::find($order_id);

        if (!$order) {

            return new JsonResponse([
                "error" => true,
                'message' => 'There is no related order',
            ]);
        }

        $order->detail;
        $order->products;
        $order->customer;

        foreach ($order->products as $product) {
            $product->relatedProduct;
        }

        return new JsonResponse([
            "error" => false,
            'message' => 'Order relations listed below',
            'order' => $order,
        ]);
    }


    /**
     * Update category.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {


        // All good so update order and order detail
        $order = Order::find($request->get('order_id'));
        $order->status = $request->get('status');
        $order->save();


        return new JsonResponse([
            "error" => false,
            'message' => "Status updated!",
            "order" => $order
        ]);

    }

}