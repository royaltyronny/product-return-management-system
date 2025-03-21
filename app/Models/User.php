<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'address' => 'array',
    ];
    
    /**
     * User role constants
     */
    const ROLE_CUSTOMER = 'customer';
    const ROLE_SUPPORT_AGENT = 'support_agent';
    const ROLE_WAREHOUSE_STAFF = 'warehouse_staff';
    const ROLE_FINANCE = 'finance';
    const ROLE_ADMIN = 'admin';
    
    /**
     * Get the orders for the user
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get the return requests for the user
     */
    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }
    
    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
    
    /**
     * Check if user is a support agent
     */
    public function isSupportAgent(): bool
    {
        return $this->role === self::ROLE_SUPPORT_AGENT;
    }
    
    /**
     * Check if user is warehouse staff
     */
    public function isWarehouseStaff(): bool
    {
        return $this->role === self::ROLE_WAREHOUSE_STAFF;
    }
    
    /**
     * Check if user is finance team
     */
    public function isFinance(): bool
    {
        return $this->role === self::ROLE_FINANCE;
    }
    
    /**
     * Check if user is a customer
     */
    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }
}