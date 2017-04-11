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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{


    public function __construct()
    {

    }


    public function index(Request $request)
    {

        $categories = Category::where('parent_id', '=', 0)->get();

        foreach ($categories as $category) {
            $category->childs;
            $category->brands;

        }

        return new JsonResponse([
            "error" => false,
            "message" => "success",
            'categoryTree' => $categories,
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function showParents(Request $request)
    {

        $categories = Category::where('parent_id', '=', 0)->get();

        return new JsonResponse([
            "error" => false,
            "message" => "success",
            'categoryTree' => $categories,
        ]);
    }

    /**
     * @param $cat_id
     * @return JsonResponse
     */
    public function showBrands($cat_id)
    {

        $category = Category::find($cat_id);
        if (!$category) {
            return new JsonResponse([
                "error" => true,
                "message" => "There is no such category",
            ]);
        }

        return new JsonResponse([
            "error" => false,
            "message" => $category->name . "'s brands are listed below.",
            'brands' => $category->brands,
        ]);
    }

    /**
     * Show  category and its childs.
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {


        if (!$id) {

            return new JsonResponse([
                "error" => true,
                "message" => "category id not provided"
            ]);

        }

        // Get category
        $category = Category::find($id);
        $category->brands;
        if (!$category) {

            return new JsonResponse([
                "error" => false,
                'message' => 'No such Category!',
            ]);

        }

        $category->childs;
        if ($category->childs->count() < 1) {

            return new JsonResponse([
                "error" => false,
                'message' => 'This category has no childs!',
                'category' => $category,
            ]);

        }


        // All good so get category with childs
        return new JsonResponse([
            "error" => false,
            'message' => 'These categories are child category of ' . $category->name,
            "category" => $category
        ]);

    }

    /**
     * Show category image.
     * @param $category_id
     * @return JsonResponse
     */
    public function showImage($category_id)
    {
        // All good so get product
        $category = Category::find($category_id);
        if (!$category->cover) {

            return new JsonResponse([
                "error" => true,
                'message' => 'category has no cover',
            ]);

        }


        $path = $this->public_path('images/uploads/categories/' . $category->cover);
        if (!file_exists($path)) {

            return new JsonResponse([
                "error" => true,
                'message' => 'no such file exists',
            ]);

        }
        $image = response()->download($path, $category->cover);
        return $image;
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
            'parent_id' => 'required',
            'cover' => 'required|image|mimes:jpeg,jpg,png|dimensions:width=500,height=500',
        ];

        // -- customize error messages
        $messages = [
            'name.required' => 'User id is required!',
            'parent_id.required' => 'Parent id is required!',
            'cover.dimensions' => "image dimensions should be 500 x 500 (px)",
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
        $file->move("public/images/uploads/categories/", $fileName);

        // All good so create new category
        $category = new Category();
        $category->name = $request->get('name');
        $category->parent_id = $request->get('parent_id');
        $category->cover = $fileName;
        $category->save();

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
        if ($request->file("cover")) {

            // first delete old image
            $imagePath = 'public/images/uploads/categories/' . $category->cover;
            if (File::exists($imagePath)) {
                File::Delete($imagePath);
            }

            // Get Image File
            $file = $request->file('cover');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            // Move Image to the related folder
            $file->move("public/images/uploads/categories/", $fileName);
            $category->cover = $fileName;

            $message = "Cover updated";
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
        // Delete image
        File::Delete('public/images/uploads/categories/' . $category->cover);
        $category->products()->delete();
        $category->delete();


        return new JsonResponse([
            "error" => false,
            'message' => $category->name . " is soft deleted",
            "category" => $category
        ]);

    }

    /**
     * @param null $path
     * @return string
     */
    function public_path($path = null)
    {

        return rtrim(app()->basePath('public/' . $path), '/');
    }


}