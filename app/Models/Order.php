<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{

    protected $fillable = [
        'user_id',
        'product_id',
        'count',
        'total',
        'count',
        'status',
        'discount',
        'order_number',
    ];

    public function products()
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
