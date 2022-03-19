<?php

namespace App\Admin\Actions\User;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserAllowedLogin;
use Illuminate\Support\Facades\Notification;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\User;

class AllowLogin extends RowAction
{
    public $name = 'Allow Login to MelakaPay';

    private function ebayarEncrypt($message){
        $encryptionKey = "8017aa25b6c6ba0c56110e3544718361af905f83f151f4f3af8f029cd36ee84d";
         
        $encryptionKeyBytes = pack('H*', $encryptionKey);
        $rawHmac = hash_hmac('sha256', $message, $encryptionKeyBytes, true);
        return bin2hex($rawHmac);
    }

    public function handle(Model $model)
    {
        # check if user already exist in MelakaPay
        $userCheck = \DB::table('users')->where('username',$model['username'])->first();

        if($userCheck){
            return $this->response()->error('This user already have MelakaPay account.');
        }

        # ebayar user details
        $ebayar_user_details = \DB::table('user_details')
            ->where([
                'id_no' => $model['username']
            ])
            ->first();

        if(!$ebayar_user_details) return $this->response()->error('This user does not hava a complete user profile.');

        # update ebayar password
        $ebayar_user = \DB::table('user')
            ->where('username', $model['username'])
            ->update([
                'password' => $this->ebayarEncrypt($model['username']),
                'modified' => Carbon::now()
            ]);

        # duplicate user into Laravel DB
        $user = new User;
        $user->name = $ebayar_user_details->full_name;
        $user->email = $ebayar_user_details->email;
        $user->username = $model['username'];
        $user->device_token = \Str::random(40);
        $user->password = Hash::make($model['username']);
        $user->api_token = \Str::random(60);
        $user->save();

        # Send e-mail notification
        $model['new_password'] = $model['username'];
        Notification::send($user, new UserAllowedLogin($user));

        return $this->response()->success('User has been set to login to MelakaPay successfully.');
    }

    public function dialog()
    {
        $this->confirm('Are you sure? This will set user password to IC and allow user to login to MelakaPay');
    }

}