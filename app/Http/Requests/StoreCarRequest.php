<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // يجب أن يكون المستخدم مسجل للدخول
    }

    public function rules()
    {
        return [
            'driver_id' => 'nullable|unique:cars,driver_id|exists:users,id',
            'type' => 'required|in:transfer,private',
            'type_car' => 'nullable|string|max:255',
            'model_car' => 'nullable|integer|digits_between:4,4',
            'serial_number' => 'required|integer|unique:cars,serial_number',
            'license_expire' => 'nullable|date',
            'scan_expire' => 'nullable|date',
            'card_run_expire' => 'nullable|date',
            'number' => 'required|string|unique:cars,number',
            'insurance_expire' => 'nullable|date',
            'oil_change_number' => 'nullable|integer',
        ];
    }
}
