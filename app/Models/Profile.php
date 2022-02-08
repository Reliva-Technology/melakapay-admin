<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public $table = 'user_details';

    public $fillable = [
        'user_id',
        'id_type',
        'id_no',
        'address',
        'address2',
        'postcode',
        'city',
        'state'
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'id_type' => 'string',
        'id_no' => 'string',
        'address' => 'string',
        'city' => 'string',
        'state' => 'string'
    ];
}
