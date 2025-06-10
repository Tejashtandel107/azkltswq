<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CustomerOrdersRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'sr_no' => 'required',
            'customer_id' => 'required',
            'date' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'sr_no.required' => 'The Serial No. field is required',
            'customer_id.required' => 'The Customer field is required',
            'date.required' => 'The Date field is required',
        ];
    }
}
