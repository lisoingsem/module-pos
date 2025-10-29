<?php

declare(strict_types=1);

namespace Modules\POS\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRequest extends FormRequest
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
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'shift_id' => ['nullable', 'integer', 'exists:pos_shifts,id'],
            'order_discount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            // Note: unit_price and discount are NOT accepted from frontend for security
            // Prices are fetched from database to prevent price manipulation
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
            'customer_id.exists' => __('pos::cashier.validation.customer-not-found'),
            'shift_id.exists' => __('pos::cashier.validation.shift-not-found'),
            'order_discount.numeric' => __('pos::cashier.validation.order-discount-numeric'),
            'order_discount.min' => __('pos::cashier.validation.order-discount-min'),
            'items.required' => __('pos::cashier.validation.items-required'),
            'items.min' => __('pos::cashier.validation.items-min'),
            'items.*.product_id.required' => __('pos::cashier.validation.product-id-required'),
            'items.*.product_id.exists' => __('pos::cashier.validation.product-not-found'),
            'items.*.quantity.required' => __('pos::cashier.validation.quantity-required'),
            'items.*.quantity.min' => __('pos::cashier.validation.quantity-min'),
        ];
    }
}
