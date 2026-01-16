<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductView extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that was viewed
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the customer who viewed the product
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}