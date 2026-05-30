<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClothingCategory extends Model
{
    protected $fillable = [
        'name',
    ];

    public function clothingItems(): HasMany
    {
        return $this->hasMany(ClothingItem::class);
    }
}
