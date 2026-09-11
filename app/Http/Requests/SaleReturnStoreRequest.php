<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaleReturnStoreRequest extends FormRequest
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
            'items' => ['array', 'min:1'],
            'items.*.product_id' => ['required', 'numeric', 'decimal:0,2', 'distinct', 'exists:products,id'],
            'items.*.sale_item_id.*' => ['required', 'numeric', 'decimal:0,2', 'distinct', 'exists:sale_items,id'],
            'items.*.return_qty' => ['required', 'numeric', 'decimal:0,2', 'distinct'],
            'notes' => ['nullable'],
            'sale_id' => ['required', 'numeric', 'decimal:0,2', 'distinct', 'exists:sales,id'],
            'date' => ['required'],
            'refunded_amount'=>['nullable'],
            'total_amount'=>['required'],
        ];
    }
}
