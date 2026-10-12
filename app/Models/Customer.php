<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['user_id', 'name', 'phone', 'email'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function loyaltyCard()
    {
        return $this->hasOne(LoyaltyCard::class);
    }

    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

   
    public function points(): int
    {
        return (int) floor($this->orders()->where('status', 'completed')->sum('total'));
    }
}
