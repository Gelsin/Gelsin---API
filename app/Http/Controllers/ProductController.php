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

class ProductController extends Controller
{


    public function __construct()
    {

    }


    public function index(Request $request)
    {

        $category_id = $request->get("category_id");
        $products = Category::find($category_id)->products;


        return new JsonResponse([
            "error" => false,
            "message" => "success",
            'products' => $products,
        ]);
    }

    /**
     * Create  new product.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {

        // All good so create new category
        $product = Product::create($request->all());

        return new JsonResponse([
            "error" => false,
            'message' => 'success!',
            "category" => $product
        ]);

    }


    /**
     * Create  new category.
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