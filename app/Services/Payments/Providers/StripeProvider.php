<?php

namespace App\Services\Payments\Providers;

use App\Interfaces\PaymentProviderInterface;

class StripeProvider implements PaymentProviderInterface
{
    public function processPayment(float $amount, array $details): array
    {
        return [
            'status' => 'success',
            'transaction_id' => 'ch_' . bin2hex(random_bytes(10)),
            'provider' => 'stripe',
            'amount' => $amount,
            'currency' => $details['currency'] ?? 'BRL', // Pega do request ou define BRL
            'message' => 'Pagamento simulado com sucesso!'
        ];
    }

    public function refund(string $transactionId): bool
    {
        return true;
    }
}
