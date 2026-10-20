<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class TopUpWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Admin;
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
