<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdatePayment extends Model
{
    use HasFactory;

    public $timestamps = true;

    public $fillable = [
        'eps_id',
        'transaction_id',
        'eps_status',
        'response'
    ];
}
