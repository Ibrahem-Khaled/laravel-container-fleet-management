<?php

namespace App\Http\Requests\container;

use Illuminate\Foundation\Http\FormRequest;

class StoreContainerTransferRequest extends FormRequest
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
            'container_id' => ['required', 'integer', 'exists:containers,id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'note'         => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'container_id.required' => 'يرجى تحديد الحاوية.',
            'container_id.integer'  => 'يجب أن تكون الحاوية رقمًا صحيحًا.',
            'container_id.exists'   => 'الحاوية المحددة غير موجودة.',
            'price.required'        => 'يرجى تحديد السعر.',
            'price.numeric'         => 'يجب أن يكون السعر رقمًا.',
            'price.min'             => 'يجب أن يكون السعر 0 أو أكثر.',
            'note.string'           => 'يجب أن تكون الملاحظة نصًا.',
            'note.max'              => 'يجب أن لا تتجاوز الملاحظة 1000 حرف.',
        ];
    }
}
