<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'description',
        'logo',
        'url',
        'slug',
    ];

    public $timestamps = true;

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}