<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DomainStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('domains')->where('user_id', $this->user()->id),
            ],
            'check_interval' => ['required', 'integer', Rule::in([1, 5, 10, 15, 30, 60])],
            'check_timeout' => ['required', 'integer', 'min:1', 'max:60'],
            'check_method' => ['required', 'string', Rule::in(['GET', 'HEAD'])],
        ];
    }
}
