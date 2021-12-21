<?php

namespace App\Admin\Actions\User;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserPasswordReset;
use Illuminate\Support\Facades\Notification;
use GuzzleHttp\Client;
use Carbon\Carbon;

class ResetPassword extends RowAction
{
    public $name = 'Reset Password';

    private function ebayarEncrypt($message){
        $encryptionKey = "8017aa25b6c6ba0c56110e3544718361af905f83f151f4f3af8f029cd36ee84d";
         
        $encryptionKeyBytes = pack('H*', $encryptionKey);
        $rawHmac = hash_hmac('sha256', $message, $encryptionKeyBytes, true);
        return bin2hex($rawHmac);
    }

    public function handle(Model $model)
    {
        # update local password
        $password = \Str::random(12);
        $model->password = \Hash::make($password);
        $model->save();

        # update ebayar password
        $ebayar_user = \DB::table('user')
            ->where('username', $model['username'])
            ->update([
                'password' => $this->ebayarEncrypt($password),
                'modified' => Carbon::now()
            ]);

        # Send e-mail notification
        $model['new_password'] = $password;
        Notification::send($model, new UserPasswordReset($model));

        # send SMS notification
        $client = new Client();
        $client->post('POST', env('APP_URL').'/credential/reset-password',[
            'form_params' => [
                'email' => $model['email']
            ]
        ]);

        return $this->response()->success('Password reset successfully.');
    }

}