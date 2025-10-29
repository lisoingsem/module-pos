<?php

declare(strict_types=1);

namespace Modules\POS\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

final class CheckoutRequest extends FormRequest
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
            'payment_method' => ['required', 'string', 'in:cash,card,mobile_money'],
            'amount' => ['required', 'numeric', 'min:0'],
            'cash_tendered' => ['nullable', 'numeric'],
            'change' => ['nullable', 'numeric'],
            'reference' => ['nullable', 'string'],
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
            'payment_method.required' => __('pos::cashier.validation.payment-method-required'),
            'payment_method.in' => __('pos::cashier.validation.payment-method-invalid'),
            'amount.required' => __('pos::cashier.validation.amount-required'),
            'amount.numeric' => __('pos::cashier.validation.amount-numeric'),
            'amount.min' => __('pos::cashier.validation.amount-min'),
            'cash_tendered.numeric' => __('pos::cashier.validation.cash-tendered-numeric'),
        ];
    }
}
