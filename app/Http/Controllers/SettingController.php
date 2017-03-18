<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 16/03/2017
 * Time: 01:08
 */

namespace App\Http\Controllers;


use App\Gelsin\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    public function index($key)
    {


        $setting = Setting::where('key', $key)->first();
        if (!$setting) {
            return new JsonResponse([
                "error" => true,
                "message" => "key does not exist",
            ]);
        }

        return new JsonResponse([
            "error" => false,
            "message" => $key . " value is displayed below.",
            "value" => $setting->value,
        ]);
    }


}