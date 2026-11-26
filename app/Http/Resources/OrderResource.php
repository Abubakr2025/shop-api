<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
final class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total' => $this->total_minor / 100, // в рублях
            'created_at' => $this->created_at?->toIso8601String(),
            // Выводим позиции заказа, если они загружены
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}