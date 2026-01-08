<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    // Mude para true para permitir que usuários autenticados usem a validação
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => 'required|string|in:stripe,paypal', // Apenas esses dois são aceitos
            'amount'   => 'required|numeric|min:1',           // Bloqueia valores negativos ou menores que 1
            'currency' => 'required|string|size:3',           // Exige formato padrão (BRL, USD, etc)
        ];
    }

    public function messages(): array
    {
        return [
            'provider.in' => 'O provedor deve ser stripe ou paypal.',
            'amount.min'  => 'O valor mínimo para transação é 1.00.',
        ];
    }
}
