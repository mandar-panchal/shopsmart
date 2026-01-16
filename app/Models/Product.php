<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'stock',
        'is_active',
        'image_path',
        'description'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Accessor for formatted price
    public function getFormattedPriceAttribute()
    {
        return '₹' . number_format($this->price, 2);
    }

    // Accessor for status badge
    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Active</span>' 
            : '<span class="badge bg-danger">Inactive</span>';
    }

    // Accessor for stock status
    public function getStockStatusAttribute()
    {
        if ($this->stock == 0) {
            return '<span class="badge bg-danger">Out of Stock</span>';
        } elseif ($this->stock < 10) {
            return '<span class="badge bg-warning">Low Stock</span>';
        }
        return '<span class="badge bg-success">In Stock</span>';
    }
}