<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ebayar
{
    use HasFactory, Notifiable;

    public $table = 'user';

    protected $fillable = [
        'username',
        'password',
    ];

    protected $hidden = [
        'password'
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }
}
