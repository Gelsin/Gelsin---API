<?php

/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 06/02/2017
 * Time: 04:53
 */
namespace App\Http\Controllers\Admin;

use App\Gelsin\Models\Branch;
use App\Gelsin\Models\BranchAdress;
use App\Gelsin\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchAddressController extends Controller
{

    /**
     * @param $branch_id
     * @return JsonResponse
     */
    public function index($branch_id)
    {
        $branch = Branch::find($branch_id);

        $branch->addresses;


        return new JsonResponse([
            "error" => false,
            'message' => 'success',
            'branch' => $branch,
        ]);
    }

}