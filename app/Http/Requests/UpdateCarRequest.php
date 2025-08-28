<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $carId = $this->route('car')->id;
        return [
            'driver_id' => ['nullable', Rule::unique('cars')->ignore($carId), 'exists:users,id'],
            'type' => 'required|in:transfer,private',
            'type_car' => 'nullable|string|max:255',
            'model_car' => 'nullable|integer|digits_between:4,4',
            'serial_number' => ['required', 'integer', Rule::unique('cars')->ignore($carId)],
            'license_expire' => 'nullable|date',
            'scan_expire' => 'nullable|date',
            'card_run_expire' => 'nullable|date',
            'number' => ['required', 'string', Rule::unique('cars')->ignore($carId)],
            'insurance_expire' => 'nullable|date',
            'oil_change_number' => 'nullable|integer',
        ];
    }
}
