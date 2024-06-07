<?php


namespace App\Service\Flutterwave;


use App\Entity\Transaction;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FlutterwaveService
{
    const BASE_URL = "https://api.flutterwave.com/v3/";
    private $params;
    private $looger;
    public $mode="";
    /**
     * @var Client
     */
    private $client;

    /**
     * FlutterwaveService constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->looger = $logger;
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json charset=UTF-8 ',
            ],
        ]);
        if ($this->params->get('mode')=="sandbox"){
            $this->mode="_PMCKDU_1";
        }

    }
    function makeTransaction(Transaction $transaction,$callback){
        $endpoint ="transfers";
        $postdata=[
            'account_bank'=>$transaction->getBeneficiare()->getBankname(),
            'account_number'=>$transaction->getBeneficiare()->getBankaccountnumber(),
            'amount'=>$transaction->getMontanttotal(),
            'narration'=>$transaction->getRaisontransaction(),
            'currency'=>$transaction->getCountry()->getMonaire(),
            'reference'=>$transaction->getNumeroidentifiant().$this->mode,
            'callback_url'=>$callback,
            'debit_currency'=>$transaction->getCountry()->getMonaire(),
            'beneficiary_name'=>$transaction->getBeneficiare()->getFirstname().' '.$transaction->getBeneficiare()->getLastname(),
            'meta' => [
                'mobile_number' => $transaction->getBeneficiare()->getPhone(),
                'email' => $transaction->getCustomer()->getEmail(),
                'beneficiary_country'=>$transaction->getCountry()->getCode(),
                'sender'=>$transaction->getCustomer()->getFirstname().' '.$transaction->getCustomer()->getLastname(),
                'sender_country'=>$transaction->getCustomer()->getCountry()->getCode(),
                'sender_mobile_number'=>$transaction->getCustomer()->getPhone()
            ],
        ];
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->params->get('flu_private'),
            ],
            'body' => json_encode($postdata)
        ];
        $response = $this->client->post($endpoint,$options);
        $body = $response->getBody();
        return json_decode($body,true);
    }
    function getTransaction($id){
        $endpoint ="transfers/".$id;
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->params->get('flu_private'),
            ]
        ];
        $response = $this->client->get($endpoint,$options);
        $body = $response->getBody();
        return json_decode($body,true);
    }
    function getRates(){
        $endpoint ="transfers/rates";
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->params->get('flu_private'),
            ]
        ];
        $response = $this->client->get($endpoint,$options);
        $body = $response->getBody();
        return json_decode($body,true);
    }
}
