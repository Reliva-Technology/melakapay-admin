<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    public $table = 'contacts';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'agency_id',
        'address',
        'email',
        'telephone'
    ];

    protected $casts = [
        'id' => 'integer',
        'agency_id' => 'integer',
        'address' => 'string',
        'email' => 'string',
        'telephone' => 'string'
    ];

    public static $rules = [
        'agency_id' => 'required|integer',
        'address' => 'required|string',
        'address' => 'required|string|email',
        'telephone' => 'required|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    protected $appends = [
        'contact_details'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function contactDetails()
    {
        return $this->hasMany(ContactDetail::class);
    }

    public function getContactDetailsAttribute()
    {
        return $this->contactDetails()->get(['contact_details.name', 'contact_details.email', 'contact_details.telephone']);
    }
}
