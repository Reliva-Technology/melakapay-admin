<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'email',
    ];

    public $timestamps = true;

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'agency_id');
    }

    public function contact()
    {
        return $this->hasMany(Contact::class, 'agency_id');
    }

    public function agencyServices()
    {
        return $this->hasMany(AgencyService::class, 'agency_id');
    }
}