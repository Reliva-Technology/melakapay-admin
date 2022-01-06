<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarianPersendirian extends Model
{
    public $table = 'carian_persendirian';
    
    public $timestamp = true;

    public $fillable = [
        'user_id',
        'bil_paparan',
        'id_hakmilik',
        'id_portal_transaksi',
        'tarikh',
    ];
}
