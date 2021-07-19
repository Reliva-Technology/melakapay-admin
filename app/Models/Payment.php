<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $table = 'payments';
    
    public $fillable = [
        'agency_id',
        'payment_mode',
        'bank_code',
        'be_message',
        'amount',
        'eps_transaction_id',
        'payment_status',
        'payee_name',
        'payee_email'
    ];

    protected $casts = [
        'id' => 'integer',
        'agency_id' => 'integer',
        'payment_mode' => 'string',
        'bank_code' => 'string',
        'be_message' => 'string',
        'amount' => 'double',
        'eps_transaction_id' => 'string',
        'payment_status' => 'string',
        'payee_name' => 'string',
        'payee_email' => 'string'
    ];

    public static $rules = [
        'agency_id' => 'required',
        'payment_mode' => 'required',
        'amount' => 'required',
        'payment_status' => 'required',
        'payee_email' => 'payee_phone_number string'
    ];
    
}