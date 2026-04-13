<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, Sluggable;
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'stock',
        'sku',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'sale_price'  => 'decimal:2',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ---- Relationships ----
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ---- Helpers ----
    protected function currentPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->sale_price ?? $this->price,
        );
    }

    protected function isOnSale(): Attribute
    {
        return Attribute::make(
            get: fn() => !is_null($this->sale_price) && $this->sale_price < $this->price,
        );
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
    // helper to get only the primary image
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'name']  // auto generates from name
        ];
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
