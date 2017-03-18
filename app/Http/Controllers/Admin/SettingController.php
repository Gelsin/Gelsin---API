<?php
/**
 * This Controller is for General App settings
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 16/03/2017
 * Time: 00:07
 */

namespace App\Http\Controllers\Admin;


use App\Gelsin\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class SettingController extends Controller
{

    public function index()
    {
        $settings = Setting::all();

        return new JsonResponse([
            "error" => false,
            "message" => "success",
            'settings' => $settings,
        ]);
    }

    /**
     * Update  new category.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {

        $id = $request->get('id');
        $value = $request->get('value');
        // All good so update setting
        $setting = Setting::find($id);
        $setting->value = $value;
        $setting->save();

        return new JsonResponse([
            "error" => false,
            'message' => "setting is updated",
            "setting" => $setting
        ]);

    }

}