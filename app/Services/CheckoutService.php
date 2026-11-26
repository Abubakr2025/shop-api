<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentGateway;

final class CheckoutService
{
    public function __construct(private readonly PaymentGateway $gateway) {}

    public function checkout(int $total): string
    {
        return $this->gateway->charge($total);
    }
}
