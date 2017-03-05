<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 11/01/2017
 * Time: 01:40
 */

namespace App\Http\Controllers;


use App\Gelsin\Models\ProductBrand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BrandController extends Controller
{


    public function index()
    {

        $brands = ProductBrand::all();

        foreach ($brands as $brand) {
            $brand->category;
        }

        return new JsonResponse([
            "error" => false,
            "message" => "success",
            'brands' => $brands,
        ]);
    }

    /**
     * show brand with its products
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {

        $brand_id = $request->get('brand_id');
        $brand = ProductBrand::find($brand_id);
        $brand->products;

        if (!$brand) {
            return new JsonResponse([
                "error" => true,
                "message" => "No related brand found! ",
            ]);
        }


        return new JsonResponse([
            "error" => false,
            "message" => "Brand is displayed below!",
            'brand' => $brand,
        ]);

    }

    /**
     * Create  new category.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {


        // -- define required parameters
        $rules = [
            'name' => 'required',
            'category_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->all()
            ]);
        }


        $name = $request->get('name');
        $cat_id = $request->get('category_id');

        // All good so create new brand for category
        $brand = new ProductBrand();
        $brand->name = $name;
        $brand->category_id = $cat_id;
        $brand->save();


        return new JsonResponse([
            "error" => false,
            'message' => 'brand is created!',
            "brand" => $brand
        ]);

    }


    /**
     * Update  new brand.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {


        // All good so update brand
        $brand = ProductBrand::find($request->get('brand_id'));
        if ($request->get("name")) {

            $brand->name = $request->get("name");
            $message = "Brand name updated";
        }

        if ($request->get("category_id")) {

            $cat_id = $request->get("category_id");
            $brand->category_id = $cat_id;
            $message = "Brand Category updated";
        }

        $brand->save();

        return new JsonResponse([
            "error" => false,
            'message' => $message,
            "brand" => $brand
        ]);

    }


    /**
     * Delete   category.
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {


        // All good so delete category and its relations
        $brand = ProductBrand::find($request->get('brand_id'));
        $brand->delete();

        if (!$brand) {
            return new JsonResponse([
                "error" => true,
                'message' => "There is no such brand for deleting.",
            ]);
        }


        return new JsonResponse([
            "error" => false,
            'message' => $brand->name . " is deleted",
            "brand" => $brand
        ]);

    }


}