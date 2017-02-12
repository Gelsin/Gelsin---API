<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 11/01/2017
 * Time: 01:40
 */

namespace App\Http\Controllers;


use App\Gelsin\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class BranchController extends Controller
{


    public function __construct()
    {

    }


    public function index(Request $request)
    {

        $branches = Branch::all();


        return new JsonResponse([
            "error" => false,
            "message" => "success",
            'branches' => $branches,
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
            'address_line' => 'required',

        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return new JsonResponse([
                "error" => true,
                "message" => $validator->errors()->all()
            ]);
        }


        // All good so create new branch
        $branch = Branch::create($request->all());

        return new JsonResponse([
            "error" => false,
            'message' => 'success!',
            "category" => $branch
        ]);

    }


    /**
     * Update  new category.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {


        // All good so update branch
        $branch = Branch::find($request->get('branch_id'));
        if ($request->get("name")) {

            $branch->name = $request->get("name");
            $message = "Branch name updated";
        }
        if ($request->get("address_line")) {

            $branch->address_line = $request->get("address_line");
            $message = "Branch address_line updated";
        }
        if ($request->get("latitude")) {

            $branch->latitude = $request->get("latitude");
            $message = "Branch latitude updated";
        }
        if ($request->get("longitude")) {

            $branch->longitude = $request->get("longitude");
            $message = "Branch longitude updated";
        }
        $branch->save();

        return new JsonResponse([
            "error" => false,
            'message' => $message,
            "category" => $branch
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
        $branch = Branch::find($request->get('branch_id'));
        $branch->delete();


        return new JsonResponse([
            "error" => false,
            'message' => $branch->name . " is soft deleted",
            "category" => $branch
        ]);

    }


}