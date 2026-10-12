<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'user_id', 'status', 'payment_method', 'notes', 'total'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'unit_price')->withTimestamps();
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'completed' => 'success',
            'preparing' => 'info',
            'cancelled' => 'danger',
            default => 'warning',
        };
    }
}
