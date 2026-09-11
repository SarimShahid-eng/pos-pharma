<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SupplierPaymentStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'update_id' => ['nullable', 'exists:suppliers,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'date' => ['required'],
            'amount' => ['required', 'min:0', 'numeric', 'decimal:0,2', 'max:9999999999.99'],
        ];
    }
}
