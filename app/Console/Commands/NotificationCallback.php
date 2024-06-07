<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Models\Transaction;
use App\Services\FlutterwareService;
use App\Services\PaydunyaService;
use Illuminate\Console\Command;

class NotificationCallback extends Command
{
    private  $paydunyaService;
    private  $flutterwaveService;

    /**
     * TransactionController constructor.
     * @param $paydunyaService
     * @param $flutterwaveService
     */
    public function __construct(PaydunyaService $paydunyaService, FlutterwareService $flutterwaveService)
    {
        parent::__construct();
        $this->paydunyaService = $paydunyaService;
        $this->flutterwaveService = $flutterwaveService;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notification-callback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->validateTransactionPaydunya();
    }
    function validateTransactionPaydunya(){
        $transaction_pending=Transaction::query()->where(['status'=>Helper::PROCESSING,
            'type'=>Helper::TRANSACTION_DEPOSIT,'method'=>"pay_dunya"])->orderByDesc("id")->get();
        foreach ($transaction_pending as $transaction){

            if (!is_null($transaction)){
                $user=$transaction->customer;
                $response=  $this->paydunyaService->checkStatus($transaction->order_key);
               logger(json_encode($response));
                if ($response['status'] =='completed'){
                    $transaction->status=Helper::CONFIRMED;
                    $user->solde+=$transaction->amount;
                    $user->save();
                }
                if ($response['status'] =='cancelled'){
                    $transaction->status=Helper::REJECTED;
                }
            }


           $transaction->save();
        }
    }
}
