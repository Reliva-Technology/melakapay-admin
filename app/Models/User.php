<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function favourite()
    {
        return $this->hasMany(Favourite::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'id_no', 'username');
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class)->app();
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
