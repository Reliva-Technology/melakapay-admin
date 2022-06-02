<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agency_id',
        'api_name',
        'search_id',
        'request',
        'response',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}
