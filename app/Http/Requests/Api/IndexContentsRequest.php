<?php

namespace App\Http\Requests\Api;

use App\Enums\ContentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexContentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::enum(ContentStatus::class)],
            'channel' => ['nullable', 'string', 'in:web,linkedin,instagram,twitter'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}
