<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 31/01/2017
 * Time: 01:58
 */

namespace App\Http\Controllers;


use App\Gelsin\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class AddressController extends Controller
{

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }


    public function index(Request $request)
    {

        $user = $this->jwt->parseToken()->authenticate();
        $addresses = $user->addresses;

        foreach ($addresses as $address) {
            $address->branchAddress;
        }

        return new JsonResponse([
            "error" => false,
            "message" => "success",
            'address' => $addresses,
        ]);
    }


    /**
     * Create  new address.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {


        // -- First  Validate
        $this->validate($request, [
            'user_id' => 'required',
            'branch_address_id' => 'required',
            'address_line' => 'required',
        ]);

        // All good so create new branch address
        $address = Address::create($request->all());

        return new JsonResponse([
            "error" => false,
            'message' => 'success!',
            "address" => $address
        ]);

    }
    /**
     * Update  Address.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {


        // All good so update branch
        $address = Address::find($request->get('address_id'));
        if ($request->get("address_line")) {

            $address->address_line = $request->get("address_line");
            $message = "Address Line updated";
        }
        if ($request->get("branch_address_id")) {

            $address->branch_address_id = $request->get("branch_address_id");
            $message = "Branch address id updated";
        }

        $address->save();

        return new JsonResponse([
            "error" => false,
            'message' => $message,
            "category" => $address
        ]);

    }


    /**
     * Delete   category.
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {


        // All good so delete address
        $address = Address::find($request->get('address_id'));
        $address->delete();

        return new JsonResponse([
            "error" => false,
            'message' => "Address is deleted",
            "address" => $address
        ]);

    }

}
