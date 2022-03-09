<?php

namespace App\Admin\Actions\User;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ImpersonateUser extends RowAction
{
    public $name = 'Login as User';

    public function href()
    {
        return env('MELAKAPAY_URL').'impersonate/'.$this->getKey();
    }

}