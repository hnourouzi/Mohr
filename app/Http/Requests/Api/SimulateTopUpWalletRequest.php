<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SimulateTopUpWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1000', 'max:1000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'حداقل مبلغ شارژ ۱۰۰۰ تومان است.',
            'amount.max' => 'حداکثر مبلغ شارژ ۱,۰۰۰,۰۰۰ تومان است.',
        ];
    }
}
