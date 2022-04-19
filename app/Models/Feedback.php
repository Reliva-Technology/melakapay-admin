<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    public $table = 'feedback';

    public $timestamps = true;
    
    public $fillable = [
        'agency_id',
        'title',
        'message',
        'user_id',
        'status'
    ];

    protected $casts = [
        'id' => 'integer',
        'agency_id' => 'string',
        'title' => 'string',
        'message' => 'string',
        'user_id' => 'integer',
        'status' => 'string'
    ];

    public static $rules = [
        
    ];

    public function contact()
    {
        return $this->hasOne(\App\Models\Contact::class, 'agency_id');
    }

    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}
