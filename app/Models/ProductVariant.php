<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'size_id', 'color_id',
        'sku', 'price_override', 'stock_quantity', 'is_active',
    ];

    protected $casts = [
        'price_override'  => 'decimal:2',
        'stock_quantity'  => 'integer',
        'is_active'       => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    // Resolved price — override wins, otherwise product base
    public function getEffectivePriceAttribute(): string
    {
        return $this->price_override ?? $this->product->base_price;
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}