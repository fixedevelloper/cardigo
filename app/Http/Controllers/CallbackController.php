<?php


namespace App\Http\Controllers;




use App\Helpers\Helper;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function success_flutterwave(Request $request)
    {
        logger($request->get("status"));
        $transaction=Transaction::query()->firstWhere(['order_key'=>$request->get("tx_ref")]);
        if ($request->get("status")=="cancelled"){
            $transaction->status=Helper::REJECTED;
        }else{
            $transaction->status=Helper::COMPLETE;
            $user=$transaction->customer;
            $user->solde+=$transaction->amount;
        }
        $transaction->save();
        return view('redirect_flutterwave', [
            "status"=>$request->get("status")
        ]);
    }
    public function success_paydunya(Request $request)
    {
        $token=$request->get('token');
        return view('success_paydunya', [

        ]);
    }
    public function echec_paydunya(Request $request)
    {
        return view('echec_paydunya', [

        ]);
    }
}
