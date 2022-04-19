<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
    public $timestamps = true;

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}
