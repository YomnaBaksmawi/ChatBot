<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutoReplyRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keywords' => ['required', 'array', 'min:1'],
            'keywords.*' => ['required', 'string', 'min:1', 'max:100'],

            'response' => ['required', 'string', 'min:1', 'max:5000'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}