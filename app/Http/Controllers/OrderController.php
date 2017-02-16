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

class OrderController extends Controller
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
                'message' => "you don't have any orders",
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
     * Create  new order.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {

        $user = $this->jwt->parseToken()->authenticate();

        // -- define required parameters
        $rules = [
            'delivery_is_now' => 'required',
            'address' => 'required',
        ];

        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->all()
            ]);
        }



        // All good so create new order
        $products = $request->get("products");

        $order = new Order();
        $order->customer_id = $user->id;
        $order->save();

        foreach ($products as $product) {

            $total_price[] = $product['price'];

            // -- Save order
            $order_product = new OrderProduct();
            $order_product->order_id = $order->id;
            $order_product->product_id = $product['product_id'];
            $order_product->price = $product['price'];
            $order_product->quantity = $product['quantity'];
            $order_product->save();

            // -- Update product in stock
            $product = $order_product->relatedProduct;
            $product->quantity = $product->quantity - $order_product->quantity;
            $product->save();

        }

        // update total price
        $order->total_price = array_sum($total_price);
        $order->save();



        $order_detail = new OrderDetail();
        $order_detail->order_id = $order->id;
        $order_detail->delivery_is_now = $request->get("delivery_is_now");
        $order_detail->delivery_date = $request->get("delivery_date");
        $order_detail->address = $request->get("address");
        $order_detail->notes = $request->get("notes");
        $order_detail->save();

        $order->detail;
        $order->products;


        if (!$order) {

            return new JsonResponse([
                "error" => true,
                'message' => 'order not created!',
            ]);

        }

        // All good so get product
        return new JsonResponse([
            "error" => false,
            'message' => 'order is completed!',
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

        // -- define required parameters
        $rules = [
            'delivery_is_now' => 'required',
            'delivery_date' => 'required',
            'address' => 'required',
        ];

        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->all()
            ]);
        }


        // All good so update order and order detail
        $order = Order::find($request->get('order_id'));
        $order->status = 0;
        $order->save();

        $order_detail = $order->detail;
        $order_detail->delivery_is_now = $request->get("delivery_is_now");
        $order_detail->delivery_date = $request->get("delivery_date");
        $order_detail->address = $request->get("address");
        $order_detail->address = $request->get("notes");
        $order_detail->save();

        return new JsonResponse([
            "error" => false,
            'message' => "Your order is completed!",
            "category" => $order_detail
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