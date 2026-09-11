<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
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
        $update_id = $this->input('update_id') ?? null;

        return [
            'update_id' => ['nullable', 'exists:products,id'],
            'barcode' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')->ignore($update_id),
            ],
            'label_title' => ['nullable', 'string', 'max:255'],
            'name'        => [
            'required',
            'string',
            'max:255',
            Rule::unique('products', 'barcode')->ignore($update_id)
            ],
            'unit'        => ['required', 'string', 'in:pcs,pack,kg,ltr'],
            'stock_qty' => ['nullable'],

            // Pricing mechanics
            'cost_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'sale_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'discount'   => ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'lte:sale_price'],
            'profit'     => ['nullable', 'numeric'],

            // Inventory quantity
            // 'stock_qty'  => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ];
    }
}
