<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheduler extends Model
{
    use HasFactory;

    public $timestamps = true;

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
