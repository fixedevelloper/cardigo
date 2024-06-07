<?php


namespace App\Helpers;


use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Helper
{

    const PENDING="PENDING";
    const PROCESSING="PROCESSING";
    const CONFIRMED="CONFIRMED";
    const COMPLETE="COMPLETE";
    const REJECTED="REJECTED";
    const TRANSACTION_DEPOSIT="DEPOSIT";
    const TRANSACTION_RECHARGE="RECHARGE";
    const TRANSACTION_WITHDRAW="WITHDRAW";
    const per_page=10;
    const COUNTRY_WEST=["CI",'BJ',"ML","BF","TG","SN"];
    public static function str_slug($text){
        return strtolower(str_ireplace(" ","_",$text)) ;
    }
    public static function upload(string $dir, string $format, $image = null)
    {
        if ($image != null) {
            $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->put($dir . $imageName, file_get_contents($image));
        } else {
            $imageName = 'def.png';
        }

        return $imageName;
    }
   public static function base64Tofile($dir,$data){
       $image_base64 = base64_decode($data['image']);
       if ($data['image_type'] === 'image/png') {
          $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . '.png';
       }elseif ($data['image_type'] === 'image/jpeg'){
            $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . '.jpeg';
       } else {
           $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . '.jpg';
       }
       if (!Storage::disk('public')->exists($dir)) {
           Storage::disk('public')->makeDirectory($dir);
       }
       Storage::disk('public')->put($dir . $imageName, $image_base64);
       return $dir.'/'.$imageName;

   }
    public static function generatenumber()
    {
        $tabs=['1','2','3','4','5','6','7','8','9','0'];
        $strong=date("ymds");
        for ($i = 1; $i <= 15; $i++) {
            $strong .= $tabs[rand(0, count($tabs) - 1)];
        }
        return $strong;
    }
    public static function generatenumber24()
    {
        $tabs=['1','2','3','4','5','6','7','8','9','0','a','b','c','d',
            'e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','z'];
        $strong=date("ymds");
        for ($i = 1; $i <= 60; $i++) {
            $strong .= $tabs[rand(0, count($tabs) - 1)];
        }
        return $strong;
    }
    public static function send_creation_account($data)
    {
        $data_ = array('email' => $data['email'],'first_name' => $data['first_name'],'activate_key'=>$data['activate_key'],'slot'=>"");
        Mail::send(['html' => 'mails.verified_mail'], $data_, function ($message)
        use ($data) {
            $message->to($data['email'], $data['first_name'])->subject("Confirm email");
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });

    }
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $err_keeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $err_keeper;
    }

    public static function response_formatter($constant, $content = null, $errors = []): array
    {
        $constant = (array)$constant;
        $constant['content'] = $content;
        $constant['errors'] = $errors;
        return $constant;
    }
    public static function getAge($date){
        $le=Carbon::createFromFormat("Y-m-d",$date);
        return Carbon::now()->diffInYears($le);
    }
}
