<?php

namespace App\Admin\Actions\User;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserPasswordReset;
use Illuminate\Support\Facades\Notification;

class ResetPassword extends RowAction
{
    public $name = 'Reset Password';

    public function handle(Model $model)
    {
        $password = \Str::random(8);
        $model->password = \Hash::make($password);
        $model->save();

        // Send e-mail notification
        $model['new_password'] = $password;

        Notification::send($model, new UserPasswordReset($model));

        return $this->response()->success('Password reset successfully.');
    }

}