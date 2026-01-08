<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Payments\PaymentProcessor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Http\Requests\PurchaseRequest;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentProcessor $paymentProcessor
    ) {}

    /**
     * [NOVO] Lista o histórico de transações do usuário logado
     */
    public function index()
    {
        // Pega as transações do usuário logado, ordenando pelas mais recentes
        $transactions = Transaction::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    /**
     * Cria uma tentativa de pagamento.
     */
    public function purchase(PurchaseRequest $request) // <-- Trocamos Request por PurchaseRequest
    {
        // Se o código chegar aqui, significa que os dados já foram validados!
        try {
            $result = $this->paymentProcessor->execute(
                $request->input('provider'),
                $request->all()
            );

            $transaction = \App\Models\Transaction::create([
                'user_id'     => auth()->id(),
                'amount'      => $request->amount,
                'currency'    => $request->currency,
                'provider'    => $request->provider,
                'provider_id' => $result['transaction_id'],
                'status'      => $result['status'] ?? 'pending',
            ]);

            return response()->json([
                'message' => 'Pagamento iniciado com sucesso',
                'data'    => $transaction
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Webhook do provider com Idempotência e atualização de status.
     */
    public function webhook(Request $request, $provider)
    {
        $eventId = $request->input('event_id');
        $externalId = $request->input('transaction_id'); // ID que o provider enviou
        $newStatus = $request->input('status'); // Ex: 'paid', 'failed'

        if (!$eventId) {
            return response()->json(['error' => 'event_id is required'], 400);
        }

        // 1. Idempotência: Checa se já processamos este evento
        if (Cache::has("webhook_processed_{$eventId}")) {
            return response()->json(['message' => 'Event already processed'], 200);
        }

        // 2. Lógica de Negócio: Atualizar a transação no banco
        $transaction = Transaction::where('provider_id', $externalId)->first();

        if ($transaction) {
            $transaction->update(['status' => $newStatus]);
            Log::info("Pagamento atualizado via Webhook: {$externalId} para {$newStatus}");
        } else {
            Log::warning("Webhook recebido para transação não encontrada: {$externalId}");
        }

        // 3. Marca no Cache
        Cache::put("webhook_processed_{$eventId}", true, now()->addDay());

        return response()->json(['status' => 'success'], 200);
    }
}
