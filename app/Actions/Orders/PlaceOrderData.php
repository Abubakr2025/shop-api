<?php

declare(strict_types=1);

namespace App\Actions\Orders;

final readonly class PlaceOrderData
{
    /**
     * Create a new class instance.
     */
    public function __construct(public int $userId, public array $items) {}
}
