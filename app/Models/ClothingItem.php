<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClothingItem extends Model
{
    public const STATUSES = [
        'available',
        'reserved',
        'rented',
        'maintenance',
    ];

    protected $fillable = [
        'clothing_category_id',
        'name',
        'rental_price',
        'color',
        'size',
        'image_path',
        'status',
    ];

    protected $attributes = [
        'status' => 'available',
    ];

    protected function casts(): array
    {
        return [
            'rental_price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClothingCategory::class, 'clothing_category_id');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
