<?php

namespace App\Services\Payments;

interface PaymentProviderInterface
{
    public function pay(array $data): array;
    public function getName(): string;
}
