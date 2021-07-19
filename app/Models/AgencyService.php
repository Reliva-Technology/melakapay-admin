<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyService extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_url',
        'api_url',
        'api_action',
        'parameters',
        'api_username',
        'api_password',
    ];

    public $timestamps = true;

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}