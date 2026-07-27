<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image_path',
        'is_active',
        'stock_quantity',
        'badge',
        'units_per_package',
        'is_event',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
        'units_per_package' => 'integer',
        'is_event' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_products', 'product_id', 'event_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}
