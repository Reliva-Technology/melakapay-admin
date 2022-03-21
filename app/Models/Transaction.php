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

    /**
     * Scope a query to only include MelakaPay transaction.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApp($query)
    {
        return $query->where('agency','LIKE','%-app');
    }

    public function scopeEbayar($query)
    {
        return $query->where('agency','NOT LIKE','%-app');
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'merchant_transaction_id');
    }
}