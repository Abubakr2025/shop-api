<?php

declare(strict_types=1);

namespace App;

final class PaymentGateway
{
    public function processPayment(int $amount): string
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Сумма должна быть больше нуля');
        }

        return "Оплата на сумму {$amount} успешно проведена!";
    }
}
