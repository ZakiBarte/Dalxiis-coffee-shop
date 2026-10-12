<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public const ROLES = ['admin', 'manager', 'employee', 'customer'];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isManager(): bool { return $this->role === 'manager'; }
    public function isEmployee(): bool { return $this->role === 'employee'; }
    public function isCustomer(): bool { return $this->role === 'customer'; }
    public function isStaff(): bool { return in_array($this->role, ['admin', 'manager', 'employee']); }

    // Admin and manager manage products, categories, customers
    public function canManageCatalog(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function roleColor(): string
    {
        return match ($this->role) {
            'admin' => 'danger',
            'manager' => 'primary',
            'employee' => 'info',
            default => 'secondary',
        };
    }

    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    
    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function ensureCustomer(): Customer
    {
        $customer = $this->customer;

        if (! $customer) {
            $customer = Customer::where('email', $this->email)->whereNull('user_id')->first();

            if ($customer) {
                $customer->update(['user_id' => $this->id]);
            } else {
                $customer = Customer::create([
                    'user_id' => $this->id,
                    'name' => $this->name,
                    'email' => $this->email,
                ]);
            }
        }

        if (! $customer->loyaltyCard) {
            LoyaltyCard::create([
                'customer_id' => $customer->id,
                'card_number' => 'DX-' . str_pad($customer->id, 5, '0', STR_PAD_LEFT),
                'tier' => 'bronze',
            ]);
            $customer->load('loyaltyCard');
        }

        return $customer;
    }
}
