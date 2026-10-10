<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFallbackRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
  public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'response' => [
            'required',
            'string',
            'min:1',
            'max:5000',
        ],

        'is_active' => [
            'required',
            'boolean',
        ],
    ];
}
}
