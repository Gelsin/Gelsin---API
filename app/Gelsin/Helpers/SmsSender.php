<?php

/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 04/03/2017
 * Time: 18:06
 */
namespace App\Gelsin\Helpers;

use App\Gelsin\Models\Courier;
use App\Gelsin\Models\Customer;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class SmsSender
{
    /**
     * @param Customer $customer
     * @param $activationCode
     * @return \Psr\Http\Message\StreamInterface
     */
    public function activation(Customer $customer, $activationCode)
    {
        $gsm = $customer->contact;
        $text = "Sizin aktivasiya kodunuz " . $activationCode;

        return $this->sendTo($gsm, $text);
    }

    /**
     * @param Courier $courier
     * @param $text
     * @return \Psr\Http\Message\StreamInterface
     */
    public function smsToCourier(Courier $courier, $text)
    {
        $gsm = $courier->contact;

        return $this->sendTo($gsm, $text);
    }

    /**
     * @param Customer $customer
     * @param $text
     * @return \Psr\Http\Message\StreamInterface
     */
    public function smsToCustomer(Customer $customer, $text)
    {
        $gsm = $customer->contact;

        return $this->sendTo($gsm, $text);
    }

    /**
     * @param $gsm
     * @param $text
     * @return \Psr\Http\Message\StreamInterface
     */
    private function sendTo($gsm, $text)
    {
        $defaults = (object)Config::get('sms.defaults');

        $client = new Client();
        $request = $client->get("http://api.msm.az/sendsms", [
            'query' => [
                'user' => $defaults->user,
                'password' => $defaults->password,
                'from' => $defaults->from,
                'gsm' => $gsm,
                'text' => $text,
            ]
        ]);

        $response = $request->getBody();
        return $response;
    }

}