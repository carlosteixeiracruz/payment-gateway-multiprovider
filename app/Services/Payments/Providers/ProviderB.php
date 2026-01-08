<?php

namespace App\Services\Payments\Providers;

use App\Services\Payments\PaymentProviderInterface;

class ProviderB implements PaymentProviderInterface
{
    public function getName(): string { return 'provider_b'; }

    public function process(array $data): array {
        // Simula uma falha de processamento para testar a resiliência
        return [
            'success' => false,
            'message' => 'Erro de comunicação com o Provider B',
            'provider' => $this->getName()
        ];
    }
}