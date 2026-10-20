<?php

namespace App\Http\Requests;

use App\Enums\BlacklistSeverity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlacklistWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === \App\Enums\UserRole::Admin;
    }

    public function rules(): array
    {
        return [
            'word' => ['required', 'string', 'max:255', 'unique:blacklist_words,word'],
            'severity' => ['required', Rule::enum(BlacklistSeverity::class)],
        ];
    }
}
