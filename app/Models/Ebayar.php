<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ebayar extends Model
{
    use HasFactory;

    public $table = 'user';

    const CREATED_AT = 'modified';
    const UPDATED_AT = 'modified';

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

    public function melakapay()
    {
        return $this->hasOne(User::class, 'username', 'username');
    }
}
