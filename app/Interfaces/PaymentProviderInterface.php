<?php

namespace App\Interfaces; // Mude para isso

interface PaymentProviderInterface
{
    public function processPayment(float $amount, array $details): array;
    public function refund(string $transactionId): bool;
}
