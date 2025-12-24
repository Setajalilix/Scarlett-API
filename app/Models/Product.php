<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'qty',
        'image',
        'category_id'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function Category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
