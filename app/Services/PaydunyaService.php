<?php


namespace App\Services;


use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

class PaydunyaService
{

    private $params;
    private $logger;
    private $base_url;
    /**
     * @var Client
     */
    private $client;


    public function __construct()
    {
        $this->base_url =  'https://app.paydunya.com/api/v1/';
        $this->client = new Client([
            'base_uri' => $this->base_url,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json charset=UTF-8 ',

            ],
        ]);
    }
    public function make_payment($value)
    {
        $currency_code = "XAF";
        try {
            $order = $this->createOrder($value);
           //logger(json_encode($order));
            $options = [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    "PAYDUNYA-MASTER-KEY"=>env("PAYDUNYA_PRINCIPAL"),
                    "PAYDUNYA-PRIVATE-KEY"=>env("PAYDUNYA_SECRET_KEY"),
                    "PAYDUNYA-TOKEN"=>env("PAYDUNYA_TOKEN")
                ],
                'body' => json_encode($order)
            ];
            logger($options);
            $res = $this->client->post("checkout-invoice/create",$options);
            $response= $res->getBody();

            $response_decoded = json_decode($response,true);
            if ($response_decoded['response_code'] && $response_decoded['response_code'] == "00") {

                return [
                    'url'=>$response_decoded['response_text'],
                    'token'=>$response_decoded['token']
                ];

            } else {
                $this->logger->error($response_decoded['response_text']);
                return '';
            }
        } catch (\Exception $exception) {
            logger($exception);
            return '';
        }
    }

    public function createOrder($values)
    {
        $value=$values['amount'];
        $txnid = $values['order_key'];
        $str = "$value|||||||||||$txnid";
        $hash = hash('sha512', $str);

        $paydunya_items[] = [
            "name" => "Recharge wallet CardIgo",
            "quantity" => 1,
            "unit_price" => intval($values['amount']),
            "total_price" => intval($values['amount']),
            "description" => ""
        ];
        $paydunya_args = [
            "invoice" => [
                "items" => $paydunya_items,
                "total_amount" => intval($values['amount']),
                "description" => "Recharge your wallet amount " . intval($values['amount']) . " for CardIgo "
            ], "store" => [
                "name" => "CardIgo",
                "website_url" => "https://agensic.com"
            ], "actions" => [
                "cancel_url" => env('app_url')."echec_paydunya",
                "callback_url" =>  env('app_url')."success_paydunya",
                "return_url" =>  env('app_url')."success_paydunya"
            ], "custom_data" => [
                "order_id" => 1,
                "trans_id" => $txnid,
                "to_user_id"=>2,
                "hash" => $hash
            ]
        ];
        return $paydunya_args;
    }
    public function checkStatus($token)
    {
        try {
            $options = [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    "PAYDUNYA-MASTER-KEY"=>env("PAYDUNYA_PRINCIPAL"),
                    "PAYDUNYA-PRIVATE-KEY"=>env("PAYDUNYA_SECRET_KEY"),
                    "PAYDUNYA-TOKEN"=>env("PAYDUNYA_TOKEN")
                ],
            ];
            $res = $this->client->get("checkout-invoice/confirm/".$token,$options);
            $response= $res->getBody();

            $response_decoded = json_decode($response,true);
            if ($response_decoded['response_code'] && $response_decoded['response_code'] == "00") {

                return [
                    'status'=>$response_decoded['status'],
                    'reason'=>$response_decoded['fail_reason']
                ];

            } else {
                logger($response_decoded);
                return '';
            }
        } catch (\Exception $exception) {
            logger($exception);
            return '';
        }
    }
}
