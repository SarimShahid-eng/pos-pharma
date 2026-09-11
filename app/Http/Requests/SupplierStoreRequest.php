<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierStoreRequest extends FormRequest
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
            'update_id' => ['nullable', 'exists:suppliers,id'],
            'name' => ['required', Rule::unique('suppliers', 'name')->ignore($updateId)],
            'phone_number' => ['nullable'],
            'opening_balance' => ['required'],
            'date' => 'required',
        ];
    }
}
