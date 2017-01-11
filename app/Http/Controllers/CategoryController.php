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
use Mockery\CountValidator\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class CategoryController extends Controller
{

    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {

        $this->jwt = $jwt;
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
            'categoryTree' => $allCategories,
        ]);
    }

    /**
     * Create  new category.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {


        try {

            $user = $this->jwt->parseToken()->authenticate();
            //-- Validate inputs
            $this->validate($request, [
                'name' => 'required',
                'parent_id' => 'required',
            ]);

            if (!$user) {

                return new JsonResponse([
                    "error" => true,
                    'message' => 'user_not_found',
                ]);
            }

        } catch (Exception $e) {

            return response()->json(['token_expired']);

        } catch (TokenInvalidException $e) {

            return new JsonResponse([
                "error" => true,
                'message' => 'token_invalid',
            ]);

        } catch (JWTException $e) {

            return new JsonResponse([
                "error" => true,
                'message' => 'token_absent',
            ]);

        }

        // All good so create new category
        $category = Category::create($request->all());

        return new JsonResponse([
            "error" => false,
            'message' => 'success!',
            "category" => $category
        ]);

    }


    /**
     * Create  new category.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {


        try {

            $user = $this->jwt->parseToken()->authenticate();
            //-- Validate inputs
            $this->validate($request, [
                'category_id' => 'required',
            ]);


            if (!$user) {

                return new JsonResponse([
                    "error" => true,
                    'message' => 'user_not_found',
                ]);
            }

        } catch (Exception $e) {

            return response()->json(['token_expired']);

        } catch (TokenInvalidException $e) {

            return new JsonResponse([
                "error" => true,
                'message' => 'token_invalid',
            ]);

        } catch (JWTException $e) {

            return new JsonResponse([
                "error" => true,
                'message' => 'token_absent',
            ]);

        }

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

        return new JsonResponse([
            "error" => false,
            'message' => $message,
            "category" => $category
        ]);

    }


}