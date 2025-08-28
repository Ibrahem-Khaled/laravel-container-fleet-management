<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Tip;

class ContainerFlowChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $container = $this->route('container');
        $current   = $container?->status;

        // خريطة الانتقالات المسموح بها
        $allowedTransitions = [
            'wait'      => ['transport'],
            'transport' => ['storage', 'done'],
            'storage'   => ['wait'],
            'done'      => ['wait'],
            'rent'      => ['wait'], // إن وُجدت حالة الإيجار
        ];
        $allowed = $allowedTransitions[$current] ?? ['transport'];

        $rules = [
            'new_status' => ['required', Rule::in($allowed)],
        ];

        // لو الهدف ليس "انتظار" نطلب بيانات الـ Tip ونمنع التكرار
        if ($this->input('new_status') !== 'wait') {
            $driver_id    = $this->input('driver_id');
            $car_id       = $this->input('car_id');

            $rules += [
                'driver_id' => ['required', 'exists:users,id'],
                'car_id'    => ['required', 'exists:cars,id'],
                'price'     => ['nullable', 'integer', 'min:0'],
                'container_id' => [
                    'nullable',
                    Rule::unique('tips', 'container_id')
                        ->where(fn($q) => $q->where('driver_id', $driver_id)
                            ->where('car_id', $car_id)
                            ->whereNull('deleted_at')),
                ],
            ];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        // سعر افتراضي = 20 عند إنشاء Tip
        if ($this->input('new_status') !== 'wait' && !$this->filled('price')) {
            $this->merge(['price' => 20]);
        }
    }

    public function messages(): array
    {
        return [
            'container_id.unique' => 'لا يمكن تكرار نفس التركيبة (حاوية + سائق + سيارة) لرحلة فعّالة.',
        ];
    }
}
