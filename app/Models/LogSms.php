<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSms extends Model
{
    public $timestamp = true;

    public $fillable = [
        'user_id',
        'phone_number',
        'sessionid',
        'messageid'
    ];

    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }
}
