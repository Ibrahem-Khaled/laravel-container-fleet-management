<?php

namespace App\Http\Requests\dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustodyAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:open,closed'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ];
    }
}
