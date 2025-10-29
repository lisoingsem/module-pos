<?php

declare(strict_types=1);

namespace Modules\POS\Http\Requests\Terminal;

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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:pos_terminals,code'],
            'location' => ['nullable', 'string', 'max:255'],
            'terminalable_type' => ['nullable', 'string'],
            'terminalable_id' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:active,inactive,maintenance'],
            'settings' => ['nullable', 'array'],
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
            'name.required' => __('pos::terminal.name-required'),
            'code.required' => __('pos::terminal.code-required'),
            'code.unique' => __('pos::terminal.code-unique'),
            'status.required' => __('pos::terminal.status-required'),
            'status.in' => __('pos::terminal.status-invalid'),
        ];
    }
}
