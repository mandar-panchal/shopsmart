<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'customer_id', 'total_amount', 'tax_amount',
        'discount_amount', 'final_amount', 'status', 'payment_status',
        'payment_method', 'shipping_address', 'billing_address', 'notes',
        'paid_at', 'shipped_at', 'delivered_at', 'cancelled_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];
        $class = $badges[$this->status] ?? 'secondary';
        return "<span class='badge bg-{$class}'>" . ucfirst($this->status) . "</span>";
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info'
        ];
        $class = $badges[$this->payment_status] ?? 'secondary';
        return "<span class='badge bg-{$class}'>" . ucfirst($this->payment_status) . "</span>";
    }
}