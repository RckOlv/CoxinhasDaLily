<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'client_name',
        'client_whatsapp',
        'delivery_method',
        'delivery_address',
        'payment_method',
        'total',
        'status',
        'stock_decremented',
        'push_endpoint',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'stock_decremented' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
