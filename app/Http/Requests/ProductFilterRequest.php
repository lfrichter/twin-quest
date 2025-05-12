<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'filter.name' => 'sometimes|string|max:255',
            'filter.category_id' => 'sometimes|integer|exists:categories,id',
            'filter.status' => ['sometimes', 'string', Rule::in(['active', 'inactive', 'discontinued'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'filter.category_id.exists' => 'The selected category is invalid.',
            'filter.status.in' => 'The selected status is invalid.',
        ];
    }
}
