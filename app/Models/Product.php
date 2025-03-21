<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'sku',
        'price',
        'image_url',
        'stock_quantity',
        'can_be_returned',
        'return_period_days',
        'supplier_id',
        'warehouse_location',
    ];

    protected $casts = [
        'price' => 'float',
        'stock_quantity' => 'integer',
        'can_be_returned' => 'boolean',
        'return_period_days' => 'integer',
    ];

    /**
     * Get the return requests for the product
     */
    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }
}
