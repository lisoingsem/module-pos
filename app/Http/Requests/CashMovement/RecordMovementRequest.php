<?php

declare(strict_types=1);

namespace Modules\POS\Http\Requests\CashMovement;

use Illuminate\Foundation\Http\FormRequest;
use Modules\POS\Enums\CashMovementTypeEnum;

final class RecordMovementRequest extends FormRequest
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
            'shift_id' => ['required', 'integer', 'exists:pos_shifts,id'],
            'type' => ['required', 'string', 'in:' . implode(',', CashMovementTypeEnum::values())],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'string', 'in:cash,card,mobile_money,bank_transfer,other'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'reference' => ['nullable', 'string', 'max:255'],
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
            'shift_id.required' => __('pos::cash.shift-required'),
            'shift_id.exists' => __('pos::cash.shift-invalid'),
            'type.required' => __('pos::cash.type-required'),
            'amount.required' => __('pos::cash.amount-required'),
            'amount.min' => __('pos::cash.amount-min'),
            'reason.required' => __('pos::cash.reason-required'),
        ];
    }
}
