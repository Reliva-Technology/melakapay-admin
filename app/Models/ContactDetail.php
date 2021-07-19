<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactDetail extends Model
{
    public $table = 'contact_details';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'contact_id',
        'name',
        'email',
        'telephone'
    ];

    protected $casts = [
        'id' => 'integer',
        'contact_id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'telephone' => 'string'
    ];

    public static $rules = [
        'contact_id' => 'required|integer',
        'name' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'telephone' => 'required|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

}
