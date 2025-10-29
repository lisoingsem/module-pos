<?php

declare(strict_types=1);

namespace Modules\POS\Http\Requests\Shift;

use Illuminate\Foundation\Http\FormRequest;

final class CloseShiftRequest extends FormRequest
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
            'actual_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'actual_cash.required' => __('pos::shift.actual-cash-required'),
            'actual_cash.min' => __('pos::shift.actual-cash-min'),
        ];
    }
}
