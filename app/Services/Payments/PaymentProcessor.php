<?php

namespace App\Services\Payments;

use App\Interfaces\PaymentProviderInterface;
use Exception;

class PaymentProcessor
{
    protected array $providers = [];

    public function addProvider(string $name, PaymentProviderInterface $provider)
    {
        $this->providers[$name] = $provider;
    }

    public function execute(string $providerName, array $data)
    {
        if (!isset($this->providers[$providerName])) {
            throw new Exception("Provider [$providerName] not found.");
        }

        return $this->providers[$providerName]->processPayment($data['amount'], $data);
    }
}