<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseStoreRequest extends FormRequest
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
        $updateId = $this->input('update_id') ?? null;

        return [
            'update_id' => ['nullable', 'exists:purchases,id'],
            'invoice_number' => ['required', Rule::unique('purchases', 'invoice_number')->ignore($updateId)],
            'reference_number' => ['required', Rule::unique('purchases', 'reference_number')->ignore($updateId)],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'date' => ['required'],
            'status' => ['in:received,pending'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['numeric', 'min:1', 'numeric','decimal:0,2', 'max:9999999999.99'],
            'items.*.unit_cost' => ['numeric', 'min:1', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
            'items.*.subtotal_amount' => ['required','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
            'items.*.bonus_qty' => ['nullable', 'min:0','decimal:0,2', 'max:9999999999.99'],
            'notes' => ['nullable'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99'],
            // 'subtotal_amount' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99'],
            'paid_amount' => ['nullable','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
            'remaining_amount' => ['required','numeric', 'min:0','decimal:0,2', 'max:9999999999.99'],
        ];
    }
}
