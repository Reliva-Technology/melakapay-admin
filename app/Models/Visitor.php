<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    public $timestamps = FALSE;

    protected $fillable = [
        'date',
        'ip'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];
}
