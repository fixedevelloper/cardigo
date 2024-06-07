<?php


namespace App\Services;


use Flutterwave\Controller\PaymentController;
use Flutterwave\Flutterwave;
use Flutterwave\Library\Modal;
use Flutterwave\EventHandlers\MomoEventHandler as PaymentHandler;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class FlutterwareService
{
    public function make_transfert($user_data)
    {
        logger(env("FLUTTER_SECRET_KEY"));
        $url="https://api.flutterwave.com/v3/payments";
        $data = [
            'payment_options' => 'card,banktransfer,mobilemoneyfranco',
            'amount' => strval($user_data['amount']),
            'email' => $user_data['email'],
            'tx_ref' => $user_data['order_key'],
            'currency' => "xaf",
            'redirect_url' => route('redirect_flutterwave'),
            'customer' => [
                'email' => $user_data['email'],
                "phone_number" => $user_data['phone'],
                "name" => $user_data['name'],
            ],
            "customizations" => [
                "title" => "",
                "description" => null,
            ]
        ];
        $response = $this->cURL($url, $data);
        logger(json_encode($response));
        return $response;
    }
    protected function checkStatus($token){

    }
    protected function cURL($url, $json)
    {

        // Create curl resource
        $ch = curl_init($url);

        // Request headers
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Bearer '.env("FLUTTER_SECRET_KEY"),
        );

        // Return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // $output contains the output string
        $output = curl_exec($ch);


        // Close curl resource to free up system resources
        curl_close($ch);
        return json_decode($output);
    }
}
