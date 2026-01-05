<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributorRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:distributors,name'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'code' => ['required', 'string', 'max:20', 'unique:distributors,code'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:distributors,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:distributors,phone'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'level' => ['required', 'in:epi_store,epi_channel'],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }
}
