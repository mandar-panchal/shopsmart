<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'email_verified_at',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the customer's orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the customer's cart items
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the customer's wishlist items
     */
    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the customer's product views
     */
    public function productViews()
    {
        return $this->hasMany(ProductView::class);
    }

    /**
     * Get the customer's OTPs
     */
    public function otps()
    {
        return $this->hasMany(CustomerOtp::class);
    }

    /**
     * Get the latest valid OTP
     */
    public function latestValidOtp()
    {
        return $this->hasOne(CustomerOtp::class)
            ->where('expires_at', '>', now())
            ->where('is_used', false)
            ->latest();
    }

    /**
     * Route notifications for mail channel
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }

    /**
     * Get total amount spent by customer
     */
    public function getTotalSpentAttribute()
    {
        return $this->orders()
            ->where('payment_status', 'paid')
            ->sum('final_amount');
    }

    /**
     * Get total orders count
     */
    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }
}