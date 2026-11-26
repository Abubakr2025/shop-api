<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'qty', 'price_minor'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
