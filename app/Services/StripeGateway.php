<?php

namespace App\Services;

use App\Contracts\PaymentGateway;

final class StripeGateway implements PaymentGateway
{
    public function charge(int $amount): string
    {
        return "Списано {$amount} рублей через Stripe.";
    }
}
