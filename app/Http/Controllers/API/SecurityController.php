<?php


namespace App\Http\Controllers\API;


use App\Helpers\Helper;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SecurityController extends BaseController
{
    function authenticate(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator, 'User login failed.');
        }
        $user=User::query()->firstWhere(['phone'=>$request->phone]);
        //availability check
        if (!isset($user)) {
            return $this->sendError("Phone not register", 'User login failed.');
        }
        if ($user->user_type != User::CUSTOMER_TYPE && $user->user_type != User::DRIVER_TYPE
        ) {
            return $this->sendError("User have not access on this resource", 'User login failed.');
        }
        //status active check
        if (isset($user->is_active) && !$user->is_active) {
            return $this->sendError("Not activate", 'User login failed.');
        }
        if (!Hash::check($request['password'], $user['password'])) {
            return $this->sendError("Not match password", 'User login failed.');
        }
        $user->update(['last_active_at' => now()]);
        $success['token'] = $user->createToken('ApiToken')->plainTextToken;
        $success['name'] = $user->first_name." ".$user->last_name;
        $success['country_id'] = $user->country_id;
        $success['phone'] = $user->phone;
        $success['photo']=$user->photo;
        $success['id'] = $user->id;
        $success['user_type'] = $user->user_type;
        return $this->sendResponse($success, 'User login successfully.');
    }
    function create(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'country_id' => 'required',
            'email' => '',
            'photo' => '',
            'phone' => 'required|min:5|max:20|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->getMessages(), 403);
        }
        $country=Country::query()->find($request->country_id);
        if (is_null($country)){
            return $this->sendError($validator->errors()->getMessages(), 403);
        }
        try {
            $driver = new User();
            $driver->first_name = $request->first_name;
            $driver->phone = $request->phone;
            $driver->last_name = $request->last_name;
            $driver->email = $request->email;
            $driver->country_id = $request->country_id;
            if ($request->has('date_born')) {
                $driver->date_born = $request->date_born;
            }
            if ($request->has('password')) {
                $driver->password = bcrypt($request->get('password'));
            }
            $driver->user_type = User::CUSTOMER_TYPE;
            if ($request->has('photo')) {
                $driver->photo = Helper::base64Tofile("photo/", [
                    'image' => $request->photo,
                    'image_type' => $request->photo_type,
                ]);
            }
            if ($request->has('document_recto')) {
                $driver->document_recto = Helper::base64Tofile("docs/", [
                    'image' => $request->document_recto,
                    'image_type' => $request->document_recto_type,
                ]);
            }
            if ($request->has('document_verso')) {
                $driver->document_verso = Helper::base64Tofile("docs/", [
                    'image' => $request->document_verso,
                    'image_type' => $request->document_verso_type,
                ]);
            }
            if ($request->has('document_id')) {
                $driver->document_id = $request->document_id;
            }
            if ($request->has('document_type')) {
                $driver->document_type = $request->document_type;
            }

            $driver->save();
            $driver->update(['last_active_at' => now()]);
            $success['token'] = $driver->createToken('ApiToken')->plainTextToken;
            $success['name'] = $driver->first_name." ".$driver->last_name;
            $success['country_id'] = $driver->country_id;
            $success['phone'] = $driver->phone;
            $success['photo'] = $driver->photo;
            $success['id'] = $driver->id;
            $success['user_type'] = $driver->user_type;
            return $this->sendResponse($success, 'request successfully.');
        } catch (\Exception $exception) {
            logger($exception->getMessage());
            return $this->sendError($exception->getMessage());
        }
    }
    function getAccount(Request $request,$id){
        $customer=User::query()->find($id);
        return $this->sendResponse([
            'first_name'=>$customer->first_name,
            'last_name'=>$customer->last_name,
            'phone'=>$customer->phone,
            'email'=>$customer->email,
            'date_born'=>$customer->date_born,
            'photo'=>$customer->photo,
            'facebook'=>$customer->facebook,
            'youtube'=>$customer->youtube,
            'balance'=>$customer->balance,
            'phone_verified'=>$customer->phone_verified,
            'email_verified'=>$customer->email_verified,
            'country_id'=>$customer->country_id,
            'flag'=>$customer->country->flag,
            'code_phone'=>$customer->country->phonecode,
            'postal_code'=>$customer->postal_code,
        ], 'request successfully.');
    }
    function searchAccount(Request $request){
        $key_=$request->get('key');
        $key = explode(' ', $key_);
        logger($key);
        $agents = User::query()->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('first_name', 'like', "%{$value}%")
                    ->orWhere('last_name', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%");
            }
        });
        $lists=[];
        foreach ($agents->get() as $customer){
            $lists[]=[
                'id'=>$customer->id,
                'first_name'=>$customer->first_name,
                'last_name'=>$customer->last_name,
                'phone'=>$customer->phone,
                'email'=>$customer->email,
                'date_born'=>$customer->date_born,
                'photo'=>$customer->photo,
                'facebook'=>$customer->facebook,
                'youtube'=>$customer->youtube,
                'balance'=>$customer->balance,
                'phone_verified'=>$customer->phone_verified,
                'email_verified'=>$customer->email_verified,
                'country_id'=>$customer->country_id,
                'country_name'=>$customer->country->name,
                'country_code'=>$customer->country->code,
                'postal_code'=>$customer->postal_code,
            ];
        }
        return $this->sendResponse($lists, 'request successfully.');
    }
    function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => '',
            'photo' => '',
            'phone' => 'required',

        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->getMessages(), 403);
        }
        $account=User::query()->find($id);
        try {
            $account->first_name = $request->first_name;
            $account->phone = $request->phone;
            $account->last_name = $request->last_name;
            $account->email = $request->email;
            if ($request->has('date_born')) {
                $account->date_born = $request->date_born;
            }

            if ($request->has('photo')) {
                $account->photo = Helper::base64Tofile("photo/", [
                    'image' => $request->photo,
                    'image_type' => $request->photo_type,
                ]);
            }
            if ($request->has('document_recto')) {
                $account->document_recto = Helper::base64Tofile("docs/", [
                    'image' => $request->document_recto,
                    'image_type' => $request->document_recto_type,
                ]);
            }
            if ($request->has('document_verso')) {
                $account->document_verso = Helper::base64Tofile("docs/", [
                    'image' => $request->document_verso,
                    'image_type' => $request->document_verso_type,
                ]);
            }
            if ($request->has('document_id')) {
                $account->document_id = $request->document_id;
            }
            if ($request->has('document_type')) {
                $account->document_type = $request->document_type;
            }

            $account->save();
            $account->update(['last_active_at' => now()]);
            $success['token'] = $account->createToken('ApiToken')->plainTextToken;
            $success['name'] = $account->first_name." ".$account->last_name;
            $success['country_id'] = $account->country_id;
            $success['phone'] = $account->phone;
            $success['photo'] = $account->photo;
            $success['id'] = $account->id;
            $success['user_type'] = $account->user_type;
            return $this->sendResponse($success, 'request successfully.');
        } catch (\Exception $exception) {
            logger($exception->getMessage());
            return $this->sendError($exception->getMessage());
        }
    }
    function changepassword(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->getMessages(), 403);
        }
        $account=User::query()->find($id);
        try {
            $account->password = bcrypt($request->get('new_password'));
            $account->save();
            return $this->sendResponse($account, 'request successfully.');
        } catch (\Exception $exception) {
            logger($exception->getMessage());
            return $this->sendError($exception->getMessage());
        }
    }
    function delete_account(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->getMessages(), 403);
        }
        $account=User::query()->find($id);
        try {
            $account->is_delete = true;
            $account->save();
            return $this->sendResponse([], 'request successfully.');
        } catch (\Exception $exception) {
            logger($exception->getMessage());
            return $this->sendError($exception->getMessage());
        }
    }
}
