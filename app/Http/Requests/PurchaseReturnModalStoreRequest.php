<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseReturnStoreRequest extends FormRequest
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
        return [
            'items' => ['array', 'min:1'],
            'items.*.product_id' => ['required', 'numeric', 'decimal:0,2', 'distinct', 'exists:products,id'],
            'items.*.purchase_item_id.*' => ['required', 'numeric', 'decimal:0,2', 'distinct', 'exists:purchase_items,id'],
            'items.*.return_qty' => ['required', 'numeric', 'decimal:0,2', 'distinct'],
            'reason' => ['nullable'],
            'purchase_id' => ['required', 'numeric', 'decimal:0,2', 'distinct', 'exists:purchases,id'],
            'date' => ['required'],
        ];
    }
}
