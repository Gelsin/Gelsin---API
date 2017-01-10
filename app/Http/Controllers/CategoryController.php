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
                    'message' => 'user_not_found',
                ]);
            }

        } catch (Exception $e) {

            return response()->json(['token_expired']);

        } catch (TokenInvalidException $e) {

            return new JsonResponse([
                'message' => 'token_invalid',
            ]);

        } catch (JWTException $e) {

            return new JsonResponse([
                'message' => 'token_absent',
            ]);

        }

        // All good so create new category
        $category = Category::create($request->all());

        return new JsonResponse([
            'message' => 'success!',
            "category" => $category
        ]);

    }


}