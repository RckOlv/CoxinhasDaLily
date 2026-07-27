<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $fillable = [
        'client_name',
        'client_whatsapp',
        'event_date',
        'quantity',
        'pickup_time',
        'event_type',
        'color',
        'notes',
        'status',
        'total',
        'deposit_paid',
    ];

    protected $casts = [
        'event_date' => 'date',
        'quantity' => 'integer',
        'total' => 'decimal:2',
        'deposit_paid' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'event_products', 'event_id', 'product_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
