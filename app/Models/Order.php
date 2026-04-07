<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'shipping_full_name', 'shipping_phone',
        'shipping_street', 'shipping_city',
        'shipping_country', 'shipping_postal_code',
        'status', 'payment_method', 'payment_status',
        'subtotal', 'shipping_cost', 'discount', 'total',
        'notes',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount'      => 'decimal:2',
        'total'         => 'decimal:2',
    ];

    // ---- Relationships ----
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ---- Helpers ----
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
