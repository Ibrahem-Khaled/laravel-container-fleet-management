<?php

namespace App\Http\Requests\dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustodyIssueRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount'      => ['required', 'numeric', 'gt:0'],
            'currency'    => ['nullable', 'string', 'size:3'],
            'occurred_at' => ['nullable', 'date'],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'method'      => ['nullable', 'in:cash,bank'], // جديد
        ];
    }
}
