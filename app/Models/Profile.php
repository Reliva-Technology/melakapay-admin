<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public $table = 'user_details';

    const CREATED_AT = 'modified';
    const UPDATED_AT = 'modified';

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
    
    public function ebayar()
    {
        return $this->hasOne(Ebayar::class, 'user_id', 'id');
    }
}
