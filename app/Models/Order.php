<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'total_minor'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function Items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
