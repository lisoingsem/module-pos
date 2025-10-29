<?php

declare(strict_types=1);

namespace Modules\POS\Http\Requests\Printer;

use Illuminate\Foundation\Http\FormRequest;
use Modules\POS\Enums\PrinterTypeEnum;

final class UpdateRequest extends FormRequest
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
            'terminal_id' => ['required', 'integer', 'exists:pos_terminals,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:' . implode(',', PrinterTypeEnum::values())],
            'connection_type' => ['required', 'string', 'in:network,usb,bluetooth'],
            'ip_address' => ['nullable', 'ip'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'device_path' => ['nullable', 'string', 'max:255'],
            'paper_width' => ['nullable', 'integer', 'in:58,80'],
            'is_active' => ['boolean'],
        ];
    }
}
