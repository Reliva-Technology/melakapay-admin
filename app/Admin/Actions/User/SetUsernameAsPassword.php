<?php

namespace App\Admin\Actions\User;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UsernamePasswordReset;
use Illuminate\Support\Facades\Notification;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\User;

class SetUsernameAsPassword extends RowAction
{
    public $name = 'Set IC As Password';

    private function ebayarEncrypt($message){
        $encryptionKey = "8017aa25b6c6ba0c56110e3544718361af905f83f151f4f3af8f029cd36ee84d";
        $encryptionKeyBytes = pack('H*', $encryptionKey);
        $rawHmac = hash_hmac('sha256', $message, $encryptionKeyBytes, true);
        return bin2hex($rawHmac);
    }

    public function handle(Model $model)
    {
        # update local password
        $password = $model['username'];
        $model->password = \Hash::make($password); # set default password as IC
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
        Notification::send($model, new UsernamePasswordReset($model));

        # get user
        $user = User::where('username', $model['username'])->first();

        # user details
        $user_details = \DB::table('user_details')
            ->where([
                'id_no' => $model['username']
            ])
            ->first();

        if(!$user_details) return $this->response()->error('This user does not hava a complete user profile.');

        # send SMS only if no phone exist
        if(isset($user_details->phone_no)){
            $phone_no = $user_details->phone_no;
        } else {
            $phone_no = NULL;
        }

        if($phone_no != NULL){

            $data = [
                'user_id' => $user->id,
                'phone_number' => $phone_no,
                'password' => $model['username'],
                'timestamp' => Carbon::now()
            ];

            try{
                send_sms($data);
            } catch(ValidatorException $e){
                Log::error('Failed to send SMS. Error:'.$e);
            }
        }

        return $this->response()->success('Temporary password set successfully.');
    }

    public function dialog()
    {
        $this->confirm('Are you sure?');
    }

}