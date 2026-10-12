<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
    protected $fillable = ['customer_id', 'card_number', 'tier'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
