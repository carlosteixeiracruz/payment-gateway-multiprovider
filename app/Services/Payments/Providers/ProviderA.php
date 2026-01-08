<?php

namespace App\Services\Payments\Providers;

use App\Services\Payments\PaymentProviderInterface;

class ProviderA implements PaymentProviderInterface
{
    public function getName(): string
    {
        return 'stripe';
    }

    public function pay(array $data): array
    {
        return [
            'success' => true,
            'transaction_id' => 'ST-' . uniqid(),
            'provider' => $this->getName()
        ];
    }
}
