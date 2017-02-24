<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 18/02/2017
 * Time: 16:51
 */

namespace App\Http\Controllers\Admin;

use App\Gelsin\Models\Category;
use App\Gelsin\Models\Order;
use App\Gelsin\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     * @internal param null $status
     */
    public function index(Request $request)
    {


        $category_id = $request->get("category_id");
        $branch_id = $request->get("branch_id");

        if (!$category_id and !$branch_id) {
            $products = Product::all();
        } else {
            $category = Category::find($category_id);
            $products = $category->products->where('branch_id', $branch_id);
        }

        foreach ($products as $product) {
            $product->branch;
            $product->category;
        }


        return new JsonResponse([
            "error" => false,
            "message" => "Products are listed below.",
            'products' => $products
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