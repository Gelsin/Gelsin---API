<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 11/01/2017
 * Time: 01:40
 */

namespace App\Http\Controllers;


use App\Gelsin\Models\Category;
use App\Gelsin\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{


    public function __construct()
    {

    }


    /**
     * List products according to selected category.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // -- define required parameters
        $rules = [
            'user_id' => 'required',
        ];

        // -- customize error messages
        $messages = [
            'user_id.required' => 'User id is required!',
        ];
        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->all()
            ]);
        }

//        // All good so get orders
//        return new JsonResponse([
//            "error" => false,
//            "message" => "success",
//            'user' => "",
//        ]);
    }

    /**
     * Create  new order.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {

        // All good so create new order
        $product = Product::create($request->all());
        if (!$product) {

            return new JsonResponse([
                "error" => true,
                'message' => 'order not created!',
            ]);

        }

        // All good so get product

        return new JsonResponse([
            "error" => false,
            'message' => 'success!',
            "category" => $product
        ]);

    }

    /**
     * Show product.
     * @param $product_id
     * @return JsonResponse
     */
    public function show($product_id)
    {

        // All good so get product
        $product = Product::find($product_id);


        if (!$product) {
            return new JsonResponse([
                "error" => true,
                'message' => 'not found!',
            ]);
        }

        return new JsonResponse([
            "error" => false,
            'message' => 'success!',
            "product" => $product
        ]);

    }


    /**
     * Update category.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {

        // All good so update product
        $product = Product::find($request->get('product_id'));
        if ($request->get("name")) {

            $product->name = $request->get("name");
            $message = "Category name updated";
        }
        if ($request->get("category_id")) {

            $product->category_id = $request->get("category_id");
            $message = "Category id updated";
        }
        if ($request->get("quantity")) {

            $product->quantity = $request->get("quantity");
            $message = "Quantity updated";
        }
        if ($request->get("price")) {

            $product->price = $request->get("price");
            $message = "Price updated";
        }
        if ($request->get("branch_id")) {

            $product->branch_id = $request->get("branch_id");
            $message = "Branch id updated";
        }

        $product->save();

        return new JsonResponse([
            "error" => false,
            'message' => $message,
            "category" => $product
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
        $product = Product::find($request->get('product_id'));
        $product->delete();


        return new JsonResponse([
            "error" => false,
            'message' => $product->name . " is soft deleted",
            "category" => $product
        ]);

    }


}