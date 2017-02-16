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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
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
            'category_id' => 'required',
            'branch_id' => 'required',
        ];

        // -- customize error messages
        $messages = [
            'category_id.required' => 'Category id is required!',
            'branch_id.required' => 'Branch id is required!',
        ];

        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->all()
            ]);
        }


        $category_id = $request->get("category_id");
        $branch_id = $request->get("branch_id");

        $products = Category::find($category_id)->products->where("branch_id", $branch_id);

        if ($products->count() < 1) {

            return new JsonResponse([
                "error" => true,
                "message" => "No product on this branch!",
            ]);
        }

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

        // -- define required parameters
        $rules = [
            'category_id' => 'required',
            'name' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'branch_id' => 'required',
            'cover' => 'required|image|mimes:jpeg,jpg|dimensions:width=500,height=500',
        ];

        $messages = [
            'cover.dimensions' => "image dimensions should be 500 x 500 (px)"
        ];


        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->all()
            ]);
        }

        // Get Image File
        $file = $request->file('cover');
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        // Move Image to the related folder
        $file->move("images/uploads/products/", $fileName);

        // All good so create new product
        $product = new Product();
        $product->name = $request->get("name");
        $product->category_id = $request->get("category_id");
        $product->branch_id = $request->get("branch_id");
        $product->quantity = $request->get("quantity");
        $product->price = $request->get("price");
        $product->cover = $fileName;
        $product->save();


        return new JsonResponse([
            "error" => false,
            'message' => 'success!',
            "product" => $product
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
     * Show product.
     * @param $product_id
     * @return JsonResponse
     */
    public function showImage($product_id)
    {
        // All good so get product
        $product = Product::find($product_id);

        if (!$product->cover) {

            return new JsonResponse([
                "error" => true,
                'message' => 'product has no cover',
                "product" => $product
            ]);

        }

        $path = $this->public_path('images/uploads/products/' . $product->cover);
        $image = response()->download($path, $product->cover);

        return $image;

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
            'cover' => 'required|image|mimes:jpeg,jpg|dimensions:width=500,height=500',
        ];

        $messages = [

            'cover.dimensions' => "image dimensions should be 500 x 500 (px)"

        ];

        // -- Validate and display error messages
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->all()
            ]);
        }

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
        if ($request->file("cover")) {

            // first delete old image
            File::Delete('images/uploads/products/' . $product->cover);

            // Get Image File
            $file = $request->file('cover');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            // Move Image to the related folder
            $file->move("images/uploads/products/", $fileName);
            $product->cover = $fileName;

            $message = "Cover updated";
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

    /**
     * @param null $path
     * @return string
     */
    function public_path($path = null)
    {

        return rtrim(app()->basePath('' . $path), '/');
    }


}