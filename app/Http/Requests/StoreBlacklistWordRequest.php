<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlacklistWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'word' => ['required', 'string', 'max:255', 'unique:blacklist_words,word'],
            'severity' => ['required', 'string', 'in:block,flag'],
        ];
    }
}