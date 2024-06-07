<?php


namespace App\Http\Controllers\API;


use App\Helpers\Helper;
use App\Models\Card;
use App\Models\Country;
use App\Models\PaymentLink;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FlutterwareService;
use App\Services\PaydunyaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends BaseController
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
        $this->paydunyaService = $paydunyaService;
        $this->flutterwaveService = $flutterwaveService;
    }


    function create_recharges(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|string',
            'total' => 'required|string',
            'card_id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->getException(), 'User login failed.');
        }
        $customer=Card::query()->find($request->card_id)->customer;

        $transaction = new Transaction();
        $transaction->charge = $request->charge;
        $transaction->amount = $request->amount;
        $transaction->total = $request->total;
        $transaction->card_id = $request->card_id;
        $transaction->type=Helper::TRANSACTION_RECHARGE;
        $transaction->status=Helper::PROCESSING;
        $transaction->save();
        $customer->solde-=$request->amount;
        $customer->save();
        return $this->sendResponse($transaction, 'Request successfull');
    }

    function transactions(Request $request)
    {

        $offset = $request->get("offset");
        if (!isset($offset)) {
            $offset = 0;
        }
        $limit = $request->get('limit');
        if (!isset($limit)) {
            $limit = 20;
        }

        try {
            $lists = Transaction::query()->orderByDesc("created_at")->paginate($limit);
            $data = [];
            foreach ($lists as $list) {
                $data[] = [
                    "id" => $list->id,
                    "amount" => $list->amount,
                    "charge" => $list->charge,
                    "total" => $list->total,
                    "status" => $list->status,
                    "card_id" => is_null($list->card) ? "" : $list->card->id,
                    "card_name" => is_null($list->card) ? "" : $list->card->name,
                    "card_number" => is_null($list->card) ? "" : $list->card->card_number,
                    "card_user" => is_null($list->card) ? "" : $list->card->customer->first_name . ' ' . $list->card->customer->last_name,
                    "transaction_date" => date_format($list->created_at, "Y-m-d"),

                ];
            }
            return $this->sendResponse($data, 'Request successfull');
        } catch (\Exception $exception) {
            logger($exception);
            return $this->sendError($exception->getMessage());
        }

    }
    function transaction_customer(Request $request, $id)
    {

        $offset = $request->get("offset");
        if (!isset($offset)) {
            $offset = 0;
        }
        $limit = $request->get('limit');
        if (!isset($limit)) {
            $limit = 20;
        }

        try {
            $lists = Transaction::query()->leftJoin('cards','cards.id','=','transactions.card_id')
                ->leftJoin("users",'users.id','=',"transactions.user_id")
                ->where(['transactions.user_id' => $id])
                ->select(['*','transactions.status'])
                ->orderByDesc("transactions.created_at")->paginate($limit);
            $data = [];
            foreach ($lists as $list) {
                $data[] = [
                    "id" => $list->id,
                    "amount" => $list->amount,
                    "charge" => $list->charge,
                    "total" => $list->total,
                    "status" => $list->status,
                    "method" => $list->method,
                    "card_id" => $list->card_id,
                    "card_name" => $list->name,
                    "type" => $list->type,
                    "card_number" =>  $list->card_number,
                    "card_user" =>  $list->first_name . ' ' . $list->last_name,
                    "transaction_date" => date_format($list->created_at, "Y-m-d"),

                ];
            }
            return $this->sendResponse($data, 'Request successfull');
        } catch (\Exception $exception) {
            logger($exception);
            return $this->sendError($exception->getMessage());
        }

    }

    function last_transactions(Request $request, $id)
    {
        try {
            $lists = Transaction::query()->where(['sender_id' => $id])->paginate(10);
            $data = [];
            foreach ($lists as $list) {
                $data[] = [
                    "id" => $list->id,
                    "amount" => $list->amount,
                    "charge" => $list->charge,
                    "total" => $list->total,
                    "status" => $list->status,
                    "card_id" => is_null($list->card) ? "" : $list->card->id,
                    "card_name" => is_null($list->card) ? "" : $list->card->name,
                    "card_number" => is_null($list->card) ? "" : $list->card->card_number,
                    "card_user" => is_null($list->card) ? "" : $list->card->customer->first_name . ' ' . $list->card->customer->last_name,
                    "transaction_date" => date_format($list->created_at, "Y-m-d"),

                ];
            }
            return $this->sendResponse($data, 'Request successfull');
        } catch (\Exception $exception) {
            logger($exception);
            return $this->sendError($exception->getMessage());
        }

    }

    function create_deposit(Request $request)
    {
        logger($request->all());
        $validator = Validator::make($request->all(), [
            'amount' => 'required|string',
            'phone' => 'required|string',
            'customer_id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->getMessageBag()->first(), 'deposit error.');
        }
        $customer=User::query()->find($request->customer_id);
        $transaction = new Transaction();
        $transaction->charge = 0.0;
        $transaction->amount = $request->amount;
        $transaction->total = $request->amount;
        $transaction->user_id = $request->customer_id;
        $transaction->order_key = $request->customer_id.Helper::generatenumber24();
        $transaction->phone = $customer->country->code_phone.$request->phone;
        $transaction->type=Helper::TRANSACTION_DEPOSIT;
        $transaction->status=Helper::PROCESSING;
        $transaction->save();
        $link="";
        if ($customer->country->iso=="CM"){
            $transaction->method="flutter_ware";
           $res= $this->flutterwaveService->make_transfert([
                'phone'=>$transaction->phone,
                'amount'=>$transaction->amount,
                'order_key'=>$transaction->order_key,
                'name'=>$customer->first_name,
                'email'=>$customer->email
            ]);
           if ($res->status=="success"){
               $link=$res->data->link;
           }else{
               $link=null;
               logger($res);
           }

        }elseif (in_array($customer->country->iso,Helper::COUNTRY_WEST)){
            $transaction->method="pay_dunya";
            $resp=$this->paydunyaService->make_payment([
                'phone'=>$transaction->phone,
                'amount'=>$transaction->amount,
                'order_key'=>$transaction->order_key,
            ]);
            $link=$resp['url'];
            $transaction->order_key =$resp['token'];
        }
        $transaction->save();
        return $this->sendResponse([
            'transaction'=>$transaction,
            "link"=>$link
        ], 'Request successfull');
    }

}
