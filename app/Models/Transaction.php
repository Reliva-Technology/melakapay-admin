<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    public $timestamps = true;

    public $table = 'transaction_details';

    public function scopeApp($query)
    {
        return $query->where('agency','LIKE','%-app%');
    }

    public function scopeStom($query)
    {
        return $query->where('agency','=','stom')->where('id', '>', '589476'); // staty of live melakapay transaction ID
    }

    public function scopeToday($query)
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();
        return $query->whereBetween('modified',[$start,$end]);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function ebayar()
    {
        return $this->belongsTo(AgencyEbayar::class, 'agency', 'agency');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'merchant_transaction_id');
    }
}