<?php

declare(strict_types=1);

namespace App\Contracts;

interface PaymentGateway
{
    public function charge(int $amount): string;
}
