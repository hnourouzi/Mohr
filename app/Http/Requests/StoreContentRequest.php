<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // کنترل اصلی دسترسی تو auth middleware انجام میشه
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:5', 'max:5000'],
            'channel' => ['nullable', 'string', 'in:web,linkedin,instagram,twitter'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'متن محتوا الزامی است.',
            'body.min' => 'متن باید حداقل ۵ کاراکتر باشد.',
            'body.max' => 'متن نباید بیشتر از ۵۰۰۰ کاراکتر باشد.',
        ];
    }
}