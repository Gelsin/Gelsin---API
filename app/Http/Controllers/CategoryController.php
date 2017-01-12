<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 11/01/2017
 * Time: 01:40
 */

namespace App\Http\Controllers;


use App\Gelsin\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CategoryController extends Controller
{


    public function __construct()
    {

    }


    public function index(Request $request)
    {

        $categories = Category::where('parent_id', '=', 0)->get();

        foreach ($categories as $category) {

            $subcategories = $category->childs;

            foreach ($subcategories as $subcategory) {

                $subcategory->childs;
            }

            $allCategories[] = $category;
        }

        return new JsonResponse([
            "error" => false,
            "message" => "success",
            'categoryTree' => $categories,
        ]);
    }

    /**
     * Create  new category.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {



        // All good so create new category
        $category = Category::create($request->all());

        return new JsonResponse([
            "error" => false,
            'message' => 'success!',
            "category" => $category
        ]);

    }


    /**
     * Update  new category.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {


        // All good so update category
        $category = Category::find($request->get('category_id'));
        if ($request->get("name")) {

            $category->name = $request->get("name");
            $message = "Category name updated";
        }
        if ($request->get("parent_id")) {

            $category->parent_id = $request->get("parent_id");
            $message = "Parent id updated";
        }
        $category->save();

        return new JsonResponse([
            "error" => false,
            'message' => $message,
            "category" => $category
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
        $category = Category::find($request->get('category_id'));
        $category->products()->delete();
        $category->delete();


        return new JsonResponse([
            "error" => false,
            'message' => $category->name . " is soft deleted",
            "category" => $category
        ]);

    }


}