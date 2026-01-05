<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($this->product)],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:20'],
            'price' => ['required', 'numeric', 'min:0'],
            'member_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive,draft'],
            'price_change_reason' => ['nullable', 'string', 'required_if:price_changed,true'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('price') && $this->product && $this->price != $this->product->price) {
            $this->merge(['price_changed' => true]);
        }
    }
}
