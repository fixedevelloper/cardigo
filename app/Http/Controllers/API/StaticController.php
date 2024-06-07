<?php


namespace App\Http\Controllers\API;


use App\Models\Country;
use App\Models\PaymentOperator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaticController extends BaseController
{
    function countries(Request $request)
    {
        $lists=Country::query()->where(['status'=>1])->get();
        $data=[];
        foreach ($lists as $list){
            $data[]=[
                "id"=>$list->id,
                "name"=>$list->name,
                "iso"=>$list->iso,
                "iso3"=>$list->iso3,
                "numcode"=>$list->numcode,
                "phonecode"=>$list->phonecode,
                "flag"=>$list->flag,
                "currency_name"=>is_null($list->currency)?"":$list->currency->name,
                "currency_symbol"=>is_null($list->currency)?"":$list->currency->symbol,
                "exchange"=>is_null($list->currency)?"":$list->currency->exchange,
            ];
        }
        return $this->sendResponse($data,'Request successfull');
    }
    function countriesby_id(Request $request,$id)
    {
        $lists=PaymentOperator::query()->where(['country_id'=>$id])->orderByDesc('id')->get();
        $data=[
            "operators"=>$lists
        ];
        return $this->sendResponse($data,'Request successfull');
    }
}
