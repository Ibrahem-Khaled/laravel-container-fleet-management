<?php

namespace App\Http\Requests\dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreLedgerEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'direction' => ['required', 'in:issue,return,expense,income,adjustment,transfer_out,transfer_in'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'occurred_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'counterparty_user_id' => ['nullable', 'exists:users,id'],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id'   => ['nullable', 'integer'],
        ];
    }
}
