<?php


namespace App\Http\Controllers\API;


use App\Helpers\Helper;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CardController extends BaseController
{
    function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'customer_id' => 'required|int',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->getException(), 'User login failed.');
        }
        $customer=User::query()->find($request->customer_id);
        if (is_null($customer)){
            return $this->sendError($validator->getException(), 'User login failed.');
        }
        if ($customer->balance<$request->amount){
           // return $this->sendError("Balance error", 'User login failed.');
        }
        $card=new Card();
        $card->name=$request->name;
        $card->user_id=$request->customer_id;
        $card->solde=$request->amount;
        $card->status=Helper::PROCESSING;
        $card->card_type=$request->card_type;
        $card->model=$request->card_model;
        $card->save();
        $customer->balance-=$request->amount;
        $customer->save();

        return $this->sendResponse($card,'Request successfull');
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'ccv' => 'required|string',
            'expired_date' => 'required|string',
            'card_number' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->getException(), 'User login failed.');
        }
        $card=Card::query()->find($id);
        $card->ccv=$request->ccv;
        $card->expired_date=$request->expired_date;
        $card->card_number=$request->card_number;
        $card->save();

        return $this->sendResponse($card,'Request successfull');
    }
    function card_customer(Request $request, $id)
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
            $lists = Card::query()->where(['user_id' => $id])->paginate($limit);
            $data = [];
            foreach ($lists as $list) {
                $cc=$list->card_number;
                $data[] = [
                    "id" => $list->id,
                    "name" => $list->name,
                    "card_number" => $cc,
                    "card_number_hide" => str_pad(substr($cc,-4),strlen($cc),"*",STR_PAD_LEFT),
                    "ccv" => $list->ccv,
                    "status" => $list->status,
                    "expired_date" => date_format($list->created_at, "y/m"),
                    "solde" => $list->solde,
                    "card_type" => $list->card_type,
                    "card_model" => $list->model,
                    "customer_id" => $list->user_id,
                    "customer_name" => is_null($list->customer) ? "" : $list->customer->first_name . ' ' . $list->customer->last_name,
                    "expired_date_format" => date_format($list->created_at, "Y-m-d"),

                ];
            }
            return $this->sendResponse($data, 'Request successfull');
        } catch (\Exception $exception) {
            logger($exception);
            return $this->sendError($exception->getMessage());
        }

    }
    function card_alls(Request $request)
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
            $lists = Card::query()->orderByDesc('created_at')->paginate($limit);
            $data = [];
            foreach ($lists as $list) {
                $data[] = [
                    "id" => $list->id,
                    "name" => $list->amount,
                    "card_number" => $list->card_number,
                    "ccv" => $list->ccv,
                    "status" => $list->status,
                    "expired_date" => $list->expired_date,
                    "solde" => $list->solde,
                    "card_type" => $list->card_type,
                    "card_model" => $list->model,
                    "customer_id" => $list->user_id,
                    "customer_name" => is_null($list->customer) ? "" : $list->customer->first_name . ' ' . $list->customer->last_name,
                    "expired_date_format" => date_format($list->created_at, "Y-m-d"),

                ];
            }
            return $this->sendResponse($data, 'Request successfull');
        } catch (\Exception $exception) {
            logger($exception);
            return $this->sendError($exception->getMessage());
        }

    }

}
