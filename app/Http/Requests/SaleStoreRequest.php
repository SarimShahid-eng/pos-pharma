<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // dd($this->all());
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $updateId = $this->input('update_id') ?? null;

        return [
            'update_id' => ['nullable', 'exists:sales,id'],
            'invoice_number' => ['required', Rule::unique('sales', 'invoice_number')->ignore($updateId)],
            // 'reference_number' => ['required', Rule::unique('sales', 'reference_number')->ignore($updateId)],
            // 'supplier_id' => ['required', 'exists:suppliers,id'],
            'date' => ['required'],
            'payment_method' => ['in:cash,card,bank'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['numeric', 'min:1', 'numeric','decimal:0,2', 'max:9999999999.99'],
            'items.*.rate' => ['numeric', 'min:1', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
            'items.*.after_discount_price' => ['required','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
            'items.*.amount' => ['required','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
            'notes' => ['nullable'],
            'total_amount' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99'],
            'discount_amount' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99'],
            // 'subtotal_amount' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99'],
            'net_amount' => ['nullable','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
            // 'received_amount' => ['required','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
            // 'change_given' => ['required','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
        ];
    }
}
